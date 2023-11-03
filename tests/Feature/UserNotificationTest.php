<?php

namespace Tests\Feature;

use App\Models\ImageUpload;
use App\Models\Notification;
use App\Models\Report\Report;
use App\Models\User\User;
use App\Services\ImageManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserNotificationTest extends TestCase {
    use RefreshDatabase, WithFaker;

    /******************************************************************************
        USER / NOTIFICATIONS
    *******************************************************************************/

    protected function setUp(): void {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->service = (new ImageManager);
    }

    /**
     * Test notifications access.
     *
     * @dataProvider getNotificationsProvider
     *
     * @param bool $withNotification
     * @param int  $status
     */
    public function testGetNotifications($withNotification, $status) {
        if ($withNotification) {
            // Create a notification to view
            Notification::factory()->user($this->user->id)->uploadRemoved()->create();
        }

        $this->actingAs($this->user)
            ->get('/notifications')
            ->assertStatus($status);
    }

    public static function getNotificationsProvider() {
        return [
            'empty'             => [0, 200],
            'with notification' => [1, 200],
        ];
    }

    /**
     * Test clearing all notifications.
     *
     * @dataProvider postClearNotificationsProvider
     *
     * @param bool $withNotification
     */
    public function testPostClearAllNotifications($withNotification) {
        if ($withNotification) {
            // Create a notification to clear
            $notification = Notification::factory()->user($this->user->id)->uploadRemoved()->create();
        }

        // This operation should always result in a redirect
        $response = $this->actingAs($this->user)
            ->post('/notifications/clear')
            ->assertStatus(302);

        $response->assertSessionHasNoErrors();
        if ($withNotification) {
            $this->assertModelMissing($notification);
        }
    }

    public static function postClearNotificationsProvider() {
        return [
            'empty'             => [0],
            'with notification' => [1],
        ];
    }

    /**
     * Test clearing notifications of a set type.
     *
     * @dataProvider postClearTypedNotificationsProvider
     *
     * @param bool $withNotification
     * @param bool $withUnrelated
     */
    public function testPostClearTypedNotifications($withNotification, $withUnrelated) {
        if ($withNotification) {
            // Create a notification to clear
            $notification = Notification::factory()->user($this->user->id)->uploadRemoved()->create();
        }

        if ($withUnrelated) {
            // Create an unrelated notification that should not be cleared
            $unrelatedNotification = Notification::factory()->user($this->user->id)->uploadRemoved()->create();
        }

        // This operation should always result in a redirect
        $response = $this->actingAs($this->user)
            ->post('/notifications/clear/0')
            ->assertStatus(302);

        $response->assertSessionHasNoErrors();
        if ($withNotification) {
            $this->assertModelMissing($notification);
        }
        if ($withUnrelated) {
            $this->assertModelExists($unrelatedNotification);
        }
    }

    public static function postClearTypedNotificationsProvider() {
        return [
            'empty'                                 => [0, 0],
            'with notification'                     => [1, 0],
            // At present Alcyone has no other notification types
            //'with unrelated notif'                  => [0, 1],
            //'with notification and unrelated notif' => [1, 1],
        ];
    }

    /**
     * Test sending notifications.
     *
     * @dataProvider sendNotificationsProvider
     *
     * @param int $type
     */
    public function testSendNotification($type) {
        $moderator = User::factory()->moderator()->create();

        switch ($type) {
            case 0:
                // UPLOAD_REMOVED
                // This is sent when an image is deleted via an accepted report
                $image = ImageUpload::factory()->user($this->user->id)->create();
                $this->service->testImages($image);
                $report = Report::factory()->image($image->id)->create();

                $response = $this->actingAs($moderator)
                    ->post('/admin/reports/'.$report->id.'/accept');
                break;
        }

        $response->assertSessionHasNoErrors();
        $this->assertDatabaseHas('notifications', [
            'user_id'              => $this->user->id,
            'notification_type_id' => $type,
        ]);
    }

    public static function sendNotificationsProvider() {
        return [
            'image upload deleted' => [0],
        ];
    }
}
