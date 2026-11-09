<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Tombol Login Instan (Auto-Login Test Accounts)
    |--------------------------------------------------------------------------
    |
    | Menampilkan tombol "Masuk sebagai ..." di halaman login untuk
    | mempermudah evaluasi demo di kelas. Fitur ini SELALU mati di
    | environment production, walaupun DEMO_LOGIN_ENABLED bernilai true.
    |
    */

    'enabled' => (bool) env('DEMO_LOGIN_ENABLED', false),

    /*
    | Daftar akun yang boleh dipakai login instan. Kunci dipakai di URL
    | (/login/demo/{akun}), sehingga request tidak bisa menyebut email
    | sembarang: hanya akun di daftar ini yang bisa dimasuki.
    */

    'accounts' => [
        'admin' => [
            'email' => 'dosenpbkk@its.ac.id',
            'label' => 'Admin',
            'keterangan' => 'Dosen PBKK: akses penuh, termasuk hapus data',
        ],
        'staf' => [
            'email' => 'staf@its.ac.id',
            'label' => 'Staf',
            'keterangan' => 'Staf Akademik: akses terbatas, tanpa hapus data',
        ],
        'mahasiswa' => [
            'email' => env('PORTFOLIO_OWNER_EMAIL', '5025241055@student.its.ac.id'),
            'label' => 'Mahasiswa',
            'keterangan' => 'Tanpa peran: tidak bisa membuka panel admin',
        ],
    ],

];
