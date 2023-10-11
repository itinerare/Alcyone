<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report;
use Illuminate\Http\Request;

class ReportController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Admin Controller
    |--------------------------------------------------------------------------
    |
    | Handles general admin requests.
    |
    */

    /**
     * Show the reports queue.
     *
     * @param mixed      $class
     * @param mixed|null $status
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getReportIndex(Request $request, $class, $status = null) {
        $reports = Report::where('status', $status ? ucfirst($status) : 'Pending');

        $data = $request->only(['sort']);
        if (isset($data['sort'])) {
            switch ($data['sort']) {
                case 'newest':
                    $reports->orderBy('created_at', 'DESC');
                    break;
                case 'oldest':
                    $reports->orderBy('created_at', 'ASC');
                    break;
            }
        } else {
            $reports->orderBy('created_at');
        }

        return view('admin.reports.index', [
            'reports' => $reports->paginate(30)->appends($request->query()),
        ]);
    }
}
