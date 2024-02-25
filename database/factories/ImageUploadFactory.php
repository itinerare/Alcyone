<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ImageUpload>
 */
class ImageUploadFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        return [
            //
            'key' => randomString(15),
        ];
    }

    /**
     * Generate an upload by a specific user.
     * This is essentially required.
     *
     * @param int $user
     *
     * @return Factory
     */
    public function user($user) {
        return $this->state(function (array $attributes) use ($user) {
            return [
                'user_id' => $user,
            ];
        });
    }
}
