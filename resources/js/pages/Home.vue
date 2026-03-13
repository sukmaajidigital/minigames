<script setup lang="ts">
import { Head, Link, usePage } from '@inertiajs/vue3';
import { Trophy, Users } from 'lucide-vue-next';
import { computed } from 'vue';
import GameCard from '@/components/GameCard.vue';
import { Button } from '@/components/ui/button';
import { dashboard, login, register } from '@/routes';
import { redirect as googleRedirect } from '@/routes/auth/google';

type GameItem = {
    id: number;
    slug: string;
    title: string;
    short_description: string;
    category: string;
    difficulty: string;
    thumbnail: string | null;
    route_path: string | null;
    play_count: number;
};

defineProps<{
    canRegister: boolean;
    games: GameItem[];
    seo: {
        title: string;
        description: string;
        keywords: string;
        canonical: string;
        ogImage: string;
    };
}>();

const page = usePage();
const isLoggedIn = computed(() => Boolean(page.props.auth?.user));
const isAdmin = computed(() => Boolean(page.props.auth?.user?.is_admin));
const schema = computed(() =>
    JSON.stringify({
        '@context': 'https://schema.org',
        '@type': 'WebSite',
        name: 'Minigames Indonesia',
        url: typeof window === 'undefined' ? '' : window.location.origin,
        potentialAction: {
            '@type': 'SearchAction',
            target: `${typeof window === 'undefined' ? '' : window.location.origin}/games`,
            'query-input': 'required name=search_term_string',
        },
    }),
);
</script>

<template>
    <Head :title="seo.title">
        <meta name="description" :content="seo.description" />
        <meta name="keywords" :content="seo.keywords" />
        <link rel="canonical" :href="seo.canonical" />
        <meta property="og:type" content="website" />
        <meta property="og:title" :content="seo.title" />
        <meta property="og:description" :content="seo.description" />
        <meta property="og:url" :content="seo.canonical" />
        <meta property="og:image" :content="seo.ogImage" />
        <meta name="twitter:card" content="summary_large_image" />
        <meta name="twitter:title" :content="seo.title" />
        <meta name="twitter:description" :content="seo.description" />
        <meta name="twitter:image" :content="seo.ogImage" />
        <script type="application/ld+json" v-html="schema" />
    </Head>

    <div
        class="min-h-screen bg-[radial-gradient(circle_at_top,#f6fbff_0%,#ffffff_45%,#fff9ef_100%)] text-neutral-900"
    >
        <header
            class="mx-auto flex w-full max-w-7xl items-center justify-between px-6 py-5 md:px-8"
        >
            <Link href="/" class="flex items-center gap-3">
                <img
                    src="/sugames.png"
                    alt="SuGames"
                    class="h-9 w-9 rounded-lg border border-neutral-200 object-cover"
                />
                <span class="text-lg font-semibold tracking-tight"
                    >SuGames</span
                >
            </Link>
            <div class="flex items-center gap-2">
                <template v-if="isLoggedIn">
                    <Button v-if="isAdmin" as-child variant="outline">
                        <Link :href="dashboard()">Admin dashboard</Link>
                    </Button>
                    <Button as-child>
                        <Link href="/games">Lihat semua game</Link>
                    </Button>
                </template>
                <template v-else>
                    <Button as-child variant="outline">
                        <Link :href="login()">Masuk</Link>
                    </Button>
                    <Button as-child variant="secondary">
                        <a :href="googleRedirect().url">Login Google</a>
                    </Button>
                    <Button v-if="canRegister" as-child>
                        <Link :href="register()">Daftar</Link>
                    </Button>
                </template>
            </div>
        </header>

        <main class="mx-auto w-full max-w-7xl px-6 pb-16 md:px-8">
            <section
                class="grid gap-8 rounded-3xl border border-neutral-200/70 bg-white/80 p-8 shadow-sm backdrop-blur md:grid-cols-[1.2fr_0.8fr] md:p-10"
            >
                <div class="space-y-5">
                    <p
                        class="inline-flex rounded-full border border-emerald-200 bg-emerald-50 px-3 py-1 text-xs font-medium text-emerald-700"
                    >
                        Platform game edukatif ringan
                    </p>
                    <h1
                        class="text-3xl leading-tight font-semibold md:text-5xl"
                    >
                        Main mini game seru untuk latih fokus, logika, dan
                        hitung cepat.
                    </h1>
                    <p
                        class="max-w-2xl text-sm leading-relaxed text-neutral-600 md:text-base"
                    >
                        Mulai dari game matematika interaktif hingga puzzle
                        ringan. Semua dirancang agar nyaman dibaca, cepat
                        dimainkan, dan cocok di mobile maupun desktop.
                    </p>
                    <div class="flex flex-wrap items-center gap-3">
                        <Button as-child size="lg">
                            <Link href="/games/math">Main Game Matematika</Link>
                        </Button>
                        <Button as-child variant="outline" size="lg">
                            <Link href="/games">Jelajahi game lain</Link>
                        </Button>
                        <Button as-child variant="secondary" size="lg">
                            <Link href="/games/leaderboard"
                                >Lihat leaderboard</Link
                            >
                        </Button>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-1"
                >
                    <div
                        class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4"
                    >
                        <div class="mb-2 inline-flex rounded-lg bg-white p-2">
                            <Trophy class="h-5 w-5 text-amber-600" />
                        </div>
                        <p class="text-sm font-medium">Leaderboard-ready</p>
                        <p
                            class="mt-1 text-xs leading-relaxed text-neutral-600"
                        >
                            Fondasi sudah siap untuk tracking skor dan statistik
                            game per user.
                        </p>
                    </div>
                    <div
                        class="rounded-2xl border border-neutral-200 bg-neutral-50 p-4"
                    >
                        <div class="mb-2 inline-flex rounded-lg bg-white p-2">
                            <Users class="h-5 w-5 text-sky-600" />
                        </div>
                        <p class="text-sm font-medium">Ramah semua usia</p>
                        <p
                            class="mt-1 text-xs leading-relaxed text-neutral-600"
                        >
                            UI dibuat kontras, simpel, dan nyaman agar mudah
                            dipahami semua pemain.
                        </p>
                    </div>
                </div>
            </section>

            <section class="mt-10 space-y-4">
                <div class="flex items-end justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-semibold">Game tersedia</h2>
                        <p class="text-sm text-neutral-600">
                            Pilih game favorit Anda dan mulai bermain.
                        </p>
                    </div>
                    <Button as-child variant="ghost">
                        <Link href="/games">Lihat katalog</Link>
                    </Button>
                </div>

                <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                    <GameCard
                        v-for="game in games"
                        :key="game.id"
                        :game="game"
                    />
                </div>
            </section>
        </main>
    </div>
</template>
