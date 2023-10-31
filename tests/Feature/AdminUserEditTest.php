<?php

namespace Tests\Feature;

use App\Models\User\Rank;
use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AdminUserEditTest extends TestCase {
    use RefreshDatabase, WithFaker;

    /******************************************************************************
        ADMIN / USER EDITING
    *******************************************************************************/

    protected function setUp(): void {
        parent::setUp();

        $this->user = User::factory()->admin()->create();

        // Create a user to view
        $this->subject = User::factory()->create();
    }

    /**
     * Test user index access.
     */
    public function testGetAdminUserIndex() {
        $this->actingAs($this->user)
            ->get('/admin/users')
            ->assertStatus(200);
    }

    /**
     * Test user edit page access.
     *
     * @dataProvider getUserProvider
     *
     * @param bool $withUser
     * @param int  $status
     */
    public function testGetEditUser($withUser, $status) {
        $this->actingAs($this->user)
            ->get('/admin/users/'.($withUser ? $this->subject->name : $this->faker()->domainWord()).'/edit')
            ->assertStatus($status);
    }

    /**
     * Test user updates access.
     *
     * @dataProvider getUserProvider
     *
     * @param bool $withUser
     * @param int  $status
     */
    public function testGetUserUpdates($withUser, $status) {
        $this->actingAs($this->user)
            ->get('/admin/users/'.($withUser ? $this->subject->name : $this->faker()->domainWord()).'/updates')
            ->assertStatus($status);
    }

    public static function getUserProvider() {
        return [
            'valid user'   => [1, 200],
            'invalid user' => [0, 404],
        ];
    }

    /**
     * Test user editing.
     *
     * @dataProvider postEditUserProvider
     *
     * @param bool  $withUser
     * @param array $data
     * @param bool  $expected
     */
    public function testPostEditUser($withUser, $data, $expected) {
        // Make a user of the specified rank
        $subject = User::factory()->create([
            'rank_id' => Rank::where('sort', $data[0])->first()->id,
        ]);

        // Generate some test data
        $username = $this->faker()->domainWord();

        $response = $this->actingAs($this->user)
            ->post('/admin/users/'.($withUser ? $subject->name : $this->faker()->domainWord()).'/basic', [
                'name'    => $data[2] ? $username : $subject->name,
                'rank_id' => $data[1],
            ]);

        if ($expected) {
            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('users', [
                'name'    => $data[2] ? $username : $subject->name,
                'rank_id' => $data[1],
            ]);
        } else {
            $response->assertSessionHasErrors();
        }
    }

    public static function postEditUserProvider() {
        // $data = [$oldRank, $newRank, $withUsername]

        return [
            'edit username'      => [1, [0, 0, 1], 1],
            'user to moderator'  => [1, [0, 1, 0], 1],
            'user to admin'      => [1, [0, 2, 0], 1],
            'moderator to admin' => [1, [1, 2, 0], 1],
            'admin to moderator' => [1, [2, 1, 0], 0],
            'invalid user'       => [0, [0, 0, 0], 0],
        ];
    }

    /******************************************************************************
        ADMIN / USER BANS
    *******************************************************************************/

    /**
     * Test ban user page access.
     *
     * @dataProvider getUserProvider
     *
     * @param bool $withUser
     * @param int  $status
     */
    public function testGetBanUser($withUser, $status) {
        $this->actingAs($this->user)
            ->get('/admin/users/'.($withUser ? $this->subject->name : $this->faker()->domainWord()).'/ban')
            ->assertStatus($status);
    }

    /**
     * Test ban confirmation access.
     *
     * @dataProvider getUserProvider
     *
     * @param bool $withUser
     * @param int  $status
     */
    public function testGetConfirmBanUser($withUser, $status) {
        $this->actingAs($this->user)
            ->get('/admin/users/'.($withUser ? $this->subject->name : $this->faker()->domainWord()).'/ban-confirm')
            ->assertStatus($status);
    }

    /**
     * Test unban user access.
     *
     * @dataProvider getUnbanUserProvider
     *
     * @param bool $withUser
     * @param bool $isBanned
     * @param int  $status
     */
    public function testGetUnbanUser($withUser, $isBanned, $status) {
        // Make a user of the specified ban status
        $subject = User::factory()->create(['is_banned' => $isBanned]);

        $this->actingAs($this->user)
            ->get('/admin/users/'.($withUser ? $subject->name : $this->faker()->domainWord()).'/unban-confirm')
            ->assertStatus($status);
    }

    public static function getUnbanUserProvider() {
        return [
            'banned user'   => [1, 1, 200],
            'unbanned user' => [1, 0, 404],
            'invalid user'  => [0, 0, 404],
        ];
    }

    /**
     * Test user banning.
     *
     * @dataProvider postBanUserProvider
     *
     * @param bool $withUser
     * @param int  $rank
     * @param bool $withReason
     * @param bool $expected
     */
    public function testPostBanUser($withUser, $rank, $withReason, $expected) {
        // Make a user of the specified rank
        $subject = User::factory()->create([
            'rank_id' => Rank::where('sort', $rank)->first()->id,
        ]);

        // Generate test data
        $reason = $this->faker()->domainWord();

        $response = $this->actingAs($this->user)
            ->post('/admin/users/'.($withUser ? $subject->name : $this->faker()->domainWord()).'/ban', [
                'ban_reason' => $withReason ? $reason : null,
            ]);

        if ($expected) {
            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('users', [
                'id'         => $subject->id,
                'is_banned'  => 1,
                'ban_reason' => $withReason ? $reason : null,
            ]);
        } else {
            $response->assertSessionHasErrors();
            if ($withUser) {
                $this->assertDatabaseHas('users', [
                    'id'         => $subject->id,
                    'is_banned'  => 0,
                    'ban_reason' => null,
                ]);
            }
        }
    }

    /**
     * Test ban message editing.
     *
     * @dataProvider postBanUserProvider
     *
     * @param bool $withUser
     * @param int  $rank
     * @param bool $withReason
     * @param bool $expected
     */
    public function testPostEditBan($withUser, $rank, $withReason, $expected) {
        // Make a persistent user of the specified rank and ban status
        $subject = User::factory()->banned()->create([
            'rank_id' => Rank::where('sort', $rank)->first()->id,
        ]);

        // Generate test data
        $reason = $this->faker()->domainWord();

        $response = $this->actingAs($this->user)
            ->post('/admin/users/'.($withUser ? $subject->name : $this->faker()->domainWord()).'/ban', [
                'ban_reason' => $withReason ? $reason : null,
            ]);

        if ($expected) {
            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('users', [
                'id'         => $subject->id,
                'is_banned'  => 1,
                'ban_reason' => $withReason ? $reason : null,
            ]);
        } else {
            $response->assertSessionHasErrors();
            $this->assertDatabaseHas('users', [
                'id'         => $subject->id,
                'is_banned'  => 1,
                'ban_reason' => $subject->ban_reason,
            ]);
        }
    }

    public static function postBanUserProvider() {
        return [
            'user'                  => [1, 0, 0, 1],
            'user with reason'      => [1, 0, 1, 1],
            'moderator'             => [1, 1, 0, 1],
            'moderator with reason' => [1, 1, 1, 1],
            'admin'                 => [1, 2, 0, 0],
            'admin with reason'     => [1, 2, 1, 0],
            'invalid user'          => [0, 0, 0, 0],
        ];
    }

    /**
     * Test user unbanning.
     *
     * @dataProvider postUnbanUserProvider
     *
     * @param bool $withUser
     * @param bool $isBanned
     * @param int  $rank
     * @param bool $expected
     */
    public function testPostUnbanUser($withUser, $isBanned, $rank, $expected) {
        // Make a persistent user of the specified rank and ban status
        $subject = User::factory()->create([
            'is_banned' => $isBanned,
            'rank_id'   => Rank::where('sort', $rank)->first()->id,
        ]);

        $response = $this->actingAs($this->user)
            ->post('/admin/users/'.($withUser ? $subject->name : $this->faker()->domainWord()).'/unban');

        if ($expected) {
            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('users', [
                'id'        => $subject->id,
                'is_banned' => 0,
            ]);
        } else {
            $response->assertSessionHasErrors();
            $this->assertDatabaseHas('users', [
                'id'        => $subject->id,
                'is_banned' => $isBanned,
            ]);
        }
    }

    public static function postUnbanUserProvider() {
        return [
            'user'               => [1, 1, 0, 1],
            'unbanned user'      => [1, 0, 0, 0],
            'moderator'          => [1, 1, 1, 1],
            'unbanned moderator' => [1, 0, 1, 0],
            'admin'              => [1, 1, 2, 0],
            'unbanned admin'     => [1, 0, 2, 0],
            'invalid user'       => [0, 0, 0, 0],
        ];
    }
}
