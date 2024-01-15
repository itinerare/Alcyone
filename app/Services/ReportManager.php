<?php

namespace App\Services;

use App\Mail\ReportAccepted;
use App\Mail\ReportCancelled;
use App\Mail\ReportSubmitted;
use App\Models\ImageUpload;
use App\Models\Report\Report;
use App\Models\Report\Reporter;
use App\Models\User\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class ReportManager extends Service {
    /*
    |--------------------------------------------------------------------------
    | Report Manager
    |--------------------------------------------------------------------------
    |
    | Handles creation and processing of reports.
    |
    */

    /**
     * Creates a report.
     *
     * @param array $data
     *
     * @return bool
     */
    public function createReport($data) {
        DB::beginTransaction();

        try {
            if (Reporter::where('ip', $data['ip'])->where('is_banned', 1)->exists()) {
                throw new \Exception('You cannot submit reports.');
            }

            // Try to determine the relevant image
            $matches = [];
            preg_match('/images\/uploads\/[0-9]+\/([a-zA-Z0-9]+).webp/', $data['image_url'], $matches);
            if (!isset($matches[1])) {
                // If no match for the direct URL, check the converted URL
                preg_match('/images\/converted\/([a-zA-Z0-9]+)/', $data['image_url'], $matches);
            }
            if (isset($matches[1])) {
                $image = ImageUpload::all()->where('slug', $matches[1])->first();
            }
            if (!isset($matches[1]) || !$image) {
                throw new \Exception('Invalid image.');
            }

            // Update or create reporter record based on IP address
            $reporter = Reporter::updateOrCreate(
                ['ip' => $data['ip']],
                ['email' => $data['email'] ?? null],
            );

            // Prevent submitting duplicate reports
            if (Report::where('image_upload_id', $image->id)->where('reporter_id', $reporter->id)->where('updated_at', '>', Carbon::now()->subMonths(3))->exists()) {
                throw new \Exception('You have already reported this image recently.');
            }

            // Create report and set key, ensuring uniqueness
            $report = Report::create([
                'image_upload_id' => $image->id,
                'reporter_id'     => $reporter->id,
                'key'             => randomString(15),
                'reason'          => $data['reason'],
            ]);
            $report->key = $report->id.$report->key;
            $report->save();

            // Send an email notification to relevant users
            foreach (User::where('receive_admin_notifs', 1)->get() as $user) {
                if ($user->isMod) {
                    Mail::to($user->email)->send(new ReportSubmitted($report));
                }
            }

            return $this->commitReturn($report);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Accepts a report.
     *
     * @param Report $report
     * @param array  $data
     * @param User   $user
     *
     * @return bool
     */
    public function acceptReport($report, $data, $user) {
        DB::beginTransaction();

        try {
            if ($report->status != 'Pending') {
                throw new \Exception('This report has already been processed.');
            }

            // Process the report
            $report->update([
                'status'         => 'Accepted',
                'staff_id'       => $user->id,
                'staff_comments' => $data['staff_comments'] ?? null,
            ]);

            if (!$report->image->deleted_at) {
                // Delete the offending image
                (new ImageManager)->deleteImage($report->image, $user, true);
            }

            // If the reporter has an email address on file, send a notification email
            if (isset($report->reporter->email)) {
                Mail::to($report->reporter->email)->send(new ReportAccepted($report));
            }

            return $this->commitReturn($report);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Cancels a report.
     *
     * @param Report $report
     * @param array  $data
     * @param User   $user
     *
     * @return bool
     */
    public function cancelReport($report, $data, $user) {
        DB::beginTransaction();

        try {
            if ($report->status != 'Pending') {
                throw new \Exception('This report has already been processed.');
            }

            // Process the report, and take no other action
            $report->update([
                'status'         => 'Cancelled',
                'staff_id'       => $user->id,
                'staff_comments' => $data['staff_comments'] ?? null,
            ]);

            // If the reporter has an email address on file, send a notification email
            if (isset($report->reporter->email)) {
                Mail::to($report->reporter->email)->send(new ReportCancelled($report));
            }

            return $this->commitReturn($report);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }

    /**
     * Bans a reporter.
     *
     * @param Report $report
     * @param array  $data
     * @param User   $user
     *
     * @return bool
     */
    public function banReporter($report, $data, $user) {
        DB::beginTransaction();

        try {
            if ($report->status != 'Pending') {
                throw new \Exception('This report has already been processed.');
            }

            // Process the report, and take no other action
            $report->update([
                'status'         => 'Cancelled',
                'staff_id'       => $user->id,
                'staff_comments' => $data['staff_comments'] ?? null,
            ]);

            // Mark the reporter as banned
            $report->reporter->update(['is_banned' => 1]);

            // and cancel all current reports from them
            Report::where('reporter_id', $report->reporter->id)->where('status', 'Pending')->update([
                'status'         => 'Cancelled',
                'staff_id'       => $user->id,
                'staff_comments' => '<p>Automatically cancelled due to ban.</p>',
            ]);

            return $this->commitReturn($report);
        } catch (\Exception $e) {
            $this->setError('error', $e->getMessage());
        }

        return $this->rollbackReturn(false);
    }
}
