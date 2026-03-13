<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import GameCard from '@/components/GameCard.vue';
import { Button } from '@/components/ui/button';

type GameItem = {
    id: number;
    slug: string;
    title: string;
    short_description: string;
    description: string | null;
    category: string;
    difficulty: string;
    thumbnail: string | null;
    route_path: string | null;
    play_count: number;
};

defineProps<{
    games: GameItem[];
    seo: {
        title: string;
        description: string;
        canonical: string;
        ogImage: string;
    };
}>();
</script>

<template>
    <Head :title="seo.title">
        <meta name="description" :content="seo.description" />
        <link rel="canonical" :href="seo.canonical" />
        <meta property="og:type" content="website" />
        <meta property="og:title" :content="seo.title" />
        <meta property="og:description" :content="seo.description" />
        <meta property="og:url" :content="seo.canonical" />
        <meta property="og:image" :content="seo.ogImage" />
        <meta name="twitter:card" content="summary_large_image" />
    </Head>

    <div class="min-h-screen bg-neutral-50">
        <header
            class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-5 md:px-8"
        >
            <Link href="/" class="text-lg font-semibold"
                >Minigames Indonesia</Link
            >
            <div class="flex items-center gap-2">
                <Button as-child variant="outline">
                    <Link href="/">Kembali ke homepage</Link>
                </Button>
                <Button as-child>
                    <Link href="/games/leaderboard">Leaderboard</Link>
                </Button>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-6 pb-16 md:px-8">
            <section class="space-y-2">
                <h1 class="text-3xl font-semibold">Daftar Game</h1>
                <p class="text-sm text-muted-foreground">
                    Koleksi game ringan untuk belajar sambil bermain.
                </p>
            </section>

            <section class="mt-6 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <GameCard v-for="game in games" :key="game.id" :game="game" />
            </section>
        </main>
    </div>
</template>
