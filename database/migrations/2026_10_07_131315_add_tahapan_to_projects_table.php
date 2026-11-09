<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom tahapan alur kerja agen, menggantikan array $stages statis di controller.
     */
    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            // JSON berisi daftar {judul, keterangan} yang jumlahnya bervariasi per proposal.
            // Nullable karena tidak semua proposal sudah dirinci sampai tahap alur kerja.
            $table->json('tahapan')->nullable()->after('deskripsi');
        });
    }

    /**
     * Hapus kolom tahapan.
     */
    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('tahapan');
        });
    }
};
