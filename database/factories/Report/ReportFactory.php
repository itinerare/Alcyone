<?php

namespace Database\Factories\Report;

use App\Models\Report\Report;
use App\Models\Report\Reporter;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ReportFactory extends Factory {
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array {
        $reporter = Reporter::factory()->create();

        return [
            //
            'status'      => 'Pending',
            'reporter_id' => $reporter->id,
            'key'         => randomString(15),
            'reason'      => $this->faker->text(),
        ];
    }

    /**
     * Configure the model factory.
     *
     * @return $this
     */
    public function configure() {
        return $this->afterMaking(function (Report $report) {
            //
            $report->update([
                'key' => $report->id.$report->key,
            ]);
        })->afterCreating(function (Report $report) {
            //
            $report->update([
                'key' => $report->id.$report->key,
            ]);
        });
    }

    /**
     * Generate a report for a specific image.
     * This is essentially required.
     *
     * @param int $image
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function image($image) {
        return $this->state(function (array $attributes) use ($image) {
            return [
                'image_upload_id' => $image,
            ];
        });
    }

    /**
     * Generate a report with a given status.
     *
     * @param string $status
     *
     * @return \Illuminate\Database\Eloquent\Factories\Factory
     */
    public function status($status) {
        return $this->state(function (array $attributes) use ($status) {
            return [
                'status' => $status,
            ];
        });
    }
}
