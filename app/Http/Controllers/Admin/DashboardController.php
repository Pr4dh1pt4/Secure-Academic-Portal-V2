<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Assignment;
use App\Models\ChatSession;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;
use Illuminate\View\View;

/**
 * Beranda panel backoffice: widget statistik agregat dari kueri count().
 */
class DashboardController extends Controller implements HasMiddleware
{
    /**
     * Pengaman controller (pengganti $this->middleware() di constructor sejak Laravel 11).
     *
     * @return list<Middleware>
     */
    public static function middleware(): array
    {
        return [
            new Middleware('permission:dashboard.lihat'),
        ];
    }

    /**
     * Tampilkan beranda admin.
     */
    public function index(Request $request): View
    {
        $user = $request->user();

        return view('admin.dashboard', [
            // Setiap widget dihitung lewat COUNT(*) di database, bukan ->get()->count().
            'widgets' => [
                [
                    'judul' => 'Mahasiswa',
                    'total' => User::mahasiswa()->count(),
                    'ikon' => 'bi bi-people-fill',
                    'warna' => 'primary',
                    'route' => 'admin.mahasiswa.index',
                    'permission' => 'mahasiswa.lihat',
                ],
                [
                    'judul' => 'Proposal Agentic AI',
                    'total' => Project::count(),
                    'ikon' => 'bi bi-lightbulb-fill',
                    'warna' => 'success',
                    'route' => 'admin.proposal.index',
                    'permission' => 'proposal.lihat',
                ],
                [
                    'judul' => 'Riwayat Tugas',
                    'total' => Assignment::count(),
                    'ikon' => 'bi bi-journal-check',
                    'warna' => 'warning',
                    'route' => 'admin.tugas.index',
                    'permission' => 'tugas.lihat',
                ],
                [
                    'judul' => 'Sesi Obrolan AI',
                    'total' => ChatSession::count(),
                    'ikon' => 'bi bi-chat-dots-fill',
                    'warna' => 'danger',
                    'route' => null,
                    'permission' => null,
                    'catatan' => 'Seluruh pengguna portal',
                ],
            ],
            'perTema' => Project::query()
                ->select('tema_agent')
                ->selectRaw('count(*) as jumlah')
                ->groupBy('tema_agent')
                ->orderByDesc('jumlah')
                ->get(),
            'proposalTerbaru' => Project::with('user:id,name,nrp')
                ->latest()
                ->take(5)
                ->get(['id', 'user_id', 'judul', 'tema_agent', 'created_at']),
            'peran' => $user->getRoleNames(),
            'hakAkses' => $user->getAllPermissions()->pluck('name')->sort()->values(),
        ]);
    }
}
