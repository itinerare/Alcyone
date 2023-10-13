<?php

namespace App\Http\Controllers;

use App\Models\ImageUpload;
use App\Services\ImageManager;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Auth;
use Intervention\Image\Facades\Image;

class Controller extends BaseController {
    use AuthorizesRequests, ValidatesRequests;

    /**
     * Show the subscription page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getIndex(Request $request) {
        $query = Auth::user()->images()->orderBy('created_at', 'DESC');

        return view('index', [
            'images' => $query->paginate(20)->appends($request->query()),
        ]);
    }

    /**
     * Shows the modal for a given image.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getImage($slug) {
        $image = ImageUpload::get()->where('slug', $slug)->first();
        if (!$image || (Auth::user()->id != $image->user_id && !Auth::user()->isMod)) {
            abort(404);
        }

        return view('images._info_popup', [
            'image' => $image,
        ]);
    }

    /**
     * Show an image in PNG format.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getConvertedImage($slug) {
        $image = ImageUpload::get()->where('slug', $slug)->first();
        if (!$image) {
            abort(404);
        }

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

            $file = Image::make($image->imagePath.'/'.$image->imageFileName)
                ->save($image->convertedPath.'/'.$image->convertedFileName, null, 'png');

            // Save the expiry time for the cached image
            $image->update([
                'cache_expiry' => Carbon::now()->addHours(config('alcyone.settings.cache_lifetime')),
            ]);
        }

        return response()->file($image->convertedPath.'/'.$image->convertedFileName);
    }

    /**
     * Uploads an image.
     *
     * @param App\Services\ImageManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postUploadImage(Request $request, ImageManager $service) {
        $request->validate(ImageUpload::$createRules);
        if ($service->uploadImage($request->only(['image']), Auth::user())) {
            flash('Image uploaded successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                $service->addError($error);
            }
        }

        return redirect()->back();
    }

    /**
     * Deletes an image.
     *
     * @param App\Services\ImageManager $service
     * @param mixed                     $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postDeleteImage(Request $request, $slug, ImageManager $service) {
        $image = ImageUpload::get()->where('slug', $slug)->first();
        if (!$image) {
            abort(404);
        }

        if ($service->deleteImage($image, Auth::user())) {
            flash('Image deleted successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                $service->addError($error);
            }
        }

        return redirect()->back();
    }
}
