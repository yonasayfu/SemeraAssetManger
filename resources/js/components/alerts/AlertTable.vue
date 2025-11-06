<script setup lang="ts">
import { Link } from '@inertiajs/vue3'

interface AssetRef {
  id: number
  asset_tag?: string
  description?: string
}

interface AlertItem {
  id: number
  type: string
  message: string
  due_date?: string
  asset?: AssetRef
}

interface PaginationLink { url: string|null; label: string; active: boolean }

const props = defineProps<{ alerts: { data: AlertItem[]; links?: PaginationLink[] } }>()

const rows = () => props.alerts?.data ?? []
const links = () => props.alerts?.links ?? []

function formatDate(value?: string) {
  if (!value) return '—'
  try {
    const d = new Date(value)
    if (isNaN(d.getTime())) return value
    return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(d)
  } catch {
    return value
  }
}
</script>

<template>
  <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
    <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-800">
      <thead class="bg-slate-50/80 dark:bg-slate-800/40">
        <tr>
          <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Type</th>
          <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Asset</th>
          <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Message</th>
          <th class="px-4 py-3 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Due</th>
          <th class="px-4 py-3 text-right font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">Actions</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
        <tr v-if="rows().length === 0">
          <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">
            No alerts found.
          </td>
        </tr>
        <tr v-for="item in rows()" :key="item.id" class="bg-white/70 dark:bg-slate-900/50">
          <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ item.type }}</td>
          <td class="px-4 py-3 text-slate-700 dark:text-slate-200">
            <div class="font-medium">{{ item.asset?.asset_tag || '—' }}</div>
            <div class="text-xs text-slate-500 dark:text-slate-400">{{ item.asset?.description || '' }}</div>
          </td>
          <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ item.message }}</td>
          <td class="px-4 py-3 text-slate-700 dark:text-slate-200">{{ formatDate(item.due_date) }}</td>
          <td class="px-4 py-3 text-right">
            <Link v-if="item.asset?.id" :href="`/assets/${item.asset.id}`" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm transition hover:bg-indigo-500">
              View Asset
            </Link>
          </td>
        </tr>
      </tbody>
    </table>

    <div v-if="links().length" class="flex items-center justify-end gap-1 border-t border-slate-200/70 bg-white/60 px-3 py-2 dark:border-slate-800/60 dark:bg-slate-900/40">
      <template v-for="(link, idx) in links()" :key="idx">
        <Link
          v-if="link.url"
          :href="link.url"
          v-html="link.label"
          class="px-2 py-1 text-xs rounded-md border border-slate-200/70 dark:border-slate-700/60"
          :class="{ 'bg-indigo-600 text-white border-indigo-600': link.active }"
        />
        <span v-else v-html="link.label" class="px-2 py-1 text-xs rounded-md text-slate-400" />
      </template>
    </div>
  </div>
</template>
