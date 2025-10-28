<script setup lang="ts">
import { computed } from 'vue';

interface AuditItem {
    id: number | string;
    found?: boolean;
    notes?: string | null;
    checked_at?: string | null;
    audit?: {
        id?: number | string | null;
        name?: string | null;
        status?: string | null;
        site?: string | null;
        location?: string | null;
        started_at?: string | null;
        completed_at?: string | null;
    } | null;
}

const props = withDefaults(
    defineProps<{
        data: { audits?: AuditItem[] } | null;
        loading: boolean;
    }>(),
    {
        data: null,
        loading: false,
    },
);

const audits = computed<AuditItem[]>(() => props.data?.audits ?? []);
</script>

<template>
    <div>
        <div v-if="loading" class="space-y-3">
            <div v-for="i in 4" :key="i" class="h-14 animate-pulse rounded-xl bg-slate-200/70 dark:bg-slate-700/50" />
        </div>

        <div v-else-if="!audits.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 text-sm text-slate-500 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 dark:text-slate-300">
            This asset has not been included in any audits yet.
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        <th class="px-4 py-3">Audit</th>
                        <th class="px-4 py-3">Site / Location</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3">Found?</th>
                        <th class="px-4 py-3">Checked</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr
                        v-for="entry in audits"
                        :key="entry.id"
                        class="bg-white/60 text-slate-700 dark:bg-slate-900/50 dark:text-slate-200"
                    >
                        <td class="px-4 py-3 font-medium">
                            {{ entry.audit?.name ?? 'Audit #' + entry.audit?.id ?? entry.id }}
                            <div v-if="entry.notes" class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                {{ entry.notes }}
                            </div>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ entry.audit?.site ?? '—' }} <span v-if="entry.audit?.location">/ {{ entry.audit?.location }}</span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ entry.audit?.status ?? '—' }}
                        </td>
                        <td class="px-4 py-3">
                            <span
                                :class="[
                                    'inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold uppercase tracking-wide',
                                    entry.found ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/20 dark:text-rose-200',
                                ]"
                            >
                                {{ entry.found ? 'Present' : 'Missing' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ entry.checked_at ? new Date(entry.checked_at).toLocaleString() : '—' }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

