<?php

use App\Models\Game;
use App\Models\GameSession;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('player profile page is displayed with stats and sessions', function () {
    $player = User::factory()->create([
        'name' => 'Player A',
    ]);

    $game = Game::factory()->create([
        'slug' => 'math-quick-challenge',
        'title' => 'Math Quick Challenge',
    ]);

    GameSession::query()->create([
        'user_id' => $player->id,
        'game_id' => $game->id,
        'game_slug' => $game->slug,
        'score' => 160,
        'duration_seconds' => 54,
        'played_at' => now(),
    ]);

    $response = $this->get(route('players.show', $player));

    $response
        ->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('players/Show')
                ->where('player.name', 'Player A')
                ->where('stats.best_score', 160)
                ->has('sessions', 1),
        );
});
