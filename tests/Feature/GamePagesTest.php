<?php

use App\Models\Game;
use Inertia\Testing\AssertableInertia as Assert;

test('games list page is displayed', function () {
    Game::factory()->create([
        'slug' => 'sample-game',
        'title' => 'Sample Game',
        'route_path' => '/games/math',
    ]);

    $response = $this->get(route('games.index'));

    $response
        ->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('Games')
                ->has('games', 1),
        );
});

test('math game page is displayed', function () {
    $response = $this->get(route('games.math'));

    $response
        ->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('games/Math')
                ->where('gameConfig.durationSeconds', 60)
                ->where('gameConfig.maxNumber', 20),
        );
});

test('leaderboard page is displayed', function () {
    $response = $this->get(route('games.leaderboard'));

    $response
        ->assertOk()
        ->assertInertia(
            fn(Assert $page) => $page
                ->component('games/Leaderboard')
                ->has('leaders')
                ->has('games'),
        );
});
