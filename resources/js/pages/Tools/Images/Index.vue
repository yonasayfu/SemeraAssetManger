<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head } from '@inertiajs/vue3';
import type { Asset } from '@/types';

const props = defineProps<{ assets: Asset[] }>();
const placeholder = '/storage/placeholders/placeholder.svg';

const onImgError = (e: Event) => {
    const target = e.target as HTMLImageElement;
    target.src = placeholder;
};
</script>

<template>
    <Head title="Images Gallery" />
    <AppLayout :breadcrumbs="[{ title: 'Tools', href: '/tools' }, { title: 'Images', href: '/tools/images' }]">
        <ResourceToolbar
            title="Images Gallery"
            description="Visual gallery for assets with photos. Click any image to view full size."
            :show-create="false"
            :show-print="false"
            :show-export="false"
        />

        <div class="mx-auto mt-6 w-full max-w-7xl px-4 pb-12">
            <div v-if="props.assets.length" class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-4">
                <a
                    v-for="asset in props.assets"
                    :key="asset.id"
                    :href="'/storage/' + asset.photo"
                    target="_blank"
                    class="group relative overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800/60 dark:bg-slate-900/60"
                >
                    <img
                        :src="'/storage/' + asset.photo"
                        @error="onImgError"
                        class="h-48 w-full object-cover transition group-hover:scale-[1.02]"
                        alt="Asset photo"
                    />
                    <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-2 bg-gradient-to-t from-black/60 to-transparent px-3 py-2 text-xs text-white">
                        <span class="font-semibold">{{ asset.asset_tag }}</span>
                        <span class="opacity-80">#{{ asset.id }}</span>
                    </div>
                </a>
            </div>

            <div v-else class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300/70 bg-slate-50/70 p-10 text-center dark:border-slate-700/60 dark:bg-slate-900/40">
                <img :src="placeholder" alt="placeholder" class="mb-3 h-16 w-16 opacity-70" />
                <p class="text-sm text-slate-600 dark:text-slate-300">No images found.</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Add photos to assets to populate the gallery.</p>
            </div>
        </div>
    </AppLayout>
</template>

