<script setup lang="ts">
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, onBeforeUnmount, onMounted, ref } from 'vue';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { login } from '@/routes';

type GameConfig = {
    durationSeconds: number;
    maxNumber: number;
};

defineProps<{
    seo: {
        title: string;
        description: string;
        canonical: string;
        ogImage: string;
    };
    gameConfig: GameConfig;
}>();

const score = ref(0);
const timeLeft = ref(60);
const answer = ref('');
const feedback = ref('Ayo mulai!');
const currentQuestion = ref({
    left: 0,
    right: 0,
    operator: '+' as '+' | '-' | '*',
});
const gameStarted = ref(false);
const gameFinished = ref(false);
const historySaved = ref(false);

const page = usePage();
const isLoggedIn = computed(() => Boolean(page.props.auth?.user));

let timer: ReturnType<typeof setInterval> | null = null;

const expectedAnswer = computed(() => {
    if (currentQuestion.value.operator === '+') {
        return currentQuestion.value.left + currentQuestion.value.right;
    }

    if (currentQuestion.value.operator === '-') {
        return currentQuestion.value.left - currentQuestion.value.right;
    }

    return currentQuestion.value.left * currentQuestion.value.right;
});

function randomQuestion(maxNumber: number) {
    const operators: Array<'+' | '-' | '*'> = ['+', '-', '*'];
    const operator = operators[Math.floor(Math.random() * operators.length)];
    const left = Math.floor(Math.random() * maxNumber) + 1;
    const right = Math.floor(Math.random() * maxNumber) + 1;

    currentQuestion.value = {
        left,
        right,
        operator,
    };
}

function startGame(durationSeconds: number, maxNumber: number) {
    score.value = 0;
    timeLeft.value = durationSeconds;
    answer.value = '';
    feedback.value = 'Game dimulai. Semangat!';
    gameStarted.value = true;
    gameFinished.value = false;
    historySaved.value = false;
    randomQuestion(maxNumber);

    if (timer) {
        clearInterval(timer);
    }

    timer = setInterval(() => {
        if (timeLeft.value <= 1) {
            gameFinished.value = true;
            gameStarted.value = false;
            feedback.value = `Waktu habis. Skor akhir: ${score.value}`;

            if (timer) {
                clearInterval(timer);
                timer = null;
            }

            saveSession();

            return;
        }

        timeLeft.value -= 1;
    }, 1000);
}

function submitAnswer(maxNumber: number) {
    if (!gameStarted.value || gameFinished.value) {
        return;
    }

    const parsed = Number(answer.value);

    if (!Number.isFinite(parsed)) {
        feedback.value = 'Masukkan angka yang valid.';

        return;
    }

    if (parsed === expectedAnswer.value) {
        score.value += 10;
        feedback.value = 'Benar! +10 poin';
    } else {
        score.value = Math.max(0, score.value - 3);
        feedback.value = `Kurang tepat. Jawaban benar: ${expectedAnswer.value}`;
    }

    answer.value = '';
    randomQuestion(maxNumber);
}

function saveSession() {
    if (!isLoggedIn.value || historySaved.value) {
        return;
    }

    router.post(
        '/games/sessions',
        {
            game_slug: 'math-quick-challenge',
            score: score.value,
            duration_seconds: 60 - timeLeft.value,
            metadata: {
                source: 'math-page',
            },
        },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                historySaved.value = true;
            },
        },
    );
}

onMounted(() => {
    startGame(60, 20);
});

onBeforeUnmount(() => {
    if (timer) {
        clearInterval(timer);
        timer = null;
    }
});
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

    <div
        class="min-h-screen bg-[linear-gradient(180deg,#f8fcff_0%,#fffaf2_100%)]"
    >
        <header
            class="mx-auto flex w-full max-w-5xl items-center justify-between px-6 py-5"
        >
            <Link href="/" class="text-base font-semibold"
                >Minigames Indonesia</Link
            >
            <div class="flex items-center gap-2">
                <Button as-child variant="outline">
                    <Link href="/games">Lihat game lain</Link>
                </Button>
                <Button as-child>
                    <Link href="/games/leaderboard">Leaderboard</Link>
                </Button>
            </div>
        </header>

        <main class="mx-auto w-full max-w-5xl px-6 pb-16">
            <section
                class="mb-6 rounded-2xl border border-neutral-200 bg-white p-6 shadow-sm"
            >
                <h1 class="text-2xl font-semibold">Math Quick Challenge</h1>
                <p class="mt-2 text-sm text-muted-foreground">
                    Jawab soal matematika secepat mungkin. Benar dapat +10,
                    salah -3 (minimal 0).
                </p>
            </section>

            <div class="grid gap-6 md:grid-cols-[1.1fr_0.9fr]">
                <Card class="border-neutral-200">
                    <CardHeader>
                        <CardTitle class="text-xl">Soal Saat Ini</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-5">
                        <p class="text-4xl font-semibold tracking-tight">
                            {{ currentQuestion.left }}
                            {{ currentQuestion.operator }}
                            {{ currentQuestion.right }} = ?
                        </p>

                        <div class="space-y-2">
                            <Label for="answer">Jawaban</Label>
                            <Input
                                id="answer"
                                v-model="answer"
                                type="number"
                                inputmode="numeric"
                                placeholder="Tulis jawaban Anda"
                                @keyup.enter="
                                    submitAnswer(gameConfig.maxNumber)
                                "
                            />
                        </div>

                        <div class="flex flex-wrap gap-3">
                            <Button
                                @click="submitAnswer(gameConfig.maxNumber)"
                                :disabled="gameFinished"
                            >
                                Kirim jawaban
                            </Button>
                            <Button
                                variant="outline"
                                @click="
                                    startGame(
                                        gameConfig.durationSeconds,
                                        gameConfig.maxNumber,
                                    )
                                "
                            >
                                Ulangi game
                            </Button>
                        </div>

                        <p
                            class="text-sm"
                            :class="
                                gameFinished
                                    ? 'text-amber-700'
                                    : 'text-muted-foreground'
                            "
                        >
                            {{ feedback }}
                        </p>
                    </CardContent>
                </Card>

                <Card class="border-neutral-200">
                    <CardHeader>
                        <CardTitle class="text-xl">Statistik</CardTitle>
                    </CardHeader>
                    <CardContent class="space-y-4">
                        <div class="rounded-xl bg-neutral-50 p-4">
                            <p
                                class="text-xs tracking-wide text-muted-foreground uppercase"
                            >
                                Skor
                            </p>
                            <p class="mt-1 text-3xl font-semibold">
                                {{ score }}
                            </p>
                        </div>
                        <div class="rounded-xl bg-neutral-50 p-4">
                            <p
                                class="text-xs tracking-wide text-muted-foreground uppercase"
                            >
                                Waktu tersisa
                            </p>
                            <p class="mt-1 text-3xl font-semibold">
                                {{ timeLeft }}s
                            </p>
                        </div>
                        <p
                            class="text-xs leading-relaxed text-muted-foreground"
                        >
                            Tips: fokus pada operasi sederhana dulu. Kecepatan
                            dan konsistensi akan meningkatkan skor.
                        </p>

                        <p
                            v-if="!isLoggedIn"
                            class="text-xs text-muted-foreground"
                        >
                            Ingin menyimpan history skor akun?
                            <Link
                                :href="login()"
                                class="underline underline-offset-4"
                                >Masuk dengan akun Anda</Link
                            >.
                        </p>
                    </CardContent>
                </Card>
            </div>
        </main>
    </div>
</template>
