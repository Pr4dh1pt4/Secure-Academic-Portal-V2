<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Factory akun mahasiswa ITS dengan NRP dan email unik berformat NRP@student.its.ac.id.
 *
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Program studi rumpun informatika ITS beserta kode NRP-nya.
     *
     * @var array<string, string>
     */
    private const PROGRAM_STUDI = [
        '5024' => 'Teknik Komputer',
        '5025' => 'Teknik Informatika',
        '5026' => 'Sistem Informasi',
        '5027' => 'Teknologi Informasi',
    ];

    /**
     * Pilihan minat dan keahlian untuk profil mahasiswa.
     */
    private const MINAT = [
        'Kecerdasan Artifisial', 'Data Engineering', 'Keamanan Siber', 'Pengembangan Web',
        'Komputasi Awan', 'Interaksi Manusia dan Komputer', 'Competitive Programming', 'Internet of Things',
    ];

    private const KEAHLIAN = [
        'PHP & Laravel', 'Python', 'JavaScript', 'TypeScript', 'Java', 'C++', 'Go',
        'SQL', 'Git', 'Docker', 'Tailwind CSS', 'React', 'Flutter',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = fake('id_ID');
        $kode = $faker->randomElement(array_keys(self::PROGRAM_STUDI));

        // NRP = kode prodi + angkatan + nomor urut. Nomor urut unique() menjamin NRP
        // (dan email) tidak kembar; rentang 2000-9999 menghindari NRP akun pemilik portofolio.
        $nrp = $kode.$faker->randomElement(['22', '23', '24']).$faker->unique()->numberBetween(2000, 9999);
        $minat = $faker->randomElements(self::MINAT, 2);

        return [
            'name' => $faker->firstName().' '.$faker->lastName(),
            'email' => $nrp.'@student.its.ac.id',
            'nrp' => $nrp,
            'program_studi' => self::PROGRAM_STUDI[$kode],
            'bio' => sprintf(
                'Mahasiswa %s ITS yang tertarik pada %s dan %s.',
                self::PROGRAM_STUDI[$kode],
                Str::lower($minat[0]),
                Str::lower($minat[1]),
            ),
            'minat' => $minat,
            'keahlian' => $faker->randomElements(self::KEAHLIAN, 4),
            'email_verified_at' => now(),
            'password' => static::$password ??= Hash::make('password'),
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Indicate that the model's email address should be unverified.
     */
    public function unverified(): static
    {
        return $this->state(fn (array $attributes) => [
            'email_verified_at' => null,
        ]);
    }
}
