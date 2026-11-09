<?php

namespace Database\Factories;

use App\Models\SecurityScanner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory agent pemindai kerentanan aplikasi web.
 *
 * @extends Factory<SecurityScanner>
 */
class SecurityScannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'WebSentinel '.fake()->unique()->bothify('##??'),
            'versi' => fake()->numerify('#.#.#'),
            'target_url' => 'https://'.fake()->randomElement(['portal', 'siakad', 'lms', 'repo']).'.its.test',
            'standar' => fake()->randomElement(['OWASP Top 10', 'OWASP ASVS 4.0', 'CWE Top 25']),
        ];
    }
}
