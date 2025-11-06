<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';

const props = defineProps<{ clearances: { id:number; status:string; submitted_at?: string; approved_at?: string; created_at: string }[] }>();
const fmt = (v?: string) => v ? new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(v)) : '—';

const createRequest = () => router.post('/clearances');
</script>

<template>
  <AppLayout :breadcrumbs="[{ title: 'Clearances', href: '/clearances' }]">
    <Head title="My Clearances" />
    <div class="mx-auto mt-6 w-full max-w-5xl px-4 pb-12">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold">My Clearances</h2>
        <button type="button" @click="createRequest" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white">New Request</button>
      </div>
      <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 p-6 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <table class="w-full text-sm">
          <thead>
            <tr class="text-left text-slate-600">
              <th class="py-2">ID</th>
              <th class="py-2">Status</th>
              <th class="py-2">Submitted</th>
              <th class="py-2">Approved</th>
              <th class="py-2">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="c in props.clearances" :key="c.id" class="border-t border-slate-200/70">
              <td class="py-2">{{ c.id }}</td>
              <td class="py-2">{{ c.status }}</td>
              <td class="py-2">{{ fmt(c.submitted_at) }}</td>
              <td class="py-2">{{ fmt(c.approved_at) }}</td>
              <td class="py-2 flex items-center gap-2">
                <Link :href="`/clearances/${c.id}`" class="text-indigo-600 hover:underline">Open</Link>
                <a v-if="c.pdf_path" :href="`/clearances/${c.id}/pdf`" class="text-slate-600 hover:underline">Download PDF</a>
              </td>
            </tr>
            <tr v-if="!props.clearances?.length">
              <td colspan="5" class="py-8 text-center text-slate-500">No clearance requests yet.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
