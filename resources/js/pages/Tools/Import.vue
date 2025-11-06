<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head, useForm, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useAsyncAction } from '@/composables/useAsyncAction';
import { useToast } from '@/composables/useToast';

const form = useForm({
  entity: 'staff' as 'staff'
    | 'sites'
    | 'locations'
    | 'categories'
    | 'departments'
    | 'maintenances'
    | 'warranties'
    | 'vendors'
    | 'products'
    | 'contracts'
    | 'purchase-orders'
    | 'software',
  file: null as File | null,
});

const onFile = (e: Event) => {
  const target = e.target as HTMLInputElement | null;
  if (target?.files?.length) {
    // @ts-expect-error inertia accepts File
    form.file = target.files[0];
  }
};

const { show } = useToast();
const doSubmit = () => form.post(`/tools/import/${form.entity}`, {
  forceFormData: true,
  onSuccess: () => show('Import completed successfully.', 'success'),
  onError: () => show('Import failed. Please check your file and try again.', 'danger'),
});
const { run: submit, loading } = useAsyncAction(doSubmit);

const page = usePage();
const can = (perm: string) => ((page.props as any).auth?.permissions || []).includes(perm);
</script>

<template>
  <Head title="Import" />
  <AppLayout :breadcrumbs="[{ title: 'Tools', href: '/tools' }, { title: 'Import', href: '/tools/import' }]">
    <ResourceToolbar
      title="Import"
      description="Upload CSV or XLSX files to bulk create or update entities."
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
              <option value="staff">Staff</option>
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
            <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">File</label>
            <input type="file" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" @input="onFile" class="mt-1 block w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm dark:border-slate-700 dark:bg-slate-900/70" />
          </div>
        </div>

        <p class="text-xs text-slate-500 dark:text-slate-400">Supported formats: CSV, XLSX. Ensure column headers match the provided templates.</p>

        <div>
          <button
            v-if="can('tools.import')"
            type="submit"
            :disabled="loading || !form.file"
            class="rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-indigo-500 disabled:opacity-60"
          >
            <span v-if="loading">Importing…</span>
            <span v-else>Start Import</span>
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
