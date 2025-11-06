<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm, Link, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps<{
  preview?: { headers: string[]; sample?: Record<string, unknown>[] };
  token?: string;
  suggestedMapping?: Record<string, string>;
  dryRun?: {
    total: number;
    ready: number;
    errors: number;
    warnings: number;
    rows: { row: number; errors: string[]; warnings: string[] }[];
  };
}>();

// Step 1: Upload
const uploadForm = useForm<{ file: File | null }>({ file: null });
const startPreview = () => uploadForm.post('/assets/import/preview', { forceFormData: true });

// Step 2: Mapping
const allTargets = [
  'asset_tag','description','purchase_date','cost','currency','status','purchased_from','serial_no','site','location','category','department','assigned_to','project_code','asset_photo'
];
const mapping = ref<Record<string, string>>({ ...(props.suggestedMapping || {}) });
const setAllSkip = () => { mapping.value = {}; };

// Options
const createMissingTaxonomy = ref<boolean>(true);

// Step 3: Dry run
const runDryRun = () => {
  router.post('/assets/import/dry-run', {
    token: props.token,
    mapping: mapping.value,
    options: { create_missing_taxonomy: createMissingTaxonomy.value },
  }, { preserveScroll: true });
};

// Step 4: Import
const importNow = () => {
  router.post('/assets/import', {
    token: props.token,
    mapping: mapping.value,
    options: { create_missing_taxonomy: createMissingTaxonomy.value },
  }, { preserveScroll: true });
};

const hasPreview = computed(() => Array.isArray(props?.preview?.headers) && props.preview!.headers.length > 0);
const dryRun = computed(() => props.dryRun);
</script>

<template>
  <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: 'Import', href: '/assets/import' }]">
    <Head title="Import Assets" />
    <div class="p-4 space-y-8">
      <div>
        <h1 class="text-2xl font-bold">Import Assets</h1>
        <p class="mt-2 text-sm text-slate-600 dark:text-slate-300">
          Start with our AssetTiger-compatible template.
          <Link href="/assets/import/template" class="text-indigo-600 hover:underline">Download CSV template</Link>
        </p>
      </div>

      <!-- Step 1: Upload & preview -->
      <div class="rounded-xl border border-slate-200/70 bg-white/80 p-4 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <h2 class="font-semibold mb-2">1. Upload file</h2>
        <form @submit.prevent="startPreview" class="flex flex-col gap-3 sm:flex-row sm:items-center">
          <input type="file" @input="uploadForm.file = ($event.target as HTMLInputElement).files?.[0] ?? null" class="w-full" />
          <button type="submit" class="rounded-md bg-indigo-600 px-4 py-2 text-white">Preview</button>
        </form>
        <div v-if="hasPreview" class="mt-4 text-sm text-slate-600 dark:text-slate-300">
          <p>Detected columns: {{ props.preview?.headers.join(', ') }}</p>
        </div>
      </div>

      <!-- Step 2: Mapping -->
      <div v-if="hasPreview" class="rounded-xl border border-slate-200/70 bg-white/80 p-4 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <h2 class="font-semibold mb-2">2. Map columns</h2>
        <div class="mb-3">
          <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" v-model="createMissingTaxonomy" class="rounded" /> Create missing Site/Location/Category/Department</label>
        </div>
        <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
          <div v-for="label in props.preview!.headers" :key="label" class="flex items-center gap-2">
            <div class="w-1/2 truncate text-sm text-slate-700 dark:text-slate-200" :title="label">{{ label }}</div>
            <select v-model="mapping[label]" class="w-1/2 rounded-md border-slate-300 text-sm">
              <option :value="''">Skip</option>
              <option v-for="t in allTargets" :key="t" :value="t">{{ t }}</option>
            </select>
          </div>
        </div>
        <div class="mt-3 flex items-center gap-2">
          <button type="button" @click="setAllSkip" class="rounded-md bg-slate-200 px-3 py-1.5 text-sm">Clear</button>
          <button type="button" @click="runDryRun" class="rounded-md bg-indigo-600 px-4 py-2 text-sm text-white">Run Dry Run</button>
        </div>
      </div>

      <!-- Step 3: Results -->
      <div v-if="props.dryRun" class="rounded-xl border border-slate-200/70 bg-white/80 p-4 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <h2 class="font-semibold mb-2">3. Dry run report</h2>
        <p class="text-sm">Rows: {{ dryRun?.total }} | Ready: {{ dryRun?.ready }} | Errors: {{ dryRun?.errors }} | Warnings: {{ dryRun?.warnings }}</p>
        <div class="mt-2 text-sm" v-if="dryRun?.rows?.length">
          <div v-for="r in dryRun!.rows" :key="r.row" class="border-b border-slate-200 py-2">
            <div class="font-medium">Row {{ r.row }}</div>
            <div v-if="r.errors?.length" class="text-red-600">Errors: {{ r.errors.join('; ') }}</div>
            <div v-if="r.warnings?.length" class="text-amber-600">Warnings: {{ r.warnings.join('; ') }}</div>
          </div>
        </div>
        <div class="mt-3">
          <button type="button" :disabled="(dryRun?.ready ?? 0) === 0" @click="importNow" class="rounded-md bg-green-600 px-4 py-2 text-sm text-white disabled:opacity-50">Import Now</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
