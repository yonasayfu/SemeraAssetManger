<script setup lang="ts">
import { computed } from 'vue';

interface ActivityItem {
    id: number | string;
    action: string;
    description?: string | null;
    created_at?: string | null;
    causer?: {
        id?: number | string | null;
        name?: string | null;
        email?: string | null;
    } | null;
}

const props = withDefaults(
    defineProps<{
        data: { activity?: ActivityItem[] } | null;
        loading: boolean;
    }>(),
    {
        data: null,
        loading: false,
    },
);

const activity = computed<ActivityItem[]>(() => props.data?.activity ?? []);
</script>

<template>
    <div>
        <div v-if="loading" class="space-y-3">
            <div v-for="i in 5" :key="i" class="h-14 animate-pulse rounded-xl bg-slate-200/70 dark:bg-slate-700/50" />
        </div>

        <div v-else-if="!activity.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 text-sm text-slate-500 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 dark:text-slate-300">
            No activity has been logged for this asset yet.
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        <th class="px-4 py-3">Action</th>
                        <th class="px-4 py-3">Description</th>
                        <th class="px-4 py-3">User</th>
                        <th class="px-4 py-3">When</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr
                        v-for="entry in activity"
                        :key="entry.id"
                        class="bg-white/60 text-slate-700 dark:bg-slate-900/50 dark:text-slate-200"
                    >
                        <td class="px-4 py-3 font-medium">{{ entry.action }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">{{ entry.description ?? '—' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            <div v-if="entry.causer">
                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ entry.causer.name ?? 'Unknown user' }}</span>
                                <div v-if="entry.causer.email" class="text-[11px] text-slate-500 dark:text-slate-400">
                                    {{ entry.causer.email }}
                                </div>
                            </div>
                            <span v-else>—</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ entry.created_at ? new Date(entry.created_at).toLocaleString() : '—' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
