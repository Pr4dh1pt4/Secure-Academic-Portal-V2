<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Seeder peran dan hak akses (Spatie Laravel Permission).
 *
 * Matriks akses:
 *
 * | Permission       | Admin | Staf |
 * |------------------|:-----:|:----:|
 * | dashboard.lihat  |   ✓   |  ✓   |
 * | proposal.lihat   |   ✓   |  ✓   |
 * | proposal.tambah  |   ✓   |  ✓   |
 * | proposal.ubah    |   ✓   |  ✓   |
 * | mahasiswa.lihat  |   ✓   |  ✓   |
 * | tugas.lihat      |   ✓   |  ✓   |
 * | data.hapus       |   ✓   |      |
 * | pengguna.kelola  |   ✓   |      |
 *
 * Admin diberi semua permission secara eksplisit (tanpa Gate::before), sehingga
 * setiap pengecekan tetap melewati tabel otorisasi di database.
 *
 * Aman dijalankan berulang kali: memakai findOrCreate dan syncPermissions.
 *
 * Sengaja TIDAK memakai WithoutModelEvents: Spatie mengosongkan cache permission
 * lewat event model, sehingga tanpa event cache kosong yang lama akan terus terpakai.
 */
class RolePermissionSeeder extends Seeder
{
    /**
     * Seluruh permission beserta keterangannya.
     *
     * @var array<string, string>
     */
    public const PERMISSIONS = [
        'dashboard.lihat' => 'Membuka beranda panel admin dan widget statistik',
        'proposal.lihat' => 'Melihat tabel proposal Agentic AI',
        'proposal.tambah' => 'Menambah proposal baru',
        'proposal.ubah' => 'Mengubah proposal',
        'mahasiswa.lihat' => 'Melihat tabel data mahasiswa',
        'tugas.lihat' => 'Melihat tabel riwayat tugas',
        'data.hapus' => 'Menghapus data (proposal, mahasiswa, tugas)',
        'pengguna.kelola' => 'Mengatur peran pengguna',
    ];

    /**
     * Permission per peran. Admin mendapat semuanya.
     *
     * @var array<string, list<string>>
     */
    public const ROLES = [
        'Admin' => ['*'],
        'Staf' => [
            'dashboard.lihat',
            'proposal.lihat',
            'proposal.tambah',
            'proposal.ubah',
            'mahasiswa.lihat',
            'tugas.lihat',
        ],
    ];

    /**
     * Buat permission dan peran, lalu hubungkan keduanya.
     */
    public function run(): void
    {
        // Cache permission Spatie wajib direset sebelum dan sesudah perubahan.
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (array_keys(self::PERMISSIONS) as $nama) {
            Permission::findOrCreate($nama, 'web');
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::ROLES as $peran => $permissions) {
            Role::findOrCreate($peran, 'web')->syncPermissions(
                $permissions === ['*'] ? array_keys(self::PERMISSIONS) : $permissions,
            );
        }

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }
}
