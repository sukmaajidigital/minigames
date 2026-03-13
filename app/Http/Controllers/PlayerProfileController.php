<?php

namespace App\Http\Controllers;

use App\Models\User;
use Inertia\Inertia;
use Inertia\Response;

class PlayerProfileController extends Controller
{
    public function show(User $user): Response
    {
        $sessions = $user->gameSessions()
            ->with('game:id,slug,title')
            ->latest('played_at')
            ->limit(20)
            ->get()
            ->map(fn($session) => [
                'id' => $session->id,
                'game_title' => $session->game?->title ?? $session->game_slug,
                'game_slug' => $session->game_slug,
                'score' => $session->score,
                'duration_seconds' => $session->duration_seconds,
                'played_at' => $session->played_at,
            ]);

        $bestScore = (int) ($user->gameSessions()->max('score') ?? 0);

        return Inertia::render('players/Show', [
            'player' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->avatar,
                'created_at' => $user->created_at,
            ],
            'stats' => [
                'best_score' => $bestScore,
                'total_sessions' => (int) $user->gameSessions()->count(),
                'total_playtime_seconds' => (int) $user->gameSessions()->sum('duration_seconds'),
            ],
            'sessions' => $sessions,
            'seo' => [
                'title' => sprintf('Profil Pemain %s - SuGames', $user->name),
                'description' => sprintf('Lihat statistik permainan dan history skor akun %s di SuGames.', $user->name),
                'canonical' => route('players.show', $user),
                'ogImage' => $user->avatar ?: '/sugames.png',
            ],
        ]);
    }
}
