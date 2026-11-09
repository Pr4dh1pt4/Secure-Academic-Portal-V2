<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Tambah kolom profil diri ke tabel users, menggantikan array profil statis di controller.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // VARCHAR(10): NRP ITS selalu 10 digit. Disimpan sebagai string agar angka 0 di depan
            // tidak hilang. Unique, tetapi nullable karena akun dosen tidak punya NRP.
            $table->string('nrp', 10)->nullable()->unique()->after('email');

            // VARCHAR untuk nama program studi.
            $table->string('program_studi')->nullable()->after('nrp');

            // TEXT karena bio bisa lebih dari 255 karakter.
            $table->text('bio')->nullable()->after('program_studi');

            // JSON untuk daftar minat dan keahlian yang jumlahnya bervariasi (dibaca sebagai array lewat cast).
            $table->json('minat')->nullable()->after('bio');
            $table->json('keahlian')->nullable()->after('minat');
        });
    }

    /**
     * Hapus kolom profil diri.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['nrp']);
            $table->dropColumn(['nrp', 'program_studi', 'bio', 'minat', 'keahlian']);
        });
    }
};
