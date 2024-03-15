<?php

namespace App\Http\Controllers;

use App\Models\SitePage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class IndexController extends Controller {
    /**
     * Show the home page.
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
     * Show the terms of service page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getTerms() {
        $page = SitePage::where('key', 'terms')->first();
        if (!$page) {
            abort(404);
        }

        return view('text_page', [
            'page' => $page,
        ]);
    }

    /**
     * Show the privacy policy page.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getPrivacyPolicy() {
        $page = SitePage::where('key', 'privacy')->first();
        if (!$page) {
            abort(404);
        }

        return view('text_page', [
            'page' => $page,
        ]);
    }
}
