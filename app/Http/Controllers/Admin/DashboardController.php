<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $stats = [
            'totalUsers' => User::query()->count(),
            'totalGames' => Game::query()->count(),
            'totalPlays' => Game::query()->sum('play_count'),
        ];

        $latestUsers = User::query()
            ->latest('id')
            ->limit(8)
            ->get(['id', 'name', 'email', 'created_at']);

        return Inertia::render('admin/Dashboard', [
            'stats' => $stats,
            'latestUsers' => $latestUsers,
        ]);
    }
}
