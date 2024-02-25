<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NotificationFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition() {
        return [
            //
            'is_unread' => 1,
        ];
    }

    /**
     * Generate a notification for a specific user.
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

    /**
     * Generate a upload removed notification.
     *
     * @param \App\Models\ImageUpload|null $image
     *
     * @return Factory
     */
    public function uploadRemoved($image = null) {
        return $this->state(function (array $attributes) use ($image) {
            return [
                'notification_type_id' => 0,
                'data'                 => json_encode([
                    'slug'  => $image->slug ?? mt_rand(1, 50).randomString(15),
                ]),
            ];
        });
    }
}
