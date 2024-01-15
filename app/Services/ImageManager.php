<?php

namespace App\Services;

use App\Facades\Notifications;
use App\Models\ImageUpload;
use App\Models\Report\Report;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Drivers\Gd\Driver as GdDriver;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Encoders\GifEncoder;
use Intervention\Image\Encoders\WebpEncoder;
use Intervention\Image\ImageManager as InterventionImageManager;

class ImageManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Image Manager
    |--------------------------------------------------------------------------
    |
    | Handles creation and modification of uploaded images.
    |
    */

    /**
     * Processes an image upload.
     *
     * @param array                 $data
     * @param \App\Models\User\User $user
     *
     * @return bool
     */
    public function uploadImage($data, $user) {
        DB::beginTransaction();

        try {
            $image = ImageUpload::create([
                'user_id' => $user->id,
                'key'     => randomString(15),
                'is_gif'  => $data['image']->getClientOriginalExtension() == 'gif' ?? 0,
            ]);

            // Save image before doing any processing
            $this->handleImage($data['image'], $image->imagePath, $image->imageFileName);

            $imageProperties = getimagesize($image->imagePath.'/'.$image->imageFileName);

            if (filesize($image->imagePath.'/'.$image->imageFileName) > '8000000' && $imageProperties['mime'] == 'image/gif') {
                // Handling for GIFs specifically, since they are not converted
                // so as to preserve any animation
                throw new \Exception('GIFs may only be up to 8MB in size.');
            }

            if ($imageProperties[0] > 2000 || $imageProperties[1] > 2000) {
                // For large images (in terms of dimensions),
                // use imagick instead, as it's better at handling them
                $manager = new InterventionImageManager(ImagickDriver::class);
                // This no longer is needed but is useful for testing
                Config::set('image.driver', 'imagick');
            } else {
                $manager = new InterventionImageManager(GdDriver::class);
            }

            // Convert image if necessary, preserving quality as best possible
            if ($imageProperties['mime'] != 'image/webp' && !$image->is_gif) {
                $manager->read($image->imagePath.'/'.$image->imageFileName)
                    ->toWebp(90)
                    ->save($image->imagePath.'/'.$image->imageFileName);
            }

            // Process and save thumbnail from the image
            $manager->read($image->imagePath.'/'.$image->imageFileName)
                ->scaleDown(height: config('alcyone.settings.thumbnail_height'))
                ->encode($image->is_gif ? (new GifEncoder()) : (new WebpEncoder(90)))
                ->save($image->thumbnailPath.'/'.$image->thumbnailFileName);

            return $this->commitReturn($image);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Converts an image upload.
     *
     * @param ImageUpload $image
     *
     * @return bool
     */
    public function convertImage($image) {
        DB::beginTransaction();

        try {
            // If there's no cached copy of the converted image, create one
            if (!file_exists($image->convertedPath.'/'.$image->convertedFileName)) {
                if (!file_exists($image->convertedPath)) {
                    // Create the directory.
                    if (!mkdir($image->convertedPath, 0755, true)) {
                        $this->setError('error', 'Failed to create image directory.');

                        return false;
                    }
                    chmod($image->convertedPath, 0755);
                }

                $imageProperties = getimagesize($image->imagePath.'/'.$image->imageFileName);
                if ($imageProperties[0] > 2000 || $imageProperties[1] > 2000) {
                    // For large images (in terms of dimensions),
                    // use imagick instead, as it's better at handling them
                    $manager = new InterventionImageManager(ImagickDriver::class);
                    // This no longer is needed but is useful for testing
                    Config::set('image.driver', 'imagick');
                } else {
                    $manager = new InterventionImageManager(GdDriver::class);
                }

                $manager->read($image->imagePath.'/'.$image->imageFileName)
                    ->toPng(90)
                    ->save($image->convertedPath.'/'.$image->convertedFileName);

                // Save the expiry time for the cached image
                $image->update([
                    'cache_expiry' => Carbon::now()->addHours(config('alcyone.settings.cache_lifetime')),
                ]);
            }

            return $this->commitReturn($image);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes an uploaded image.
     *
     * @param ImageUpload           $image
     * @param \App\Models\User\User $user
     * @param bool                  $reportAction
     *
     * @return bool
     */
    public function deleteImage($image, $user, $reportAction = false) {
        DB::beginTransaction();

        try {
            if ($image->user_id != $user->id && !$user->isMod) {
                throw new \Exception('Invalid image selected.');
            }

            // If the acting user is staff, send a notification to the image's uploader
            if ($user->id != $image->user_id) {
                Notifications::create('UPLOAD_REMOVED', $image->user, [
                    'slug' => $image->slug,
                ]);
            }

            // First, remove the image files, including the cached PNG if it exists
            unlink($image->imagePath.'/'.$image->imageFileName);
            unlink($image->imagePath.'/'.$image->thumbnailFileName);
            if (file_exists($image->convertedPath.'/'.$image->convertedFileName)) {
                unlink($image->convertedPath.'/'.$image->convertedFileName);
            }

            // If reports with this image exist, and it is not being deleted due to them,
            // cancel the relevant report(s)
            if (!$reportAction && Report::where('image_upload_id', $image->id)->exists()) {
                Report::where('image_upload_id', $image->id)->update([
                    'status'         => 'Cancelled',
                    'staff_comments' => '<p>Automatically cancelled due to image deletion.</p>',
                ]);
            }

            // Then, delete the image object itself
            $image->delete();

            return $this->commitReturn(true);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Generates and saves test images for page image test purposes.
     *
     * @param ImageUpload $image
     * @param bool        $create
     *
     * @return bool
     */
    public function testImages($image, $create = true) {
        if ($create) {
            // Generate the fake files to save
            $file['image'] = UploadedFile::fake()->image('test_image.png');
            $file['thumbnail'] = UploadedFile::fake()->image('test_thumb.png');

            // Save the files in line with usual image handling.
            $this->handleImage($file['image'], $image->imagePath, $image->imageFileName);
            $this->handleImage($file['thumbnail'], $image->imagePath, $image->thumbnailFileName);
        } elseif (!$create && File::exists($image->imagePath.'/'.$image->thumbnailFileName)) {
            // Remove test files
            unlink($image->imagePath.'/'.$image->thumbnailFileName);
            unlink($image->imagePath.'/'.$image->imageFileName);
            if (file_exists($image->convertedPath.'/'.$image->convertedFileName)) {
                unlink($image->convertedPath.'/'.$image->convertedFileName);
            }
        }

        return true;
    }
}
