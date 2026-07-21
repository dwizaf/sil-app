<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Domain>
 */
class DomainFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = $this->faker->randomElement(['active', 'inactive']);
        
        return [
            'name' => $this->faker->unique()->domainName(),
            'opd' => $this->faker->optional(0.8)->randomElement(['Dinas Pendidikan', 'Dinas Kesehatan', 'Badan Kepegawaian Daerah', 'Dinas Komunikasi dan Informatika']),
            'app_name' => $this->faker->optional(0.7)->company(), // 70% chance to have app name
            'app_description' => $this->faker->optional(0.5)->sentence(),
            'type' => $this->faker->randomElement(['A', 'CNAME', 'MX', 'TXT']),
            'content' => $this->faker->ipv4(),
            'ttl' => $this->faker->randomElement([1, 300, 3600, 86400]),
            'proxied' => $this->faker->boolean(),
            'status' => $status,
            'category' => $this->faker->randomElement(['pemerintah', 'docker', 'staging', 'production', 'lainnya']),
            'notes' => $this->faker->sentence(),
            'inactive_at' => $status === 'inactive' ? $this->faker->dateTimeBetween('-1 year', 'now') : null,
            'imported_at' => $this->faker->dateTimeBetween('-1 month', 'now'),
        ];
    }
}
