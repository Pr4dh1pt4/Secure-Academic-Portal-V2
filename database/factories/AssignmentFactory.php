<?php

namespace Database\Factories;

use App\Models\Assignment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * Factory riwayat tugas kuliah Teknik Informatika yang realistis.
 *
 * @extends Factory<Assignment>
 */
class AssignmentFactory extends Factory
{
    /**
     * Mata kuliah beserta contoh judul tugas yang relevan.
     *
     * @var array<string, list<string>>
     */
    private const TUGAS = [
        'Pemrograman Berbasis Kerangka Kerja' => [
            'Routing dan Controller Profil Akademik',
            'Blade Layout dan Komponen Multi-View',
            'Form GET/POST dengan Validasi dan CSRF',
            'Migration, Seeder, dan Eloquent Relationship',
        ],
        'Basis Data' => [
            'Normalisasi Skema Sistem Perpustakaan',
            'Query Agregasi dan Subquery pada Data Akademik',
            'Stored Procedure dan Trigger Audit Log',
        ],
        'Kecerdasan Artifisial' => [
            'Implementasi A* untuk Pencarian Rute',
            'Klasifikasi Teks dengan Naive Bayes',
            'Rancangan Agen Berbasis LLM dengan Tool Calling',
        ],
        'Rekayasa Perangkat Lunak' => [
            'Dokumen SRS dan Use Case Diagram',
            'Rancangan Arsitektur dan Class Diagram',
            'Rencana Pengujian dan Test Case',
        ],
        'Jaringan Komputer' => [
            'Konfigurasi VLAN dan Routing Statis',
            'Analisis Paket HTTP dengan Wireshark',
        ],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $mataKuliah = fake()->randomElement(array_keys(self::TUGAS));
        $judul = fake()->randomElement(self::TUGAS[$mataKuliah]);

        return [
            'mata_kuliah' => $mataKuliah,
            'judul' => $judul,
            'deskripsi' => fake()->randomElement([
                'Dikerjakan secara individu, mencakup implementasi dan laporan singkat.',
                'Tugas kelompok tiga orang dengan pembagian peran analis, developer, dan penguji.',
                'Disertai demo langsung di kelas dan tanya jawab dengan asisten dosen.',
                null,
            ]),
            'tautan' => fake()->boolean(70)
                ? 'https://github.com/'.fake()->userName().'/'.Str::slug($judul)
                : null,
            'nilai' => fake()->optional(0.8)->numberBetween(70, 100),
            'dikumpulkan_pada' => fake()->dateTimeBetween('-4 months')->format('Y-m-d'),
        ];
    }
}
