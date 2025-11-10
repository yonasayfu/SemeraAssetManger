<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head, Link } from '@inertiajs/vue3';

interface AssetLite {
  id: number | string
  asset_tag: string
  description?: string | null
  photo?: string | null
}

const props = defineProps<{ asset: AssetLite }>()
const placeholder = '/storage/placeholders/placeholder.svg'
const photoSrc = (a: AssetLite) => {
  const p = a.photo
  if (!p) return placeholder
  return p.startsWith('http') || p.startsWith('/storage/') ? p : `/storage/${p}`
}
const hasRealPhoto = !!props.asset.photo
</script>

<template>
  <Head :title="`Image · ${asset.asset_tag}`" />
  <AppLayout :breadcrumbs="[{ title: 'Tools', href: '/tools' }, { title: 'Images', href: '/tools/images' }, { title: asset.asset_tag, href: `/tools/images/${asset.id}` }]">
    <ResourceToolbar
      :title="`Image · ${asset.asset_tag}`"
      :description="asset.description ?? ''"
      :show-create="false"
      :show-export="false"
      :show-print="false"
    />

    <div class="mx-auto mt-6 w-full max-w-5xl px-4 pb-12">
      <div class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <div class="bg-slate-100 dark:bg-slate-800">
          <img :src="photoSrc(asset)" alt="Asset image" class="mx-auto w-full max-w-full object-contain md:max-h-[70vh]" />
        </div>
        <div class="flex items-center justify-between gap-3 px-4 py-3 text-sm">
          <div class="min-w-0">
            <div class="truncate font-semibold text-slate-900 dark:text-slate-100">{{ asset.asset_tag }}</div>
            <div class="truncate text-slate-600 dark:text-slate-300">{{ asset.description }}</div>
          </div>
          <div class="flex flex-shrink-0 items-center gap-2">
            <Link :href="`/assets/${asset.id}`" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-800">Open Asset</Link>
            <a v-if="hasRealPhoto" :href="photoSrc(asset)" target="_blank" class="rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-indigo-500">Open original</a>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
