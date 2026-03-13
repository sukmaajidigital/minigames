<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Game;
use App\Models\GameSession;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class HistoryController extends Controller
{
    public function index(Request $request): Response
    {
        $user = request()->user();

        $selectedGame = $request->string('game')->toString();
        $period = $request->string('period')->toString();

        $sessionsQuery = $user->gameSessions()->with('game:id,slug,title');

        if ($selectedGame !== '') {
            $sessionsQuery->where('game_slug', $selectedGame);
        }

        if ($period === '7d') {
            $sessionsQuery->where('played_at', '>=', now()->subDays(7));
        } elseif ($period === '30d') {
            $sessionsQuery->where('played_at', '>=', now()->subDays(30));
        }

        $sessions = $sessionsQuery
            ->latest('played_at')
            ->limit(30)
            ->get()
            ->map(fn($session) => [
                'id' => $session->id,
                'game_slug' => $session->game_slug,
                'game_title' => $session->game?->title ?? $session->game_slug,
                'score' => $session->score,
                'duration_seconds' => $session->duration_seconds,
                'played_at' => $session->played_at,
                'rank_in_game' => $this->rankInGame($session),
            ]);

        $bestScore = (int) ($user->gameSessions()->max('score') ?? 0);

        $globalRank = null;
        if ($bestScore > 0) {
            $globalRank = GameSession::query()
                ->selectRaw('user_id, MAX(score) as user_best')
                ->groupBy('user_id')
                ->havingRaw('MAX(score) > ?', [$bestScore])
                ->count() + 1;
        }

        $stats = [
            'total_sessions' => $user->gameSessions()->count(),
            'best_score' => $bestScore,
            'total_playtime_seconds' => (int) ($user->gameSessions()->sum('duration_seconds') ?? 0),
            'global_rank' => $globalRank,
        ];

        $games = Game::query()
            ->active()
            ->orderBy('title')
            ->get(['slug', 'title']);

        return Inertia::render('settings/History', [
            'sessions' => $sessions,
            'stats' => $stats,
            'games' => $games,
            'filters' => [
                'game' => $selectedGame,
                'period' => in_array($period, ['7d', '30d'], true) ? $period : 'all',
            ],
        ]);
    }

    private function rankInGame(GameSession $session): ?int
    {
        $higherCount = GameSession::query()
            ->where('game_slug', $session->game_slug)
            ->where('score', '>', $session->score)
            ->count();

        return $higherCount + 1;
    }
}
