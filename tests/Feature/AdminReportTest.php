<?php

namespace Tests\Feature;

use App\Mail\ReportAccepted;
use App\Mail\ReportCancelled;
use App\Models\ImageUpload;
use App\Models\Report\Report;
use App\Models\User\User;
use App\Services\ImageManager;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class AdminReportTest extends TestCase {
    use RefreshDatabase, WithFaker;

    /******************************************************************************
        ADMIN / REPORTS
    *******************************************************************************/

    protected function setUp(): void {
        parent::setUp();

        $this->user = User::factory()->moderator()->create();
        $this->service = (new ImageManager);
    }

    /**
     * Test getting the report queue.
     *
     * @dataProvider getCreateReportProvider
     *
     * @param string|null $status
     * @param array|null  $reportStatus
     * @param int         $responseStatus
     */
    public function testGetReportQueue($status, $reportStatus, $responseStatus) {
        if ($reportStatus) {
            // Create an image to report
            $image = ImageUpload::factory()->user($this->user->id)->create();
            $this->service->testImages($image, true);

            if ($reportStatus[0]) {
                $report = Report::factory()->image($image->id)->status($status ? ucfirst($status) : 'Pending')->create();
            }
            if ($reportStatus[1]) {
                $otherReport = Report::factory()->image($image->id)->status(
                    !$status || $status == 'pending' ? 'Accepted' : 'Pending'
                )->create();
            }
        }

        $response = $this
            ->actingAs($this->user)
            ->get('admin/reports'.($status ? '/'.$status : ''));

        $response->assertStatus($responseStatus);

        if ($reportStatus) {
            if ($reportStatus[0]) {
                $response->assertViewHas('reports', function ($reports) use ($report) {
                    return $reports->contains($report);
                });
            }
            if ($reportStatus[1]) {
                $response->assertViewHas('reports', function ($reports) use ($otherReport) {
                    return !$reports->contains($otherReport);
                });
            }

            $this->service->testImages($image, false);
        }
    }

    public static function getCreateReportProvider() {
        return [
            // $reportData = [$withReport, $withOtherReport]

            'default'                     => [null, null, 200],
            'default with report'         => [null, [1, 0], 200],
            'default with other report'   => [null, [0, 1], 200],
            'default with both reports'   => [null, [1, 1], 200],
            'pending'                     => ['pending', null, 200],
            'pending with report'         => ['pending', [1, 0], 200],
            'pending with other report'   => ['pending', [0, 1], 200],
            'pending with both reports'   => ['pending', [1, 1], 200],
            'accepted'                    => ['accepted', null, 200],
            'accepted with report'        => ['accepted', [1, 0], 200],
            'accepted with other report'  => ['accepted', [0, 1], 200],
            'accepted with both reports'  => ['accepted', [1, 1], 200],
            'cancelled'                   => ['cancelled', null, 200],
            'cancelled with report'       => ['cancelled', [1, 0], 200],
            'cancelled with other report' => ['cancelled', [0, 1], 200],
            'cancelled with both reports' => ['cancelled', [1, 1], 200],
            'invalid'                     => ['invalid', null, 404],
        ];
    }

    /**
     * Test getting a report.
     *
     * @dataProvider getReportProvider
     *
     * @param bool $isValid
     * @param bool $isProcessed
     * @param bool $deletedImage
     * @param int  $status
     */
    public function testGetReport($isValid, $isProcessed, $deletedImage, $status) {
        if ($isValid) {
            // Generate an image to report
            $image = ImageUpload::factory()->user($this->user->id)->create([
                'deleted_at' => $deletedImage ? Carbon::now() : null,
            ]);
            if (!$deletedImage) {
                $this->service->testImages($image);
            }

            $report = Report::factory()->image($image->id)->create($isProcessed ? [
                'status'   => ($deletedImage ? 'Accepted' : 'Cancelled'),
                'staff_id' => $this->user->id,
            ] : []);
        }

        $response = $this->actingAs($this->user);

        $response = $response
            ->get('admin/reports/'.(
                $isValid ? $report->id : mt_rand(1, 100)
            ));

        $response->assertStatus($status);

        if ($status == 200) {
            if (!$deletedImage) {
                $response->assertSee($image->url);
            } else {
                $response->assertDontSee($image->url);
            }

            $response->assertSee($report->reason);
            $response->assertSee($report->reporter->displayName);

            if ($isProcessed) {
                $response->assertSeeText($this->user->name);
                $response->assertSeeText($deletedImage ? 'Accepted' : 'Cancelled');
            } else {
                $response->assertSeeText('Pending');
            }
        }

        if ($isValid && !$deletedImage) {
            $this->service->testImages($image, false);
        }
    }

    public static function getReportProvider() {
        return [
            'valid, pending'   => [1, 0, 0, 200],
            'valid, cancelled' => [1, 1, 0, 200],
            'valid, accepted'  => [1, 1, 1, 200],
            'invalid'          => [0, 0, 0, 404],
        ];
    }

    /**
     * Test report processing.
     *
     * @dataProvider postProcessReportProvider
     *
     * @param string $status
     * @param string $operation
     * @param bool   $withComments
     * @param bool   $deletedImage
     * @param bool   $sendMail
     * @param bool   $expected
     */
    public function testPostProcessReport($status, $operation, $withComments, $deletedImage, $sendMail, $expected) {
        Mail::fake();

        // Generate an image to report
        $image = ImageUpload::factory()->user($this->user->id)->create([
            'deleted_at' => $deletedImage ? Carbon::now() : null,
        ]);
        if (!$deletedImage) {
            $this->service->testImages($image);
        }

        $report = Report::factory()->status($status)->image($image->id)->create();
        $comments = $withComments ? $this->faker->text() : null;

        if ($sendMail) {
            // Add an email address for the reporter so notifications will be sent
            $report->reporter->update([
                'email' => $this->faker->email(),
            ]);
        }

        $response = $this
            ->actingAs($this->user)
            ->post('/admin/reports/'.$report->id.'/'.$operation, [
                'staff_comments' => $comments,
            ]);

        if ($expected) {
            $response->assertSessionHasNoErrors();

            // Check that the commission has been updated appropriately
            switch ($operation) {
                case 'accept':
                    $this->assertDatabaseHas('reports', [
                        'id'             => $report->id,
                        'status'         => 'Accepted',
                        'staff_comments' => $comments ?? null,
                    ]);

                    if ($sendMail) {
                        Mail::assertSent(ReportAccepted::class);
                    } else {
                        Mail::assertNotSent(ReportAccepted::class);
                    }

                    if ($expected || $deletedImage) {
                        $this->assertSoftDeleted($image);
                        $this->assertFileDoesNotExist($image->imagePath.'/'.$image->imageFileName);
                        $this->assertFileDoesNotExist($image->imagePath.'/'.$image->thumbnailFileName);
                    } else {
                        $this->assertNotSoftDeleted($image);
                        $this->assertFileExists($image->imagePath.'/'.$image->imageFileName);
                        $this->assertFileExists($image->imagePath.'/'.$image->thumbnailFileName);
                    }
                    break;
                case 'cancel':
                    $this->assertDatabaseHas('reports', [
                        'id'             => $report->id,
                        'status'         => 'Cancelled',
                        'staff_comments' => $comments ?? null,
                    ]);

                    if ($sendMail) {
                        Mail::assertSent(ReportCancelled::class);
                    } else {
                        Mail::assertNotSent(ReportCancelled::class);
                    }

                    if (!$deletedImage) {
                        $this->assertNotSoftDeleted($image);
                        $this->assertFileExists($image->imagePath.'/'.$image->imageFileName);
                        $this->assertFileExists($image->imagePath.'/'.$image->thumbnailFileName);
                    }
                    break;
                case 'ban':
                    // Check both that the report and the reporter have been updated appropriately
                    $this->assertDatabaseHas('reporters', [
                        'id'        => $report->reporter->id,
                        'is_banned' => 1,
                    ]);

                    $this->assertDatabaseHas('reports', [
                        'id'             => $report->id,
                        'status'         => 'Cancelled',
                        'staff_comments' => $comments ?? null,
                    ]);

                    if ($sendMail) {
                        Mail::assertNotSent(ReportCancelled::class);
                    }

                    if (!$deletedImage) {
                        $this->assertNotSoftDeleted($image);
                        $this->assertFileExists($image->imagePath.'/'.$image->imageFileName);
                        $this->assertFileExists($image->imagePath.'/'.$image->thumbnailFileName);
                    }
                    break;
            }
        } else {
            $response->assertSessionHasErrors();
        }

        if (!$deletedImage && ($operation != 'accept' || $expected == 0)) {
            $this->service->testImages($image, false);
        }
    }

    public static function postProcessReportProvider() {
        return [
            'accept pending'                          => ['Pending', 'accept', 0, 0, 0, 1],
            'accept pending with comments'            => ['Pending', 'accept', 1, 0, 0, 1],
            'accept pending with deleted image'       => ['Pending', 'accept', 0, 1, 0, 1],
            'accept pending with mail'                => ['Pending', 'accept', 0, 0, 1, 1],
            'accept pending with deleted image, mail' => ['Pending', 'accept', 0, 1, 1, 1],
            'accept pending with all'                 => ['Pending', 'accept', 1, 1, 1, 1],
            'cancel pending'                          => ['Pending', 'cancel', 0, 0, 0, 1],
            'cancel pending with comments'            => ['Pending', 'cancel', 1, 0, 0, 1],
            'cancel pending with deleted image'       => ['Pending', 'cancel', 1, 1, 0, 1],
            'cancel pending with mail'                => ['Pending', 'cancel', 0, 0, 1, 1],
            'cancel pending with deleted image, mail' => ['Pending', 'cancel', 0, 1, 1, 1],
            'cancel pending with all'                 => ['Pending', 'cancel', 1, 1, 1, 1],

            'accept accepted'                         => ['Accepted', 'accept', 0, 0, 0, 0],
            'cancel accepted'                         => ['Accepted', 'cancel', 0, 0, 0, 0],

            'ban, pending'                            => ['Pending', 'ban', 0, 0, 0, 1],
            'ban, pending with comments'              => ['Pending', 'ban', 1, 0, 0, 1],
            'ban, pending with deleted image'         => ['Pending', 'ban', 0, 1, 0, 1],
            'ban, pending with mail'                  => ['Pending', 'ban', 0, 0, 1, 1],
            'ban, pending with mail, deleted image'   => ['Pending', 'ban', 0, 1, 1, 1],
            'ban, pending with all'                   => ['Pending', 'ban', 1, 1, 1, 1],
            'ban, accepted'                           => ['Accepted', 'ban', 0, 0, 0, 0],
            'ban, cancelled'                          => ['Cancelled', 'ban', 0, 0, 0, 0],
        ];
    }
}
