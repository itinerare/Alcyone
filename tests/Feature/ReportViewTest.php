<?php

namespace Tests\Feature;

use App\Mail\ReportSubmitted;
use App\Models\ImageUpload;
use App\Models\Report\Report;
use App\Models\User\User;
use App\Services\ImageManager;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ReportViewTest extends TestCase {
    use RefreshDatabase, WithFaker;

    /******************************************************************************
        REPORTS
    *******************************************************************************/

    protected function setUp(): void {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->service = (new ImageManager);
    }

    /**
     * Test getting the create report page.
     *
     * @dataProvider getCreateReportProvider
     *
     * @param bool $user
     * @param int  $status
     */
    public function testGetCreateReport($user, $status) {
        if ($user) {
            $response = $this->actingAs($this->user)->get('reports/new');
        } else {
            $response = $this->get('reports/new');
        }

        $response->assertStatus($status);
    }

    public static function getCreateReportProvider() {
        return [
            'visitor' => [0, 200],
            'user'    => [1, 200],
        ];
    }

    /**
     * Test getting the report page.
     *
     * @dataProvider getReportProvider
     *
     * @param bool $user
     * @param bool $isValid
     * @param bool $isProcessed
     * @param bool $deletedImage
     * @param int  $status
     */
    public function testGetReport($user, $isValid, $isProcessed, $deletedImage, $status) {
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

        if ($user) {
            $response = $this->actingAs($this->user);
        } else {
            $response = $this;
        }

        $response = $response
            ->get('reports/'.(
                $isValid ? $report->key : mt_rand(1, 1000).randomString(15)
            ));

        $response->assertStatus($status);

        if ($status == 200) {
            if (!$deletedImage) {
                $response->assertSee($image->url);
            } else {
                $response->assertDontSee($image->url);
            }
            $response->assertSee($report->reason);

            if ($isProcessed) {
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
            'visitor, pending, valid'   => [0, 1, 0, 0, 200],
            'visitor, cancelled, valid' => [0, 1, 1, 0, 200],
            'visitor, accepted, valid'  => [0, 1, 1, 1, 200],
            'visitor, pending, invalid' => [0, 0, 0, 0, 404],
            'user, pending, valid'      => [1, 1, 0, 0, 200],
            'user, cancelled, valid'    => [1, 1, 1, 0, 200],
            'user, accepted, valid'     => [1, 1, 1, 1, 200],
            'user, pending, invalid'    => [1, 0, 0, 0, 404],
        ];
    }

    /**
     * Test report creation.
     *
     * @dataProvider postReportProvider
     *
     * @param bool  $user
     * @param array $validity
     * @param bool  $sendNotif
     * @param bool  $expected
     */
    public function testPostReport($user, $validity, $sendNotif, $expected) {
        // Disable honeypot temporarily
        // otherwise the speed at which tests are run will cause it to trigger
        config()->set('honeypot.enabled', false);

        Mail::fake();
        if($sendNotif && $expected) {
            User::factory()->moderator()->create([
                'receive_admin_notifs' => 1,
            ]);
        }

        if ($validity[0]) {
            // Generate an image to report
            $image = ImageUpload::factory()->user($this->user->id)->create();
            $this->service->testImages($image);
        }

        // Generate some test data
        $data = [
            'image_url' => $validity[0] ? ($validity[0] == 'web' ? $image->imageUrl : url('images/converted/'.$image->slug)) : $this->faker()->url(),
            'reason'    => $validity[1] ? $this->faker()->text() : null,
            'email'     => $validity[2] ? $this->faker()->email() : null,
            'agreement' => $validity[3],
        ];

        if ($user) {
            $response = $this->actingAs($this->user);
        } else {
            $response = $this;
        }

        $response = $response
            ->post('reports/new', $data);

        if ($expected) {
            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('reports', [
                'image_upload_id' => $image->id,
                'reason'          => $data['reason'],
            ]);

            if ($validity[2]) {
                $this->assertDatabaseHas('reporters', [
                    'email' => $data['email'],
                ]);
            } else {
                $this->assertDatabaseCount('reporters', 1);
            }

            if ($sendNotif) {
                Mail::assertSent(ReportSubmitted::class);
            } else {
                Mail::assertNotSent(ReportSubmitted::class);
            }
        } else {
            $response->assertSessionHasErrors();
            $this->assertDatabaseEmpty('reports');
            $this->assertDatabaseEmpty('reporters');
            Mail::assertNotSent(ReportSubmitted::class);
        }

        if ($validity[0]) {
            $this->service->testImages($image, false);
        }
    }

    public static function postReportProvider() {
        return [
            // $validity = [$withImage, $withReason, $withEmail, $withAgreement]

            'visitor, with image (web), reason, agree'               => [0, ['web', 1, 0, 1], 0, 1],
            'visitor, with image (png), reason, agree'               => [0, ['png', 1, 0, 1], 0, 1],
            'visitor, with image (web), reason, agree, notif'        => [0, ['web', 1, 0, 1], 1, 1],
            'visitor, with image (png), reason, agree, notif'        => [0, ['png', 1, 0, 1], 1, 1],
            'visitor, with image (web), reason, email, agree'        => [0, ['web', 1, 1, 1], 0, 1],
            'visitor, with image (png), reason, email, agree'        => [0, ['png', 1, 1, 1], 0, 1],
            'visitor, with image (web), reason, email, agree, notif' => [0, ['web', 1, 1, 1], 1, 1],
            'visitor, with image (png), reason, email, agree, notif' => [0, ['png', 1, 1, 1], 1, 1],
            'visitor, with image (web), email, agree'                => [0, ['web', 0, 1, 1], 0, 0],
            'visitor, with image (png), email, agree'                => [0, ['png', 0, 1, 1], 0, 0],
            'visitor, with image (web), email, agree, notif'         => [0, ['web', 0, 1, 1], 1, 0],
            'visitor, with image (png), email, agree, notif'         => [0, ['png', 0, 1, 1], 1, 0],
            'visitor, with image (web)'                              => [0, ['web', 0, 0, 0], 0, 0],
            'visitor, with image (png)'                              => [0, ['png', 0, 0, 0], 0, 0],
            'visitor, with image (web), notif'                       => [0, ['web', 0, 0, 0], 1, 0],
            'visitor, with image (png), notif'                       => [0, ['png', 0, 0, 0], 1, 0],
            'visitor, with image (web), reason'                      => [0, ['web', 1, 0, 0], 0, 0],
            'visitor, with image (png), reason'                      => [0, ['png', 1, 0, 0], 0, 0],
            'visitor, with image (web), reason, notif'               => [0, ['web', 1, 0, 0], 1, 0],
            'visitor, with image (png), reason, notif'               => [0, ['png', 1, 0, 0], 1, 0],
            'visitor, with image (web), reason, email'               => [0, ['web', 1, 1, 0], 0, 0],
            'visitor, with image (png), reason, email'               => [0, ['png', 1, 1, 0], 0, 0],
            'visitor, with image (web), reason, email, notif'        => [0, ['web', 1, 1, 0], 1, 0],
            'visitor, with image (png), reason, email, notif'        => [0, ['png', 1, 1, 0], 1, 0],
            'visitor'                                                => [0, [null, 0, 0, 0], 0, 0],
            'visitor, notif'                                         => [0, [null, 0, 0, 0], 1, 0],
            'visitor, with reason'                                   => [0, [null, 1, 0, 0], 0, 0],
            'visitor, with reason, notif'                            => [0, [null, 1, 0, 0], 1, 0],
            'visitor, with email'                                    => [0, [null, 0, 1, 0], 0, 0],
            'visitor, with email, notif'                             => [0, [null, 0, 1, 0], 1, 0],
            'visitor, with reason, email'                            => [0, [null, 1, 1, 0], 0, 0],
            'visitor, with reason, email, notif'                     => [0, [null, 1, 1, 0], 1, 0],

            'user, with image (web), reason, agree'                  => [1, ['web', 1, 0, 1], 0, 1],
            'user, with image (png), reason, agree'                  => [1, ['png', 1, 0, 1], 0, 1],
            'user, with image (web), reason, agree, notif'           => [1, ['web', 1, 0, 1], 1, 1],
            'user, with image (png), reason, agree, notif'           => [1, ['png', 1, 0, 1], 1, 1],
            'user, with image (web), reason, email, agree'           => [1, ['web', 1, 1, 1], 0, 1],
            'user, with image (png), reason, email, agree'           => [1, ['png', 1, 1, 1], 0, 1],
            'user, with image (web), reason, email, agree, notif'    => [1, ['web', 1, 1, 1], 1, 1],
            'user, with image (png), reason, email, agree, notif'    => [1, ['png', 1, 1, 1], 1, 1],
            'user, with image (web), email, agree'                   => [1, ['web', 0, 1, 1], 0, 0],
            'user, with image (png), email, agree'                   => [1, ['png', 0, 1, 1], 0, 0],
            'user, with image (web), email, agree, notif'            => [1, ['web', 0, 1, 1], 1, 0],
            'user, with image (png), email, agree, notif'            => [1, ['png', 0, 1, 1], 1, 0],
            'user, with image (web), reason'                         => [1, ['web', 0, 0, 0], 0, 0],
            'user, with image (png), reason'                         => [1, ['png', 0, 0, 0], 0, 0],
            'user, with image (web), reason, notif'                  => [1, ['web', 0, 0, 0], 1, 0],
            'user, with image (png), reason, notif'                  => [1, ['png', 0, 0, 0], 1, 0],
            'user, with image (web), reason'                         => [1, ['web', 1, 0, 0], 0, 0],
            'user, with image (png), reason'                         => [1, ['png', 1, 0, 0], 0, 0],
            'user, with image (web), reason, notif'                  => [1, ['web', 1, 0, 0], 1, 0],
            'user, with image (png), reason, notif'                  => [1, ['png', 1, 0, 0], 1, 0],
            'user, with image (web), reason, email'                  => [1, ['web', 1, 1, 0], 0, 0],
            'user, with image (png), reason, email'                  => [1, ['png', 1, 1, 0], 0, 0],
            'user, with image (web), reason, email, notif'           => [1, ['web', 1, 1, 0], 1, 0],
            'user, with image (png), reason, email, notif'           => [1, ['png', 1, 1, 0], 1, 0],
            'user'                                                   => [1, [null, 0, 0, 0], 0, 0],
            'user, notif'                                            => [1, [null, 0, 0, 0], 1, 0],
            'user, with reason'                                      => [1, [null, 1, 0, 0], 0, 0],
            'user, with reason, notif'                               => [1, [null, 1, 0, 0], 1, 0],
            'user, with email'                                       => [1, [null, 0, 1, 0], 0, 0],
            'user, with email, notif'                                => [1, [null, 0, 1, 0], 1, 0],
            'user, with reason, email'                               => [1, [null, 1, 1, 0], 0, 0],
            'user, with reason, email, notif'                        => [1, [null, 1, 1, 0], 1, 0],
        ];
    }
}
