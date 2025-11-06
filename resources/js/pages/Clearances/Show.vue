<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import { useToast } from '@/composables/useToast';

const props = defineProps<{ clearance: any }>();

const { show } = useToast();
const checked = ref<Record<number, boolean>>({});
(props.clearance.items || []).forEach((it: any) => { checked.value[it.id] = !!it.checked; });
const anyItems = computed(() => (props.clearance.items || []).length > 0);
const save = () => {
  const items = (props.clearance.items || []).map((it: any) => ({ id: it.id, checked: !!checked.value[it.id] }));
  router.put(`/clearances/${props.clearance.id}`, { items }, { onSuccess: () => show('Saved changes.', 'success'), onError: () => show('Save failed.', 'danger') });
};
const submit = () => router.post(`/clearances/${props.clearance.id}/submit`, {}, { onSuccess: () => show('Submitted for review.', 'success') });
</script>

<template>
  <AppLayout :breadcrumbs="[{ title: 'Clearances', href: '/clearances' }, { title: `#${props.clearance.id}`, href: `/clearances/${props.clearance.id}` }]">
    <Head :title="`Clearance #${props.clearance.id}`" />
    <div class="mx-auto mt-6 w-full max-w-5xl px-4 pb-12">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold">Clearance #{{ props.clearance.id }}</h2>
        <div class="flex items-center gap-2">
          <a v-if="props.clearance.pdf_path" :href="`/clearances/${props.clearance.id}/pdf`" class="rounded-md bg-slate-700 px-3 py-2 text-sm font-semibold text-white">Download PDF</a>
          <button v-if="props.clearance.status==='draft'" class="rounded-md bg-slate-600 px-3 py-2 text-sm font-semibold text-white" @click="save">Save</button>
          <button v-if="props.clearance.status==='draft' || props.clearance.status==='submitted'" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white" @click="submit">Submit</button>
        </div>
      </div>
      <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 p-6 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <p class="text-sm text-slate-600">Status: <strong>{{ props.clearance.status }}</strong></p>
        <table class="mt-4 w-full text-sm">
          <thead>
            <tr class="text-left text-slate-600">
              <th class="py-2" v-if="props.clearance.status==='draft'">Include</th>
              <th class="py-2">Asset</th>
              <th class="py-2">Action</th>
              <th class="py-2">Result</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="it in props.clearance.items" :key="it.id" class="border-t border-slate-200/70">
              <td class="py-2" v-if="props.clearance.status==='draft'"><input type="checkbox" v-model="checked[it.id]" /></td>
              <td class="py-2">{{ it.asset?.asset_tag }} - {{ it.asset?.description || it.description }}</td>
              <td class="py-2">{{ props.clearance.status==='rejected' ? "—" : (it.action || "—") }}</td>
              <td class="py-2">{{ it.result || (props.clearance.status==='rejected' ? 'REJECTED' : '—') }}</td>
            </tr>
            <tr v-if="!props.clearance.items?.length">
              <td colspan="3" class="py-8 text-center text-slate-500">No items.</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
