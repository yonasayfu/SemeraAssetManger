<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

interface AlertItem {
  id: number;
  type?: string;
  message?: string | null;
  due_date?: string | null;
  sent?: boolean;
  asset?: { id: number; asset_tag: string } | null;
}

const props = defineProps<{
  title: string;
  alerts: { data: AlertItem[] } | any;
}>();

function formatDate(d?: string | null) {
  if (!d) return '—';
  try { return new Date(d).toLocaleString(); } catch { return d as string; }
}
</script>

<template>
  <Head :title="title" />
  <AppLayout :breadcrumbs="[{ title: 'Alerts', href: '/alerts' }, { title, href: '#' }]">
    <div class="p-4 sm:p-6 lg:p-8">
      <h1 class="mb-4 text-2xl font-semibold text-slate-900 dark:text-slate-100">{{ title }}</h1>
      <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
          <thead class="bg-slate-50 dark:bg-slate-800/60">
            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
              <th class="px-4 py-3">Asset</th>
              <th class="px-4 py-3">Message</th>
              <th class="px-4 py-3">Due</th>
              <th class="px-4 py-3">Sent</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-if="!alerts?.data?.length">
              <td colspan="4" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No alerts found.</td>
            </tr>
            <tr v-for="a in alerts.data" :key="a.id" class="bg-white/70 text-slate-700 dark:bg-slate-900/50 dark:text-slate-200">
              <td class="px-4 py-3">
                <template v-if="a.asset">
                  <Link :href="`/assets/${a.asset.id}`" class="text-indigo-600 hover:underline dark:text-indigo-300">{{ a.asset.asset_tag }}</Link>
                </template>
                <template v-else>—</template>
              </td>
              <td class="px-4 py-3">{{ a.message ?? a.type ?? '—' }}</td>
              <td class="px-4 py-3">{{ formatDate(a.due_date) }}</td>
              <td class="px-4 py-3">
                <span :class="['inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold uppercase tracking-wide', a.sent ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300']">{{ a.sent ? 'Sent' : 'Pending' }}</span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

