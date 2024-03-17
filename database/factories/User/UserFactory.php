<?php

namespace Database\Factories\User;

use App\Models\User\Rank;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory {
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        // First things first, check if user ranks exist...
        if (!Rank::count()) {
            // Create ranks if not already present.
            // A light-weight rank system is used here since the site is intended
            // only for individuals or small groups/granular permissions are not
            // necessary.
            $adminRank = Rank::create([
                'name'        => 'Admin',
                'description' => 'The site admin.',
                'sort'        => 2,
            ]);

            Rank::create([
                'name'        => 'Moderator',
                'description' => 'A site moderator.',
                'sort'        => 1,
            ]);

            Rank::create([
                'name'        => 'Member',
                'description' => 'A regular member of the site.',
                'sort'        => 0,
            ]);
        }

        return [
            'name'              => fake()->unique()->domainWord(),
            'email'             => fake()->unique()->safeEmail(),
            'email_verified_at' => now(),
            'rank_id'           => Rank::orderBy('sort', 'ASC')->first(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }

    /**
     * Generate a user with a safe username.
     *
     * @return Factory
     */
    public function safeUsername() {
        return $this->state(function (array $attributes) {
            return [
                'name' => $this->faker->unique()->domainWord(),
            ];
        });
    }

    /**
     * Generate a user with a simple, known password.
     *
     * @return Factory
     */
    public function simplePass() {
        return $this->state(function (array $attributes) {
            return [
                'password' => Hash::make('simple_password'),
            ];
        });
    }

    /**
     * Indicate that the user is banned.
     *
     * @return Factory
     */
    public function banned() {
        return $this->state(function (array $attributes) {
            return [
                'is_banned'  => 1,
                'ban_reason' => 'Generated as banned',
                'banned_at'  => now(),
            ];
        });
    }

    /**
     * Indicate that the user is a moderator.
     *
     * @return Factory
     */
    public function moderator() {
        return $this->state(function (array $attributes) {
            return [
                'rank_id' => Rank::orderBy('sort', 'DESC')->skip(1)->first(),
            ];
        });
    }

    /**
     * Indicate that the user is an admin.
     *
     * @return Factory
     */
    public function admin() {
        return $this->state(function (array $attributes) {
            return [
                'rank_id' => Rank::orderBy('sort', 'DESC')->first(),
            ];
        });
    }
}
