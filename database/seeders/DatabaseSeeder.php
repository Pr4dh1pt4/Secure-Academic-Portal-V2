<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Orkestrasi seeding, otomatis dijalankan saat instalasi awal (composer setup):
 *
 * - Peran Admin & Staf beserta 8 permission (RolePermissionSeeder).
 * - 30 mahasiswa (pemilik portofolio + 29 acak via Faker), masing-masing 2 proposal dan 3 riwayat tugas.
 * - Akun demo dosen (Admin) dan staf akademik (Staf).
 * - 2 sesi obrolan Agentic AI per user beserta log tool-nya.
 *
 * Total: 32 user, 63 proposal, 92 riwayat tugas, 64 sesi obrolan.
 * Aman dijalankan berulang kali tanpa error duplikasi.
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            // Peran dibuat paling awal karena seeder akun demo memberi peran ke user.
            RolePermissionSeeder::class,
            // Pemilik portofolio dibuat lebih dulu agar ikut terhitung sebagai salah satu dari 30 mahasiswa.
            PortfolioOwnerSeeder::class,
            StudentSeeder::class,
            DemoUserSeeder::class,
            // Dijalankan terakhir agar semua user di atas ikut mendapat riwayat obrolan.
            ChatSeeder::class,
        ]);
    }
}
