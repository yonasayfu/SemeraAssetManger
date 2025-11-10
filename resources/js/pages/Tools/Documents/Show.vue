<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head, Link } from '@inertiajs/vue3';

interface AssetLite { id: number | string; asset_tag: string; description?: string | null }
interface DocLite {
  id: number | string
  title?: string | null
  mime_type?: string | null
  url?: string | null
  asset?: AssetLite | null
}

const props = defineProps<{ document: DocLite }>()
const isImage = (d: DocLite) => (d.mime_type || '').startsWith('image/')
const isPdf = (d: DocLite) => (d.mime_type || '').toLowerCase().includes('pdf')
</script>

<template>
  <Head :title="`Document · ${document.title || '#' + document.id}`" />
  <AppLayout :breadcrumbs="[{ title: 'Tools', href: '/tools' }, { title: 'Documents', href: '/tools/documents' }, { title: document.title || ('#' + document.id), href: `/tools/documents/${document.id}` }]">
    <ResourceToolbar
      :title="`Document · ${document.title || '#' + document.id}`"
      :description="document.mime_type || ''"
      :show-create="false"
      :show-export="false"
      :show-print="false"
    />

    <div class="mx-auto mt-6 w-full max-w-5xl px-4 pb-12">
      <div class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <div class="bg-slate-100 dark:bg-slate-800">
          <img v-if="isImage(document) && document.url" :src="document.url" alt="Document image" class="mx-auto w-full max-w-full object-contain md:max-h-[70vh]" />
          <iframe v-else-if="isPdf(document) && document.url" :src="document.url" class="h-[70vh] w-full" />
          <div v-else class="flex h-[40vh] items-center justify-center text-slate-600 dark:text-slate-300">
            Preview unavailable. Use Download to view.
          </div>
        </div>
        <div class="flex items-center justify-between gap-3 px-4 py-3 text-sm">
          <div class="min-w-0">
            <div class="truncate font-semibold text-slate-900 dark:text-slate-100">{{ document.title || ('Document #' + document.id) }}</div>
            <div class="truncate text-slate-600 dark:text-slate-300">{{ document.mime_type || 'Unknown type' }}</div>
            <div v-if="document.asset" class="truncate text-slate-500 dark:text-slate-400">
              For asset: <Link :href="`/assets/${document.asset.id}`" class="underline">{{ document.asset.asset_tag }}</Link>
            </div>
          </div>
          <div class="flex flex-shrink-0 items-center gap-2">
            <a v-if="document.url" :href="document.url" target="_blank" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Download</a>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>

