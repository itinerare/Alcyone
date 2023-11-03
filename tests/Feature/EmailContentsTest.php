<?php

namespace Tests\Feature;

use App\Mail\ReportAccepted;
use App\Mail\ReportCancelled;
use App\Models\ImageUpload;
use App\Models\Report\Report;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class EmailContentsTest extends TestCase {
    use RefreshDatabase, WithFaker;

    /******************************************************************************
        EMAIL CONTENTS
    *******************************************************************************/

    protected function setUp(): void {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * Test report notification email contents.
     *
     * @dataProvider reportNotificationProvider
     *
     * @param string $mailType
     * @param string $status
     */
    public function testReportNotification($mailType, $status) {
        $image = ImageUpload::factory()->user($this->user->id)->create();
        $report = Report::factory()->image($image->id)->status($status)->create();

        switch ($mailType) {
            case 'ReportCancelled':
                $mailable = new ReportCancelled($report);
                $mailable->assertHasSubject('Content Report Processed');
                break;
            case 'ReportAccepted':
                $mailable = new ReportAccepted($report);
                $mailable->assertHasSubject('Content Report Accepted');
                break;
        }

        $mailable->assertSeeInHtml($report->url);
    }

    public static function reportNotificationProvider() {
        return [
            'declined request' => ['ReportCancelled', 'Cancelled'],
            'accepted request' => ['ReportAccepted', 'Accepted'],
        ];
    }
}
