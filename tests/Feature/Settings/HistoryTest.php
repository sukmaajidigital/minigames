<?php

use App\Models\Game;
use App\Models\GameSession;
use App\Models\User;
use Inertia\Testing\AssertableInertia as Assert;

test('history page requires authentication', function () {
    $response = $this->get(route('history.index'));

    $response->assertRedirect(route('login'));
});

test('history page shows authenticated user sessions only', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $game = Game::factory()->create([
        'slug' => 'math-quick-challenge',
        'title' => 'Math Quick Challenge',
    ]);

    GameSession::query()->create([
        'user_id' => $user->id,
        'game_id' => $game->id,
        'game_slug' => $game->slug,
        'score' => 120,
        'duration_seconds' => 60,
        'played_at' => now(),
    ]);

    GameSession::query()->create([
        'user_id' => $otherUser->id,
        'game_id' => $game->id,
        'game_slug' => $game->slug,
        'score' => 999,
        'duration_seconds' => 30,
        'played_at' => now(),
    ]);

    $response = $this->actingAs($user)->get(route('history.index'));

    $response
        ->assertSuccessful()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('settings/History')
                ->has('sessions', 1)
                ->where('stats.total_sessions', 1)
                ->where('stats.best_score', 120),
        );
});
