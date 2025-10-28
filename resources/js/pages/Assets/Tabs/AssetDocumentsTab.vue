<script setup lang="ts">
import { computed } from 'vue';

interface DocumentItem {
    id: number | string;
    title: string;
    url?: string | null;
    mime_type?: string | null;
    uploaded_at?: string | null;
}

const props = withDefaults(
    defineProps<{
        data: { documents?: DocumentItem[] } | null;
        loading: boolean;
    }>(),
    {
        data: null,
        loading: false,
    },
);

const documents = computed<DocumentItem[]>(() => props.data?.documents ?? []);
</script>

<template>
    <div>
        <div v-if="loading" class="space-y-3">
            <div v-for="i in 5" :key="i" class="h-12 animate-pulse rounded-xl bg-slate-200/70 dark:bg-slate-700/50" />
        </div>

        <div v-else-if="!documents.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 text-sm text-slate-500 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 dark:text-slate-300">
            No documents have been uploaded for this asset.
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Uploaded</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr
                        v-for="document in documents"
                        :key="document.id"
                        class="bg-white/60 text-slate-700 dark:bg-slate-900/50 dark:text-slate-200"
                    >
                        <td class="px-4 py-3 font-medium">{{ document.title }}</td>
                        <td class="px-4 py-3 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            {{ document.mime_type ?? 'Unknown' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ document.uploaded_at ? new Date(document.uploaded_at).toLocaleString() : 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a
                                :href="document.url ?? '#'"
                                target="_blank"
                                rel="noopener noreferrer"
                                :class="[
                                    'inline-flex items-center rounded-full border border-indigo-500 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-600 transition hover:bg-indigo-50 focus:outline-none focus:ring-1 focus:ring-indigo-300 dark:border-indigo-400 dark:text-indigo-200 dark:hover:bg-indigo-500/10',
                                    !document.url ? 'pointer-events-none opacity-60' : '',
                                ]"
                            >
                                {{ document.url ? 'Download' : 'Unavailable' }}
                            </a>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
