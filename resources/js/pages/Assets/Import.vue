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
  options?: { create_missing_taxonomy?: boolean };
  presets?: { id: number; name: string; mapping: Record<string, string>; options?: Record<string, any> }[];
}>();

// Step 1: Upload
const uploadForm = useForm<{ file: File | null }>({ file: null });
const startPreview = () => uploadForm.post('/assets/import/preview', { forceFormData: true });

// Step 2: Mapping
const allTargets = [
  'asset_tag','description','purchase_date','cost','currency','status','purchased_from','brand','model','serial_no','site','location','category','department','assigned_to','project_code','asset_condition','asset_photo','photo'
];
const mapping = ref<Record<string, string>>({ ...(props.suggestedMapping || {}) });
const setAllSkip = () => { mapping.value = {}; };

// Options
const createMissingTaxonomy = ref<boolean>(props.options?.create_missing_taxonomy ?? true);
const updateExistingByTag = ref<boolean>(props.options?.update_existing_by_tag ?? true);
const autoGenerateTags = ref<boolean>(props.options?.auto_generate_tags ?? false);
const tagPrefix = ref<string>(props.options?.tag_prefix ?? 'AST-');
const downloadPhotos = ref<boolean>(props.options?.download_photos ?? false);
const includeUnmappedAsCustomFields = ref<boolean>(props.options?.include_unmapped_as_custom_fields ?? false);

// Step 3: Dry run
const buildOptions = () => ({
  create_missing_taxonomy: createMissingTaxonomy.value,
  update_existing_by_tag: updateExistingByTag.value,
  auto_generate_tags: autoGenerateTags.value,
  tag_prefix: tagPrefix.value,
  download_photos: downloadPhotos.value,
  include_unmapped_as_custom_fields: includeUnmappedAsCustomFields.value,
});

const runDryRun = () => {
  router.post('/assets/import/dry-run', {
    token: props.token,
    mapping: mapping.value,
    options: JSON.stringify(buildOptions()),
  }, { preserveScroll: true });
};

// Step 4: Import
const importNow = () => {
  router.post('/assets/import', {
    token: props.token,
    mapping: mapping.value,
    options: JSON.stringify(buildOptions()),
  }, { preserveScroll: true });
};

const hasPreview = computed(() => Array.isArray(props?.preview?.headers) && props.preview!.headers.length > 0);
const dryRun = computed(() => props.dryRun);

// Presets
const selectedPresetId = ref<number | ''>('');
const newPresetName = ref<string>('');
const applyPreset = (id: number) => {
  const preset = props.presets?.find(p => p.id === id);
  if (!preset) return;
  mapping.value = { ...preset.mapping };
  const opt = preset.options || {};
  if (typeof opt.create_missing_taxonomy !== 'undefined') createMissingTaxonomy.value = !!opt.create_missing_taxonomy;
  if (typeof opt.update_existing_by_tag !== 'undefined') updateExistingByTag.value = !!opt.update_existing_by_tag;
  if (typeof opt.auto_generate_tags !== 'undefined') autoGenerateTags.value = !!opt.auto_generate_tags;
  if (typeof opt.tag_prefix !== 'undefined') tagPrefix.value = String(opt.tag_prefix);
  if (typeof opt.download_photos !== 'undefined') downloadPhotos.value = !!opt.download_photos;
  if (typeof opt.include_unmapped_as_custom_fields !== 'undefined') includeUnmappedAsCustomFields.value = !!opt.include_unmapped_as_custom_fields;
};

// Background import job
const job = ref<{ id: number; status: string; processed_rows: number; total_rows: number; message?: string; failures?: number } | null>(null);
let pollTimer: number | null = null;
const startBackgroundImport = async () => {
  const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content || '';
  const res = await fetch('/assets/import/jobs', {
    method: 'POST',
    headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf },
    body: JSON.stringify({
      token: props.token,
      mapping: mapping.value,
      options: buildOptions(),
      expected_total: props.dryRun?.total ?? undefined,
    }),
    credentials: 'same-origin',
  });
  const data = await res.json();
  job.value = data.job;
  if (pollTimer) window.clearInterval(pollTimer);
  pollTimer = window.setInterval(pollJob, 1500);
};

const pollJob = async () => {
  if (!job.value) return;
  const res = await fetch(`/assets/import/jobs/${job.value.id}`, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
  const data = await res.json();
  job.value = data.job;
  if (job.value.status === 'succeeded' || job.value.status === 'failed') {
    if (pollTimer) { window.clearInterval(pollTimer); pollTimer = null; }
  }
};

const cancelJob = async () => {
  if (!job.value) return;
  const csrf = (document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null)?.content || '';
  await fetch(`/assets/import/jobs/${job.value.id}/cancel`, { method: 'POST', headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrf } });
  await pollJob();
};
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
        <div class="mb-3 space-y-2">
          <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
            <label class="text-sm w-24">Presets</label>
            <select v-model="selectedPresetId" @change="selectedPresetId && applyPreset(Number(selectedPresetId))" class="rounded-md border-slate-300 text-sm w-full sm:w-64">
              <option :value="''">Select a preset</option>
              <option v-for="p in props.presets || []" :key="p.id" :value="p.id">{{ p.name }}</option>
            </select>
            <form class="flex gap-2 ml-auto" @submit.prevent="router.post('/assets/import/presets', { name: newPresetName, mapping: mapping, options: JSON.stringify(buildOptions()) }, { preserveScroll: true })">
              <input v-model="newPresetName" placeholder="Preset name" class="rounded-md border-slate-300 text-sm w-40" />
              <button type="submit" class="rounded-md bg-slate-700 px-3 py-1.5 text-sm text-white">Save Preset</button>
            </form>
            <button v-if="selectedPresetId" @click="router.delete(`/assets/import/presets/${selectedPresetId}`)" class="rounded-md bg-red-600 px-3 py-1.5 text-sm text-white">Delete</button>
          </div>
          <label class="block inline-flex items-center gap-2 text-sm"><input type="checkbox" v-model="createMissingTaxonomy" class="rounded" /> Create missing Site/Location/Category/Department</label>
          <label class="block inline-flex items-center gap-2 text-sm"><input type="checkbox" v-model="updateExistingByTag" class="rounded" /> Update existing assets matched by Asset Tag</label>
          <div class="flex items-center gap-2 text-sm">
            <label class="inline-flex items-center gap-2"><input type="checkbox" v-model="autoGenerateTags" class="rounded" /> Auto-generate Asset Tag if missing</label>
            <input v-model="tagPrefix" class="rounded-md border-slate-300 text-sm w-28" :disabled="!autoGenerateTags" placeholder="Prefix" />
          </div>
          <label class="block inline-flex items-center gap-2 text-sm"><input type="checkbox" v-model="downloadPhotos" class="rounded" /> Download photos from URLs (save to storage)</label>
          <label class="block inline-flex items-center gap-2 text-sm"><input type="checkbox" v-model="includeUnmappedAsCustomFields" class="rounded" /> Include unmapped columns as custom fields</label>
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
          <button type="button" @click="importNow" class="rounded-md bg-green-600 px-4 py-2 text-sm text-white">Import Now</button>
          <button type="button" @click="startBackgroundImport" class="rounded-md bg-sky-600 px-4 py-2 text-sm text-white">Import in Background</button>
          <form @submit.prevent="router.post('/assets/import/presets', { name: newPresetName || 'Default', mapping: mapping, options: JSON.stringify(buildOptions()) }, { preserveScroll: true })">
            <button type="submit" class="rounded-md bg-slate-700 px-3 py-1.5 text-sm text-white">Save as default mapping</button>
          </form>
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
          <button type="button" @click="startBackgroundImport" class="ml-2 rounded-md bg-sky-600 px-4 py-2 text-sm text-white">Import in Background</button>
        </div>
      </div>

      <!-- Background Job Status -->
      <div v-if="job" class="rounded-xl border border-slate-200/70 bg-white/80 p-4 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <h2 class="font-semibold mb-2">Background Import</h2>
        <p class="text-sm">Status: {{ job.status }} <span v-if="job.message">- {{ job.message }}</span></p>
        <div class="mt-2">
          <div class="h-3 w-full bg-slate-200 rounded">
            <div class="h-3 bg-sky-600 rounded" :style="{ width: ((job.processed_rows / Math.max(1, job.total_rows)) * 100).toFixed(0) + '%' }"></div>
          </div>
          <p class="text-xs mt-1">{{ job.processed_rows }} / {{ job.total_rows }} rows</p>
        </div>
        <div class="mt-2 text-sm" v-if="(job.status === 'succeeded' || job.status === 'failed') && props.token">
          <Link :href="`/assets/import/report/${props.token}`" class="text-indigo-600 underline">Download issue report</Link>
        </div>
        <div class="mt-2" v-if="job.status === 'running' || job.status === 'pending'">
          <button @click="cancelJob" class="rounded-md bg-red-600 px-3 py-1.5 text-sm text-white">Cancel</button>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
