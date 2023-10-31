<?php

namespace Tests\Feature;

use App\Models\User\Rank;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccessTest extends TestCase {
    use RefreshDatabase;

    /******************************************************************************
        ACCESS/MIDDLEWARE
    *******************************************************************************/

    protected function setUp(): void {
        parent::setUp();
    }

    /**
     * Test getting the main page.
     *
     * @dataProvider accessProvider
     *
     * @param bool $user
     * @param int  $status
     */
    public function testGetIndex($user, $status) {
        if ($user) {
            $response = $this->actingAs($this->user)->get('/');
        } else {
            $response = $this->get('/');
        }

        $response->assertStatus($status);
    }

    public static function accessProvider() {
        return [
            'visitor' => [0, 302],
            'user'    => [1, 200],
        ];
    }

    /**
     * Test access to account settings.
     * This should be representative of all member routes.
     *
     * @dataProvider memberAccessProvider
     *
     * @param bool $user
     * @param int  $rank
     * @param int  $status
     */
    public function testMemberRouteAccess($user, $rank, $status) {
        if ($user) {
            $user = User::factory()->make([
                'rank_id' => Rank::where('sort', $rank)->first()->id,
            ]);
            $response = $this->actingAs($user)->get('/account/settings');
        } else {
            $response = $this->get('/account/settings');
        }

        $response->assertStatus($status);
    }

    public static function memberAccessProvider() {
        return [
            'visitor'   => [0, 0, 302],
            'user'      => [1, 0, 200],
            'moderator' => [1, 1, 200],
            'admin'     => [1, 2, 200],
        ];
    }

    /**
     * Test access to the admin dashboard.
     * This should be representative of all moderator routes.
     *
     * @dataProvider moderatorAccessProvider
     *
     * @param bool $user
     * @param int  $rank
     * @param int  $status
     */
    public function testModeratorRouteAccess($user, $rank, $status) {
        if ($user) {
            $user = User::factory()->make([
                'rank_id' => Rank::where('sort', $rank)->first()->id,
            ]);
            $response = $this->actingAs($user)->get('/admin');
        } else {
            $response = $this->get('/admin');
        }

        $response->assertStatus($status);
    }

    public static function moderatorAccessProvider() {
        return [
            'visitor'   => [0, 0, 302],
            'user'      => [1, 0, 302],
            'moderator' => [1, 1, 200],
            'admin'     => [1, 2, 200],
        ];
    }

    /**
     * Test access to the admin user index.
     * This should be representative of all admin-only routes.
     *
     * @dataProvider adminAccessProvider
     *
     * @param bool $user
     * @param int  $rank
     * @param int  $status
     */
    public function testAdminRouteAccess($user, $rank, $status) {
        if ($user) {
            $user = User::factory()->make([
                'rank_id' => Rank::where('sort', $rank)->first()->id,
            ]);
            $response = $this->actingAs($user)->get('/admin/users');
        } else {
            $response = $this->get('/admin/users');
        }

        $response->assertStatus($status);
    }

    public static function adminAccessProvider() {
        return [
            'visitor'   => [0, 0, 302],
            'user'      => [1, 0, 302],
            'moderator' => [1, 1, 302],
            'admin'     => [1, 2, 200],
        ];
    }
}
