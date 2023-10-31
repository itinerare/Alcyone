<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Report\Report;
use App\Services\ReportManager;
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
     * @param mixed|null $status
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getReportIndex(Request $request, $status = null) {
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

    /**
     * Displays a report.
     *
     * @param int $id
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function getReport($id) {
        $report = Report::where('id', $id)->first();
        if (!$report) {
            abort(404);
        }

        return view('admin.reports.report', [
            'report' => $report,
        ]);
    }

    /**
     * Acts on a report.
     *
     * @param int    $id
     * @param string $action
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postReport($id, $action, Request $request, ReportManager $service) {
        $report = Report::findOrFail($id);

        switch ($action) {
            default:
                flash('Invalid action selected.')->error();
                break;
            case 'accept':
                return $this->postAcceptReport($report, $request->only(['staff_comments']), $request, $service);
                break;
            case 'cancel':
                return $this->postCancelReport($report, $request->only(['staff_comments']), $request, $service);
                break;
            case 'ban':
                return $this->postBanReporter($report, $request->only(['staff_comments']), $request, $service);
                break;
        }

        return redirect()->back();
    }

    /**
     * Accepts a report, deleting the reported image.
     *
     * @param \App\Models\Report\Report $report
     * @param array                     $data
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postAcceptReport($report, $data, Request $request, ReportManager $service) {
        if ($service->acceptReport($report, $data, $request->user())) {
            flash('Report processed successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                $service->addError($error);
            }
        }

        return redirect()->back();
    }

    /**
     * Cancels a report.
     *
     * @param \App\Models\Report\Report $report
     * @param array                     $data
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postCancelReport($report, $data, Request $request, ReportManager $service) {
        if ($service->cancelReport($report, $data, $request->user())) {
            flash('Report cancelled successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                $service->addError($error);
            }
        }

        return redirect()->back();
    }

    /**
     * Bans a reporter.
     *
     * @param \App\Models\Report\Report $report
     * @param array                     $data
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    private function postBanReporter($report, $data, Request $request, ReportManager $service) {
        if ($service->banReporter($report, $data, $request->user())) {
            flash('Reporter banned successfully.')->success();
        } else {
            foreach ($service->errors()->getMessages()['error'] as $error) {
                $service->addError($error);
            }
        }

        return redirect()->back();
    }
}
