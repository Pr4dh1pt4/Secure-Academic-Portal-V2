<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Project;
use App\Models\User;
use Illuminate\View\View;

/**
 * Halaman depan portal kampus untuk umum (tanpa login).
 *
 * Hanya menampilkan data agregat dan informasi yang aman dipublikasikan:
 * tanpa email, NRP, maupun API key.
 */
class PortalController extends Controller
{
    /**
     * Tampilkan portal.
     */
    public function index(): View
    {
        $mahasiswa = User::where('email', 'like', '%@student.its.ac.id');

        return view('portal', [
            'statistik' => [
                'Mahasiswa' => (clone $mahasiswa)->count(),
                'Proposal Agentic AI' => Project::count(),
                'Riwayat Tugas' => Assignment::count(),
            ],
            'perProdi' => (clone $mahasiswa)
                ->whereNotNull('program_studi')
                ->select('program_studi')
                ->selectRaw('count(*) as jumlah')
                ->groupBy('program_studi')
                ->orderByDesc('jumlah')
                ->get(),
            // Hanya kolom yang aman ditampilkan; api_key_secure juga tersembunyi lewat $hidden.
            'proposalTerbaru' => Project::with('user:id,name,program_studi')
                ->latest()
                ->take(6)
                ->get(['id', 'user_id', 'judul', 'tema_agent', 'created_at']),
        ]);
    }
}
