<?php

namespace App\Services;

use App\Facades\Notifications;
use App\Models\ImageUpload;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Intervention\Image\Facades\Image;

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
            ]);

            // Save image before doing any processing
            $this->handleImage($data['image'], $image->imagePath, $image->imageFileName);
            Image::make($image->imagePath.'/'.$image->imageFileName)->save($image->imagePath.'/'.$image->imageFileName, null, 'webp');

            // Process and save thumbnail from the image
            $thumbnail = Image::make($image->imagePath.'/'.$image->imageFileName)
                ->resize(null, config('alcyone.settings.thumbnail_height'), function ($constraint) {
                    $constraint->aspectRatio();
                    $constraint->upsize();
                })
                ->save($image->thumbnailPath.'/'.$image->thumbnailFileName, null, 'webp');

            return $this->commitReturn($image);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Deletes an uploaded image.
     *
     * @param \App\Models\ImageUpload $image
     * @param \App\Models\User\User   $user
     *
     * @return bool
     */
    public function deleteImage($image, $user) {
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
     * @param \App\Models\ImageUpload $image
     * @param bool                    $create
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
