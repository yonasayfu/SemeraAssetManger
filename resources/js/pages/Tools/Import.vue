<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Head, useForm, usePage, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
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
  mapping: {} as Record<string, string>,
  options: '' as string,
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

// Smart preview + mapping
const props = defineProps<{
  preview?: { headers: string[]; sample?: Record<string, unknown>[] };
  suggestedMapping?: Record<string, string>;
  entity?: string;
}>();

const hasPreview = computed(() => Array.isArray(props?.preview?.headers) && props.preview!.headers.length > 0);
const mapping = ref<Record<string, string>>({ ...(props.suggestedMapping || {}) });
const allTargetsByEntity: Record<string, string[]> = {
  staff: ['name','email','phone','job_title','account_type','status','recovery_email'],
  sites: ['name','address','city','state','postal_code','country'],
  locations: ['name','site'],
  categories: ['name'],
  departments: ['name'],
  vendors: ['name'],
  products: ['name','vendor','warranty_months','unit_cost_minor','currency'],
  maintenances: ['asset_tag','title','scheduled_for'],
  warranties: ['asset_tag','provider','expiry_date'],
  contracts: ['number'],
  'purchase-orders': ['number'],
  software: ['name','vendor'],
};
const allTargets = computed(() => allTargetsByEntity[form.entity] || []);

const startPreview = () => {
  const fd = new FormData();
  if (form.file) fd.append('file', form.file as any);
  fd.append('entity', form.entity);
  router.post('/tools/import/preview', fd, { forceFormData: true, preserveScroll: true });
};
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

    <div class="mx-auto mt-6 w-full max-w-4xl px-4 pb-12 space-y-6">
      <form @submit.prevent="startPreview" class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
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

        <div class="mt-3">
          <button type="submit" class="rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-indigo-500 disabled:opacity-60" :disabled="!form.file">Preview</button>
        </div>
      </form>

      <div v-if="hasPreview" class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <h3 class="mb-2 text-sm font-semibold">Map columns</h3>
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="label in props.preview!.headers" :key="label" class="flex items-center gap-2">
            <div class="w-1/2 truncate text-sm text-slate-700 dark:text-slate-200" :title="label">{{ label }}</div>
            <select v-model="mapping[label]" class="w-1/2 rounded-md border-slate-300 text-sm">
              <option :value="''">Skip</option>
              <option v-for="t in allTargets" :key="t" :value="t">{{ t }}</option>
            </select>
          </div>
        </div>
        <div class="mt-3">
          <button
            v-if="can('tools.import')"
            type="button"
            :disabled="loading || !form.file"
            @click="() => { form.mapping = mapping as any; form.options = JSON.stringify({}); submit(); }"
            class="rounded-full bg-green-600 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-green-500 disabled:opacity-60"
          >
            <span v-if="loading">Importing…</span>
            <span v-else>Import Now</span>
          </button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
