<?php

namespace Database\Seeders;

use App\Models\Assignment;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder akun mahasiswa acak hingga total 30 mahasiswa (termasuk pemilik portofolio),
 * masing-masing dengan tepat 2 proposal project dan 3 riwayat tugas kuliah.
 */
class StudentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Target jumlah akun mahasiswa (@student.its.ac.id) di database.
     */
    private const JUMLAH_MAHASISWA = 30;

    /**
     * Seed mahasiswa beserta project-nya.
     *
     * Hanya membuat kekurangan dari target, sehingga menjalankan db:seed
     * berulang kali tidak menggandakan data maupun memicu bentrok email.
     */
    public function run(): void
    {
        $sudahAda = User::where('email', 'like', '%@student.its.ac.id')->count();
        $kekurangan = max(0, self::JUMLAH_MAHASISWA - $sudahAda);

        if ($kekurangan === 0) {
            $this->command?->info('Mahasiswa acak sudah lengkap, dilewati.');

            return;
        }

        User::factory($kekurangan)
            ->has(Project::factory()->count(2))
            ->has(Assignment::factory()->count(3))
            ->create();
    }
}
