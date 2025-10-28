<script setup lang="ts">
import { computed } from 'vue';

interface EventItem {
    id?: number | string;
    type: string;
    title: string;
    occurred_at: string | null;
    status?: string | null;
    meta?: Record<string, unknown>;
}

const props = withDefaults(
    defineProps<{
        data: { events?: EventItem[] } | null;
        loading: boolean;
    }>(),
    {
        data: null,
        loading: false,
    },
);

const events = computed<EventItem[]>(() => props.data?.events ?? []);
</script>

<template>
    <div>
        <div v-if="loading" class="space-y-3">
            <div v-for="i in 4" :key="i" class="h-14 animate-pulse rounded-xl bg-slate-200/70 dark:bg-slate-700/50" />
        </div>

        <div v-else-if="!events.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 text-sm text-slate-500 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 dark:text-slate-300">
            No lifecycle events recorded for this asset yet.
        </div>

        <ul v-else class="space-y-4">
            <li
                v-for="event in events"
                :key="event.id ?? `${event.type}-${event.occurred_at}`"
                class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60"
            >
                <div class="flex flex-col gap-1 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100">
                            {{ event.title }}
                        </p>
                        <p class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            {{ event.type }}
                        </p>
                    </div>
                    <div class="text-xs text-slate-500 dark:text-slate-400">
                        {{ event.occurred_at ? new Date(event.occurred_at).toLocaleString() : 'Unknown time' }}
                    </div>
                </div>
                <div v-if="event.status" class="mt-2 inline-flex rounded-full bg-indigo-500/10 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-200">
                    {{ event.status }}
                </div>
                <div v-if="event.meta && Object.keys(event.meta).length" class="mt-3 grid gap-2 sm:grid-cols-2">
                    <div
                        v-for="(value, key) in event.meta"
                        :key="`${key}-${value}`"
                        class="rounded-lg border border-slate-200/70 bg-slate-50/70 p-2 text-xs text-slate-600 dark:border-slate-700/60 dark:bg-slate-800/40 dark:text-slate-300"
                    >
                        <span class="font-semibold uppercase tracking-wide">{{ key.replace(/_/g, ' ') }}:</span>
                        <span class="ml-1">{{ value ?? '—' }}</span>
                    </div>
                </div>
            </li>
        </ul>
    </div>
</template>
