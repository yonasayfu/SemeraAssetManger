<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import GlassCard from '@/components/GlassCard.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import type { Audit } from '@/types';

const props = defineProps<{ audits: Audit[]; filters?: { search?: string; status?: string | null } }>();

const search = ref<string>(props.filters?.search ?? '');
const status = ref<string | null>(props.filters?.status ?? null);

const apply = () => {
  router.get('/tools/audits', {
    search: search.value || undefined,
    status: status.value || undefined,
  }, { preserveScroll: true, replace: true, preserveState: true });
};

const clear = () => { search.value = ''; status.value = null; apply(); };
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Tools', href: '/tools' }, { title: 'Audits', href: '/tools/audits' }]">
    <Head title="Audits" />
    <div class="space-y-4 p-4">
        <ResourceToolbar
          title="Audits"
          description="Quick access to scan and report on audits."
          :show-export="false"
          :show-print="false"
          :create-route="'/audits/wizard'"
          create-text="Create Audit"
        />

        <GlassCard>
          <div class="flex flex-wrap items-center gap-2">
            <input
              v-model="search"
              type="search"
              placeholder="Search by audit name..."
              class="h-9 w-full max-w-xs rounded-lg border border-slate-200 bg-white px-3 text-sm leading-none text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200"
            />
            <select
              v-model="status"
              class="h-9 rounded-lg border border-slate-200 bg-white px-3 text-sm leading-none text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:border-slate-700 dark:bg-slate-900/70 dark:text-slate-200"
            >
              <option :value="null">All Statuses</option>
              <option value="Ongoing">Ongoing</option>
              <option value="Draft">Draft</option>
              <option value="Completed">Completed</option>
            </select>
            <button type="button" class="btn-glass btn-glass-sm btn-variant-primary" @click="apply">Apply</button>
            <button type="button" class="btn-glass btn-glass-sm btn-variant-secondary" @click="clear">Clear</button>
          </div>
        </GlassCard>

        <div v-if="audits.length" class="mt-2 overflow-hidden rounded-lg border border-slate-200 dark:border-slate-800">
            <table class="min-w-full divide-y divide-slate-200 bg-white dark:divide-slate-800 dark:bg-slate-900/60">
                <thead>
                    <tr class="bg-slate-50 text-left text-xs font-semibold uppercase tracking-wide text-slate-600 dark:bg-slate-800/60 dark:text-slate-300">
                        <th class="py-2 px-4">Name</th>
                        <th class="py-2 px-4">Site</th>
                        <th class="py-2 px-4">Location</th>
                        <th class="py-2 px-4">Status</th>
                        <th class="py-2 px-4">Started</th>
                        <th class="py-2 px-4">Completed</th>
                        <th class="py-2 px-4">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="audit in audits" :key="audit.id" class="bg-white text-sm dark:bg-slate-900/60">
                        <td class="py-2 px-4 border-b dark:border-slate-800">
                          <Link :href="`/audits/${audit.id}/scan`" class="text-indigo-600 hover:underline dark:text-indigo-300">{{ audit.name }}</Link>
                        </td>
                        <td class="py-2 px-4 border-b dark:border-slate-800">{{ audit.site?.name }}</td>
                        <td class="py-2 px-4 border-b dark:border-slate-800">{{ audit.location?.name }}</td>
                        <td class="py-2 px-4 border-b dark:border-slate-800">{{ audit.status }}</td>
                        <td class="py-2 px-4 border-b dark:border-slate-800">{{ audit.started_at }}</td>
                        <td class="py-2 px-4 border-b dark:border-slate-800">{{ audit.completed_at }}</td>
                        <td class="py-2 px-4 border-b dark:border-slate-800">
                            <div class="flex items-center gap-2">
                                <Link :href="`/audits/${audit.id}/scan`" class="btn-glass btn-glass-sm btn-variant-primary">Scan</Link>
                                <Link :href="`/audits/${audit.id}/report`" class="btn-glass btn-glass-sm">Report</Link>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p class="text-sm text-slate-600 dark:text-slate-300">No audits found. Use Create Audit to get started.</p>
        </div>
    </div>
    </AppLayout>
</template>
