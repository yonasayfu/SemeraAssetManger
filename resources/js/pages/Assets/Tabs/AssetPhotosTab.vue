<script setup lang="ts">
import { computed } from 'vue';

interface PhotoItem {
    id: number | string;
    caption?: string | null;
    url: string;
    uploaded_at?: string | null;
}

const props = withDefaults(
    defineProps<{
        data: { photos?: PhotoItem[] } | null;
        loading: boolean;
    }>(),
    {
        data: null,
        loading: false,
    },
);

const photos = computed<PhotoItem[]>(() => props.data?.photos ?? []);
</script>

<template>
    <div>
        <div v-if="loading" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="i in 6" :key="i" class="h-48 animate-pulse rounded-2xl bg-slate-200/70 dark:bg-slate-700/50" />
        </div>

        <div v-else-if="!photos.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 text-sm text-slate-500 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 dark:text-slate-300">
            No photos have been uploaded for this asset yet.
        </div>

        <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="photo in photos"
                :key="photo.id"
                class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60"
            >
                <div class="h-48 w-full overflow-hidden bg-slate-200/80 dark:bg-slate-800/40">
                    <template v-if="photo.url">
                        <img :src="photo.url" :alt="photo.caption ?? 'Asset photo'" class="h-full w-full object-cover" />
                    </template>
                    <template v-else>
                        <div class="flex h-full w-full items-center justify-center text-xs text-slate-500 dark:text-slate-300">
                            No image available
                        </div>
                    </template>
                </div>
                <div class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300">
                    <p class="font-medium text-slate-700 dark:text-slate-100">{{ photo.caption ?? 'Untitled photo' }}</p>
                    <p class="mt-1 text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        {{ photo.uploaded_at ? new Date(photo.uploaded_at).toLocaleString() : 'Upload date unknown' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
