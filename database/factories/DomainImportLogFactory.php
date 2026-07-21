<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DomainImportLog>
 */
class DomainImportLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $newCount = $this->faker->numberBetween(1, 10);
        $updatedCount = $this->faker->numberBetween(1, 15);
        $deactivatedCount = $this->faker->numberBetween(0, 5);
        $reactivatedCount = $this->faker->numberBetween(0, 3);
        
        $newDomains = [];
        for ($i = 0; $i < $newCount; $i++) {
            $newDomains[] = $this->faker->domainWord() . '.go.id (Dinas ' . $this->faker->word() . ')';
        }
        
        $updatedDomains = [];
        for ($i = 0; $i < $updatedCount; $i++) {
            $updatedDomains[] = $this->faker->domainWord() . '.go.id (Badan ' . $this->faker->word() . ')';
        }
        
        $deactivatedDomains = [];
        for ($i = 0; $i < $deactivatedCount; $i++) {
            $deactivatedDomains[] = $this->faker->domainWord() . '.go.id (Kantor ' . $this->faker->word() . ')';
        }
        
        $reactivatedDomains = [];
        for ($i = 0; $i < $reactivatedCount; $i++) {
            $reactivatedDomains[] = $this->faker->domainWord() . '.go.id (Dinas ' . $this->faker->word() . ')';
        }

        return [
            'filename' => 'export_cloudflare_' . $this->faker->date('Ymd') . '.xlsx',
            'imported_by' => 1,
            'total_records' => $this->faker->numberBetween(50, 200),
            'new_domains' => $newCount,
            'updated_domains' => $updatedCount,
            'deactivated_domains' => $deactivatedCount,
            'reactivated_domains' => $reactivatedCount,
            'summary' => [
                'new' => $newDomains,
                'updated' => $updatedDomains,
                'deactivated' => $deactivatedDomains,
                'reactivated' => $reactivatedDomains,
                'errors' => [],
            ],
        ];
    }
}
