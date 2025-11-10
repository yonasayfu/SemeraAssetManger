<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { AssetDocument } from '@/types';

const props = defineProps<{ documents: AssetDocument[] }>();

const placeholder = '/storage/placeholders/placeholder.svg';

const selected = ref<AssetDocument | null>(null);
const docUrl = (d: AssetDocument) => {
  const p = (d as any).file_path as string | null | undefined;
  if (!p) return '#';
  return p.startsWith('http') || p.startsWith('/storage/') ? p : `/storage/${p}`;
};
const isImage = (d: AssetDocument) => ((d as any).mime_type || '').startsWith('image/');
const isPdf = (d: AssetDocument) => ((d as any).mime_type || '').toLowerCase().includes('pdf');
const open = (d: AssetDocument) => { selected.value = d; };
const close = () => { selected.value = null; };
</script>

<template>
    <Head title="Documents Gallery" />
    <AppLayout :breadcrumbs="[{ title: 'Tools', href: '/tools' }, { title: 'Documents', href: '/tools/documents' }]">
        <ResourceToolbar
            title="Documents Gallery"
            description="Browse uploaded documents linked to assets. Click to open in a new tab."
            :show-create="false"
            :show-print="false"
            :show-export="false"
        />

        <div class="mx-auto mt-6 w-full max-w-6xl px-4 pb-12">
            <div v-if="props.documents.length" class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <button
                    v-for="doc in props.documents"
                    :key="doc.id"
                    type="button"
                    @click="open(doc)"
                    class="group flex items-start gap-3 rounded-xl border border-slate-200/70 bg-white/80 p-4 text-left shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800/60 dark:bg-slate-900/60"
                >
                    <img
                        :src="doc.mime_type?.startsWith('image/') ? docUrl(doc) : placeholder"
                        @error="(e: any) => (e.target.src = placeholder)"
                        class="h-16 w-16 flex-shrink-0 rounded-md object-cover ring-1 ring-slate-200/70 dark:ring-slate-700/60"
                        alt="Preview"
                    />
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                            <span class="hover:underline">
                                {{ (doc as any).title || 'Untitled Document' }}
                            </span>
                        </div>
                        <div class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            <span>{{ (doc as any).mime_type || 'Unknown type' }}</span>
                            <span v-if="(doc as any).asset"> · Asset {{ (doc as any).asset?.asset_tag }}</span>
                        </div>
                    </div>
                </button>
            </div>

            <div v-else class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300/70 bg-slate-50/70 p-10 text-center dark:border-slate-700/60 dark:bg-slate-900/40">
                <img :src="placeholder" alt="placeholder" class="mb-3 h-16 w-16 opacity-70" />
                <p class="text-sm text-slate-600 dark:text-slate-300">No documents found.</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Upload asset documents to see them here.</p>
            </div>
        </div>

        <!-- Lightbox / Preview -->
        <div v-if="selected" class="fixed inset-0 z-50 flex items-center justify-center bg-black/70 p-4" @click.self="close">
            <div class="relative max-h-[90vh] w-full max-w-5xl overflow-hidden rounded-2xl bg-white shadow-2xl dark:bg-slate-900">
                <button @click="close" class="absolute right-2 top-2 rounded-md bg-black/50 px-2 py-1 text-xs text-white hover:bg-black/70">Close</button>
                <div class="w-full bg-slate-100 dark:bg-slate-800">
                    <img v-if="isImage(selected)" :src="docUrl(selected)" alt="Document image" class="max-h-[70vh] w-full object-contain" />
                    <iframe v-else-if="isPdf(selected)" :src="docUrl(selected)" class="h-[70vh] w-full" />
                    <div v-else class="flex h-[40vh] items-center justify-center text-slate-600 dark:text-slate-300">
                        Preview unavailable. Use Download to view.
                    </div>
                </div>
                <div class="flex items-center justify-between gap-3 px-4 py-3 text-sm">
                    <div class="min-w-0">
                        <div class="truncate font-semibold text-slate-900 dark:text-slate-100">{{ (selected as any).title || 'Untitled Document' }}</div>
                        <div class="truncate text-slate-600 dark:text-slate-300">{{ (selected as any).mime_type || 'Unknown type' }}</div>
                    </div>
                    <div class="flex flex-shrink-0 items-center gap-2">
                        <Link :href="`/tools/documents/${(selected as any).id}`" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">Details</Link>
                        <a :href="docUrl(selected)" target="_blank" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Download</a>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
    
</template>
