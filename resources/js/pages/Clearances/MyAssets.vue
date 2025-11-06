<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useToast } from '@/composables/useToast';

const props = defineProps<{ assets: { id: number; asset_tag: string; description: string; status: string; serial_no: string | null }[] }>();

const selected = ref<number[]>([]);
const toggle = (id: number) => {
  const i = selected.value.indexOf(id);
  if (i > -1) selected.value.splice(i, 1); else selected.value.push(id);
};
const allSelected = computed(() => selected.value.length === props.assets.length && props.assets.length > 0);
const toggleAll = () => {
  if (allSelected.value) selected.value = []; else selected.value = props.assets.map(a => a.id);
};

const { show } = useToast();
const requestClearance = () => router.post('/clearances', { asset_ids: selected.value }, {
  onSuccess: () => show('Clearance draft created.', 'success'),
  onError: () => show('Failed to create clearance.', 'danger'),
});
</script>

<template>
  <AppLayout :breadcrumbs="[{ title: 'Clearance', href: '/clearances' }, { title: 'My Assets', href: '/my/assets' }]">
    <Head title="My Assets" />
    <div class="mx-auto mt-6 w-full max-w-5xl px-4 pb-12">
      <!-- Print header for consistency -->
      <div class="hidden print:block text-center text-slate-800">
        <img src="/images/asset-logo.svg" alt="Asset Management" class="mx-auto mb-3 h-12 w-auto print-logo" />
        <h1 class="text-xl font-semibold">Asset Management</h1>
        <p class="text-sm">My Assets</p>
        <hr class="print-divider" />
      </div>
      <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 p-6 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <div class="flex items-center justify-between print:hidden">
          <h2 class="text-xl font-semibold">My Assets</h2>
          <div class="flex items-center gap-2">
            <button class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white" @click="requestClearance">Request Clearance</button>
            <button class="rounded-md bg-slate-700 px-3 py-2 text-sm font-semibold text-white" onclick="window.print()">Print</button>
          </div>
        </div>
        <table class="mt-4 w-full text-sm">
          <thead>
            <tr class="text-left text-slate-600">
              <th class="py-2 print:hidden"><input type="checkbox" :checked="allSelected" @change="toggleAll" /></th>
              <th class="py-2">Tag</th>
              <th class="py-2">Description</th>
              <th class="py-2">Serial</th>
              <th class="py-2">Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="a in props.assets" :key="a.id" class="border-t border-slate-200/70">
              <td class="py-2 print:hidden"><input type="checkbox" :checked="selected.includes(a.id)" @change="toggle(a.id)" /></td>
              <td class="py-2">{{ a.asset_tag }}</td>
              <td class="py-2">{{ a.description }}</td>
              <td class="py-2">{{ a.serial_no || '—' }}</td>
              <td class="py-2">{{ a.status }}</td>
            </tr>
            <tr v-if="!props.assets?.length">
              <td colspan="4" class="py-8 text-center text-slate-500">No assets assigned.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>

<style>
@media print {
  @page { size: A4 landscape; margin: 1.5cm; }
  body { -webkit-print-color-adjust: exact !important; print-color-adjust: exact !important; background: #fff !important; color: #0f172a !important; }
  .print-header { display: block !important; text-align: center; margin-bottom: 10px; }
  .print-header img { max-height: 48px; margin: 0 auto 6px; }
}
.print-logo { max-height: 48px; }
.print-divider { border: 0; border-top: 1px solid #e2e8f0; margin: 1rem auto 1.5rem; width: 100%; }
</style>
