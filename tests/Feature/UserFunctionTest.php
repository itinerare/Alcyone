<?php

namespace Tests\Feature;

use App\Models\User\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserFunctionTest extends TestCase {
    use RefreshDatabase, WithFaker;

    /******************************************************************************
        USER / SETTINGS
    *******************************************************************************/

    protected function setUp(): void {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * Test theme editing.
     *
     * @dataProvider userEditProvider
     *
     * @param bool $isValid
     * @param bool $expected
     */
    public function testPostEditTheme($isValid, $expected) {
        // Generate some test data
        if ($isValid) {
            $theme = 'light';
        } else {
            $theme = $this->faker->domainWord();
        }

        $response = $this->actingAs($this->user)
            ->post('account/theme', [
                'theme' => $theme,
            ]);

        if ($expected) {
            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('users', [
                'id'    => $this->user->id,
                'theme' => $theme,
            ]);
        } else {
            $response->assertSessionHasErrors();
        }
    }

    /**
     * Test email editing.
     *
     * @dataProvider userEditProvider
     *
     * @param bool $isValid
     * @param bool $expected
     */
    public function testPostEditEmail($isValid, $expected) {
        // Generate some test data
        if ($isValid) {
            $email = $this->faker->unique()->safeEmail();
        } else {
            $email = $this->faker->domainWord();
        }

        $response = $this->actingAs($this->user)
            ->post('account/email', [
                'email' => $email,
            ]);

        if ($expected) {
            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('users', [
                'id'    => $this->user->id,
                'email' => $email,
            ]);
        } else {
            $response->assertSessionHasErrors();
        }
    }

    /**
     * Test admin notificatin preference editing.
     *
     * @dataProvider userEditProvider
     *
     * @param bool $isValid
     * @param bool $expected
     */
    public function testPostEditAdminNotifs($isValid, $expected) {
        if ($isValid) {
            $user = User::factory()->moderator()->create();
        } else {
            $user = $this->user;
        }

        $response = $this->actingAs($user)
            ->post('account/admin-notifs', [
                'receive_admin_notifs' => 1,
            ]);

        if ($expected) {
            $response->assertSessionHasNoErrors();
            $this->assertDatabaseHas('users', [
                'id'                   => $user->id,
                'receive_admin_notifs' => 1,
            ]);
        } else {
            $response->assertStatus(404);
        }
    }

    /**
     * Test password editing.
     *
     * @dataProvider userEditProvider
     *
     * @param bool $isValid
     * @param bool $expected
     */
    public function testPostEditPassword($isValid, $expected) {
        // Make a persistent user with a simple password
        $user = User::factory()->simplePass()->create();

        $response = $this->actingAs($user)
            ->post('account/password', [
                'old_password'              => 'simple_password',
                'new_password'              => 'password',
                'new_password_confirmation' => $isValid ? 'password' : 'not_password',
            ]);

        if ($expected) {
            $response->assertSessionHasNoErrors();
            $this->assertTrue(Hash::check('password', $user->fresh()->password));
        } else {
            $response->assertSessionHasErrors();
        }
    }

    public static function userEditProvider() {
        return [
            'valid'   => [1, 1],
            'invalid' => [0, 0],
        ];
    }
}
