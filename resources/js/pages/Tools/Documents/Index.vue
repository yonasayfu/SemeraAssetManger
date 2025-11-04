<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head, Link } from '@inertiajs/vue3';
import type { AssetDocument } from '@/types';

const props = defineProps<{ documents: AssetDocument[] }>();

const placeholder = '/storage/placeholders/placeholder.svg';
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
                <div
                    v-for="doc in props.documents"
                    :key="doc.id"
                    class="group flex items-start gap-3 rounded-xl border border-slate-200/70 bg-white/80 p-4 shadow-sm transition hover:-translate-y-0.5 hover:shadow-md dark:border-slate-800/60 dark:bg-slate-900/60"
                >
                    <img
                        :src="doc.mime_type?.startsWith('image/') ? ('/storage/' + doc.file_path) : placeholder"
                        @error="(e: any) => (e.target.src = placeholder)"
                        class="h-16 w-16 flex-shrink-0 rounded-md object-cover ring-1 ring-slate-200/70 dark:ring-slate-700/60"
                        alt="Preview"
                    />
                    <div class="min-w-0">
                        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                            <a :href="'/storage/' + doc.file_path" target="_blank" class="hover:underline">
                                {{ doc.title || 'Untitled Document' }}
                            </a>
                        </div>
                        <div class="mt-0.5 text-xs text-slate-500 dark:text-slate-400">
                            <span>{{ doc.mime_type || 'Unknown type' }}</span>
                            <span v-if="doc.asset"> · Asset {{ doc.asset?.asset_tag }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-else class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-slate-300/70 bg-slate-50/70 p-10 text-center dark:border-slate-700/60 dark:bg-slate-900/40">
                <img :src="placeholder" alt="placeholder" class="mb-3 h-16 w-16 opacity-70" />
                <p class="text-sm text-slate-600 dark:text-slate-300">No documents found.</p>
                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">Upload asset documents to see them here.</p>
            </div>
        </div>
    </AppLayout>
    
</template>

