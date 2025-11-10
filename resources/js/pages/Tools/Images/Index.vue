<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Asset } from '@/types';

const props = defineProps<{ assets: Asset[] }>();
const placeholder = '/storage/placeholders/placeholder.svg';

const onImgError = (e: Event) => {
    const target = e.target as HTMLImageElement;
    target.src = placeholder;
};

const selected = ref<Asset | null>(null);
const open = (asset: Asset) => { selected.value = asset; };
const close = () => { selected.value = null; };
const photoSrc = (asset: Asset) => {
    const p = (asset as any).photo as string | null | undefined;
    if (!p) return placeholder;
    return p.startsWith('http') || p.startsWith('/storage/') ? p : `/storage/${p}`;
};
const hasRealPhoto = (asset: Asset | null) => !!(asset && (asset as any).photo);
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
                <button
                    v-for="asset in props.assets"
                    :key="asset.id"
                    type="button"
                    @click="open(asset)"
                    class="group relative overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800/60 dark:bg-slate-900/60"
                >
                    <img
                        :src="photoSrc(asset)"
                        @error="onImgError"
                        class="h-48 w-full object-cover transition group-hover:scale-[1.02]"
                        alt="Asset photo"
                    />
                    <div class="absolute inset-x-0 bottom-0 flex items-center justify-between gap-2 bg-gradient-to-t from-black/60 to-transparent px-3 py-2 text-xs text-white">
                        <span class="font-semibold">{{ asset.asset_tag }}</span>
                        <span class="opacity-80">#{{ asset.id }}</span>
                    </div>
                </button>
            </div>

            <div v-else class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300/70 bg-slate-50/70 p-10 text-center dark:border-slate-700/60 dark:bg-slate-900/40">
                <img :src="placeholder" alt="placeholder" class="mb-3 h-16 w-16 opacity-70" />
                <p class="text-sm text-slate-600 dark:text-slate-300">No images found.</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Add photos to assets to populate the gallery.</p>
            </div>
        </div>
        <!-- Lightbox -->
        <div v-if="selected" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4" @click.self="close">
            <div class="relative max-h-[90vh] w-full max-w-4xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-slate-900">
                <button @click="close" class="absolute right-2 top-2 rounded-md bg-black/50 px-2 py-1 text-xs text-white hover:bg-black/70">Close</button>
                <div class="aspect-[16/9] w-full bg-slate-100 dark:bg-slate-800">
                    <img :src="photoSrc(selected)" alt="Asset image" class="h-full w-full object-contain" />
                </div>
                <div class="flex items-center justify-between gap-3 px-4 py-3 text-sm">
                    <div class="min-w-0">
                        <div class="truncate font-semibold text-slate-900 dark:text-slate-100">{{ selected.asset_tag }}</div>
                        <div class="truncate text-slate-600 dark:text-slate-300">{{ (selected as any).description }}</div>
                    </div>
                    <div class="flex flex-shrink-0 items-center gap-2">
                        <Link :href="`/tools/images/${selected.id}`" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">Details</Link>
                        <a v-if="hasRealPhoto(selected)" :href="photoSrc(selected)" target="_blank" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Open original</a>
                        <Link v-else :href="`/assets/${selected.id}`" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Open Asset</Link>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
