<?php

namespace Tests\Feature;

use App\Models\User\User;
use App\Services\InvitationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthRegistrationTest extends TestCase {
    use RefreshDatabase;

    // These tests center on basic user authentication
    // They are modified from https://github.com/dwightwatson/laravel-auth-tests

    /******************************************************************************
        AUTH / REGISTRATION
    *******************************************************************************/

    protected function setUp(): void {
        parent::setUp();
    }

    /**
     * Test registration page access.
     * This should always succeed, regardless of if registration is currently open.
     */
    public function testCanGetRegisterForm() {
        $this->get('/register')
            ->assertStatus(200);
    }

    /**
     * Test registration.
     *
     * @dataProvider postRegistrationProvider
     *
     * @param bool  $isValid
     * @param array $code
     * @param bool  $expected
     */
    public function testPostRegistration($isValid, $code, $expected) {
        if ($code[0] && $code[1]) {
            // Create a persistent admin and generate an invitation code
            $admin = User::factory()->admin()->create();
            $invitation = (new InvitationService)->generateInvitation($admin);

            if ($code[2]) {
                // Mark the code used if relevant
                $recipient = User::factory()->create();
                $invitation->update(['recipient_id' => $recipient->id]);
            }

            $invitationCode = $invitation->code;
        } elseif ($code[0] && !$code[1]) {
            // Otherwise generate a fake "code"
            $invitationCode = randomString(15);
        }

        $response = $this->post('register', [
            'name'                  => $this->user->name,
            'email'                 => $this->user->email,
            'password'              => 'password',
            'password_confirmation' => $isValid ? 'password' : 'invalid',
            'agreement'             => $isValid ?? null,
            'code'                  => $code[0] ? $invitationCode : null,
        ]);

        if ($expected) {
            $response->assertStatus(302);
            $response->assertSessionHasNoErrors();
            $this->assertAuthenticated();
        } else {
            $response->assertSessionHasErrors();
            $this->assertGuest();
        }
    }

    public static function postRegistrationProvider() {
        // $code = [$withCode, $isValid, $isUsed]

        return [
            'valid, with unused code'    => [1, [1, 1, 0], 1],
            'valid, with used code'      => [1, [1, 1, 1], 0],
            'valid, with invalid code'   => [1, [1, 0, 0], 0],
            'valid, without code'        => [1, [0, 0, 0], 0],
            'invalid, with unused code'  => [0, [1, 1, 0], 0],
            'invalid, with used code'    => [0, [1, 1, 1], 0],
            'invalid, with invalid code' => [0, [1, 0, 0], 0],
            'invalid, without code'      => [0, [0, 0, 0], 0],
        ];
    }
}
