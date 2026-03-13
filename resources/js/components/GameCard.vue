<script setup lang="ts">
import { Link } from '@inertiajs/vue3';
import { Play } from 'lucide-vue-next';
import { Badge } from '@/components/ui/badge';
import { Button } from '@/components/ui/button';
import {
    Card,
    CardContent,
    CardDescription,
    CardFooter,
    CardHeader,
    CardTitle,
} from '@/components/ui/card';

type GameItem = {
    id: number;
    slug: string;
    title: string;
    short_description: string;
    category: string;
    difficulty: string;
    play_count: number;
    route_path: string | null;
};

defineProps<{
    game: GameItem;
}>();
</script>

<template>
    <Card class="h-full border-neutral-200/80 shadow-sm">
        <CardHeader class="space-y-2">
            <div class="flex items-center justify-between gap-2">
                <Badge variant="outline" class="capitalize">{{
                    game.category
                }}</Badge>
                <span class="text-xs text-muted-foreground capitalize">{{
                    game.difficulty
                }}</span>
            </div>
            <CardTitle class="text-lg">{{ game.title }}</CardTitle>
            <CardDescription class="line-clamp-2 leading-relaxed">
                {{ game.short_description }}
            </CardDescription>
        </CardHeader>

        <CardContent>
            <p class="text-xs text-muted-foreground">
                {{ game.play_count }} dimainkan
            </p>
        </CardContent>

        <CardFooter>
            <Button as-child class="w-full" :disabled="!game.route_path">
                <Link :href="game.route_path || '/games'">
                    <Play class="mr-2 h-4 w-4" />
                    {{ game.route_path ? 'Main sekarang' : 'Segera hadir' }}
                </Link>
            </Button>
        </CardFooter>
    </Card>
</template>
