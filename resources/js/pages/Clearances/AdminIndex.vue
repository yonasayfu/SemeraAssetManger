<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps<{ clearances: any[] }>();
const fmt = (v?: string) => v ? new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(v)) : '—';
</script>

<template>
  <AppLayout :breadcrumbs="[{ title: 'Admin', href: '/admin' }, { title: 'Clearances', href: '/admin/clearances' }]">
    <Head title="Clearances (Admin)" />
    <div class="mx-auto mt-6 w-full max-w-6xl px-4 pb-12">
      <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 p-6 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-slate-600">
              <th class="py-2">ID</th>
              <th class="py-2">Staff</th>
              <th class="py-2">Status</th>
              <th class="py-2">Submitted</th>
              <th class="py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in props.clearances" :key="c.id" class="border-t border-slate-200/70">
              <td class="py-2">{{ c.id }}</td>
              <td class="py-2">{{ c.staff?.name ?? '—' }}</td>
              <td class="py-2">{{ c.status }}</td>
              <td class="py-2">{{ fmt(c.submitted_at) }}</td>
              <td class="py-2"><Link :href="`/admin/clearances/${c.id}`" class="text-indigo-600 hover:underline">Review</Link></td>
            </tr>
            <tr v-if="!props.clearances?.length">
              <td colspan="5" class="py-8 text-center text-slate-500">No requests.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
