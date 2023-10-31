<?php

namespace Database\Factories\User;

use App\Models\User\Rank;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
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
            'name'              => $this->faker->unique()->domainWord(),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'rank_id'           => Rank::orderBy('sort', 'ASC')->first(),
            'password'          => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token'    => Str::random(10),
        ];
    }

    /**
     * Generate a user with a safe username.
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
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
     * @return \Illuminate\Database\Eloquent\Factories\Factory
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
     * @return \Illuminate\Database\Eloquent\Factories\Factory
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
     * @return \Illuminate\Database\Eloquent\Factories\Factory
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
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function admin() {
        return $this->state(function (array $attributes) {
            return [
                'rank_id' => Rank::orderBy('sort', 'DESC')->first(),
            ];
        });
    }
}
