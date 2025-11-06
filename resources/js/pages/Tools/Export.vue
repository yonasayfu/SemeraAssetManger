<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useToast } from '@/composables/useToast';
import { useAsyncAction } from '@/composables/useAsyncAction';

const form = useForm({
  entity: 'assets',
  format: 'csv',
});

const { show } = useToast();
const doSubmit = () => {
  show('Export started. You can track it in Download Center.', 'info');
  // Hook real endpoint here when implemented
};
const { run: submit, loading } = useAsyncAction(doSubmit);

const page = usePage();
const can = (perm: string) => ((page.props as any).auth?.permissions || []).includes(perm);
</script>

<template>
  <Head title="Export" />
  <AppLayout :breadcrumbs="[{ title: 'Tools', href: '/tools' }, { title: 'Export', href: '/tools/export' }]">
    <ResourceToolbar
      title="Export"
      description="Generate CSV/XLSX/PDF exports for selected entities."
      :show-create="false"
      :show-print="false"
      :show-export="false"
    />

    <div class="mx-auto mt-6 w-full max-w-3xl px-4 pb-12">
      <form @submit.prevent="submit" class="space-y-5 rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <div class="grid gap-4 sm:grid-cols-2">
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Entity</label>
            <select v-model="form.entity" class="mt-1 w-full rounded-lg border border-transparent bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200">
              <option value="assets">Assets</option>
              <option value="sites">Sites</option>
              <option value="locations">Locations</option>
              <option value="categories">Categories</option>
              <option value="departments">Departments</option>
              <option value="maintenances">Maintenances</option>
              <option value="warranties">Warranties</option>
              <option value="vendors">Vendors</option>
              <option value="products">Products</option>
              <option value="contracts">Contracts</option>
              <option value="purchase-orders">Purchase Orders</option>
              <option value="software">Software</option>
            </select>
          </div>
          <div>
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">Format</label>
            <select v-model="form.format" class="mt-1 w-full rounded-lg border border-transparent bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200">
              <option value="csv">CSV</option>
              <option value="xlsx">XLSX</option>
              <option value="pdf">PDF</option>
            </select>
          </div>
        </div>

        <p class="text-xs text-slate-500 dark:text-slate-400">Exports will include standard columns; use the Reports module for advanced customisation.</p>

        <div>
          <button
            v-if="can('tools.export')"
            type="submit"
            :disabled="loading"
            class="rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-indigo-500 disabled:opacity-60"
          >
            <span v-if="loading">Exporting…</span>
            <span v-else>Start Export</span>
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
