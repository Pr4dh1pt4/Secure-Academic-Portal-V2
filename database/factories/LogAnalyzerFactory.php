<?php

namespace Database\Factories;

use App\Models\LogAnalyzer;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * Factory agent penganalisis log server.
 *
 * @extends Factory<LogAnalyzer>
 */
class LogAnalyzerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nama' => 'LogLens '.fake()->unique()->bothify('##??'),
            'versi' => fake()->numerify('#.#.#'),
            'sumber_log' => fake()->randomElement([
                'nginx/access.log',
                'nginx/error.log',
                'storage/logs/laravel.log',
                'auth.log',
            ]),
            'jendela_jam' => fake()->randomElement([6, 24, 72, 168]),
        ];
    }
}
