<?php

namespace App\Http\Controllers;

use App\Models\Report\Report;
use App\Services\ReportManager;
use Illuminate\Http\Request;

class ReportController extends Controller {
    /*
    |--------------------------------------------------------------------------
    | Report Controller
    |--------------------------------------------------------------------------
    |
    | Handles reports.
    |
    */

    /**
     * Shows the new report view.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getCreateReport() {
        return view('reports.create_report');
    }

    /**
     * Displays a report.
     *
     * @param string $key
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getReport($key) {
        $report = Report::where('key', $key)->first();
        if (!$report) {
            abort(404);
        }

        return view('reports.report', [
            'report' => $report,
        ]);
    }

    /**
     * Creates a report.
     *
     * @param App\Services\ReportManager $service
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postCreateReport(Request $request, ReportManager $service) {
        $request->validate(Report::$createRules);
        $data = $request->only([
            'image_url', 'reason', 'email',
        ]);
        $data['ip'] = $request->ip();

        if ($report = $service->createReport($data)) {
            flash('Report submitted successfully.')->success();

            return redirect()->to($report->url);
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                $service->addError($error);
            }
        }

        return redirect()->back();
    }
}
