<?php

namespace App\Http\Controllers;

use App\Models\ImageUpload;
use App\Services\ImageManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ImageController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Image Controller
    |--------------------------------------------------------------------------
    |
    | Handles uploaded images.
    |
    */

    /**
     * Shows the modal for a given image.
     *
     * @param string $slug
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getImage($slug) {
        $image = ImageUpload::get()->where('slug', $slug)->first();
        if (!$image || (Auth::user()->id != $image->user_id)) {
            abort(404);
        }

        return view('images._info_popup', [
            'image' => $image,
        ]);
    }

    /**
     * Show an image in PNG format.
     *
     * @param string                    $slug
     * @param App\Services\ImageManager $service
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getConvertedImage($slug, ImageManager $service) {
        $image = ImageUpload::get()->where('slug', $slug)->first();
        if (!$image) {
            abort(404);
        }

        if (!file_exists($image->convertedPath.'/'.$image->convertedFileName)) {
            $service->convertImage($image);
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
        if (!$image || (Auth::user()->id != $image->user_id)) {
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
