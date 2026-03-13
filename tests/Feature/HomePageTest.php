<?php

use Inertia\Testing\AssertableInertia as Assert;

test('homepage is displayed with games section', function () {
    $response = $this->get(route('home'));

    $response
        ->assertOk()
        ->assertInertia(
            fn (Assert $page) => $page
                ->component('Home')
                ->has('games')
                ->where('seo.title', 'Minigames Indonesia - Main Game Seru Online'),
        );
});
