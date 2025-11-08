<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head, Link } from '@inertiajs/vue3';

interface PageItem {
  id: number;
  slug: string;
  title: string;
  published?: boolean;
  updated_at?: string | null;
}

const props = defineProps<{ pages: PageItem[] }>();

function fmt(d?: string | null) {
  if (!d) return '—';
  try { return new Date(d).toLocaleString(); } catch { return d as string; }
}
</script>

<template>
  <Head title="Support Pages" />
  <AppLayout :breadcrumbs="[{ title: 'Help', href: '/help' }, { title: 'Pages', href: '/help/pages' }]">
    <ResourceToolbar title="Support Pages" description="Manage static Help & Support pages." :show-create="true">
      <template #actions>
        <Link href="/help/pages/create" class="btn-glass btn-variant-primary">Add Page</Link>
      </template>
    </ResourceToolbar>

    <div class="mx-auto mt-6 w-full max-w-5xl px-4 pb-12">
      <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
          <thead class="bg-slate-50 dark:bg-slate-800/60">
            <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
              <th class="px-4 py-3">Slug</th>
              <th class="px-4 py-3">Title</th>
              <th class="px-4 py-3">Published</th>
              <th class="px-4 py-3">Updated</th>
              <th class="px-4 py-3 text-right">Action</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
            <tr v-if="!pages.length">
              <td colspan="5" class="px-4 py-8 text-center text-slate-500 dark:text-slate-400">No pages found.</td>
            </tr>
            <tr v-for="p in pages" :key="p.id" class="bg-white/70 text-slate-700 dark:bg-slate-900/50 dark:text-slate-200">
              <td class="px-4 py-3">{{ p.slug }}</td>
              <td class="px-4 py-3">{{ p.title }}</td>
              <td class="px-4 py-3">
                <span :class="['inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold uppercase tracking-wide', p.published ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300']">{{ p.published ? 'Yes' : 'No' }}</span>
              </td>
              <td class="px-4 py-3">{{ fmt(p.updated_at) }}</td>
              <td class="px-4 py-3 text-right">
                <Link :href="`/help/pages/${p.id}/edit`" class="btn-glass btn-variant-secondary btn-glass-sm">Edit</Link>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
  
</template>

