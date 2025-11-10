<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue'
import ResourceToolbar from '@/components/ResourceToolbar.vue'
import { Head, Link } from '@inertiajs/vue3'

const props = defineProps<{ groups: Array<{ label: string; type: string; items: any[] }> }>()

const printCurrent = () => {
  const t = document.title
  document.title = 'Contracts Board'
  window.print()
  document.title = t
}
</script>

<template>
  <Head title="Contracts Board" />
  <AppLayout title="Contracts Board">
    <div class="hidden print:block text-center text-slate-800">
      <img :src="(usePage().props as any).branding?.logo_url || '/images/asset-logo.svg'" :alt="(usePage().props as any).branding?.name || 'Asset Management'" class="mx-auto mb-3 h-12 w-auto print-logo" />
      <h1 class="text-xl font-semibold">{{ (usePage().props as any).branding?.name || 'Asset Management' }}</h1>
      <p class="text-sm">Contracts Board</p>
      <p class="text-xs text-slate-500">Printed {{ new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date()) }}</p>
      <hr class="print-divider" />
    </div>
    <ResourceToolbar title="Contracts Board" description="Unified view across all contract types." :show-export="false" :show-print="true" @print="printCurrent" />

    <div class="hidden print:block text-center text-slate-800">
      <img src="/images/asset-logo.svg" alt="Asset Management" class="mx-auto mb-3 h-12 w-auto print-logo" />
      <h1 class="text-xl font-semibold">Asset Management</h1>
      <p class="text-sm">Contracts Board</p>
      <p class="text-xs text-slate-500">Printed {{ new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date()) }}</p>
      <hr class="print-divider" />
    </div>

    <div class="mx-auto mt-6 w-full max-w-6xl px-4 pb-12">
      <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div v-for="col in props.groups" :key="col.type" class="rounded-xl border border-slate-200/70 bg-white/80 p-4 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
          <div class="mb-3 text-sm font-semibold text-slate-700 dark:text-slate-200">{{ col.label }}</div>
          <div class="space-y-2">
            <div v-if="!col.items.length" class="text-xs text-slate-500 dark:text-slate-400">No contracts</div>
            <div v-for="c in col.items" :key="c.id" class="rounded-lg border border-slate-200/60 bg-white/90 p-3 text-xs dark:border-slate-800/60 dark:bg-slate-900/70">
              <div class="flex items-center justify-between">
                <div class="font-medium text-slate-800 dark:text-slate-100">{{ c.vendor || 'Vendor' }}</div>
                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-medium text-slate-600 dark:bg-slate-800 dark:text-slate-300">{{ c.status || '—' }}</span>
              </div>
              <div class="mt-1 text-slate-600 dark:text-slate-300">{{ c.product || 'Product' }}</div>
              <div class="mt-1 flex items-center justify-between text-slate-600 dark:text-slate-300">
                <span>End: {{ c.end_at ? new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(c.end_at)) : '—' }}</span>
                <span v-if="c.asset_tag" class="text-indigo-600 dark:text-indigo-300">{{ c.asset_tag }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
