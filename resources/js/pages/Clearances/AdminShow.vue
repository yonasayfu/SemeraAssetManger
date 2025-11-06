<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useToast } from '@/composables/useToast';

const props = defineProps<{ clearance: any }>();

const { show } = useToast();
const approve = () => router.post(`/admin/clearances/${props.clearance.id}/approve`, { auto_checkin: autoCheckin.value }, { onSuccess: () => show('Approved and PDF generated.', 'success'), onError: () => show('Approval failed.', 'danger') });
const autoCheckin = ref(true);
const fmt = (v?: string) => v ? new Intl.DateTimeFormat(undefined, { dateStyle: 'medium', timeStyle: 'short' }).format(new Date(v)) : '—';
const remarks = ref<string>(props.clearance.remarks || '');
const save = () => router.put(`/admin/clearances/${props.clearance.id}`, { remarks: remarks.value, items: [] }, { onSuccess: () => show('Saved remarks.', 'success') });
const reject = () => router.post(`/admin/clearances/${props.clearance.id}/reject`, {}, { onSuccess: () => show('Rejected.', 'danger') });
</script>

<template>
  <AppLayout :breadcrumbs="[{ title: 'Clearances', href: '/admin/clearances' }, { title: `#${props.clearance.id}`, href: `/admin/clearances/${props.clearance.id}` }]">
    <Head :title="`Review Clearance #${props.clearance.id}`" />
    <div class="mx-auto mt-6 w-full max-w-5xl px-4 pb-12">
      <div class="mb-4 flex items-center justify-between">
        <h2 class="text-xl font-semibold">Review Clearance #{{ props.clearance.id }}</h2>
        <div class="flex items-center gap-3">
          <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" v-model="autoCheckin" class="rounded" /> Auto check-in returned assets</label>
          <button class="rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white" @click="approve">Approve</button>
          <button class="rounded-md bg-rose-600 px-3 py-2 text-sm font-semibold text-white" @click="reject">Reject</button>
        </div>
      </div>
      <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/80 p-6 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <p class="text-sm text-slate-600">Staff: <strong>{{ props.clearance.staff?.name }}</strong> — Status: <strong>{{ props.clearance.status }}</strong></p>
        <p class="text-xs text-slate-500 mt-1">Submitted: {{ fmt(props.clearance.submitted_at) }} • Approved: {{ fmt(props.clearance.approved_at) }}</p>
        <div class="mt-4">
          <label class="block text-sm font-medium text-slate-700">Remarks / Comments</label>
          <textarea v-model="remarks" rows="3" class="mt-1 w-full rounded-md border-slate-300 text-sm"></textarea>
          <div class="mt-2"><button class="rounded-md bg-slate-700 px-3 py-2 text-sm font-semibold text-white" @click="save">Save</button>
          <a v-if="props.clearance.pdf_path" :href="`/admin/clearances/${props.clearance.id}/pdf`" class="ml-2 rounded-md bg-slate-500 px-3 py-2 text-sm font-semibold text-white">Download PDF</a></div>
        </div>
        <table class="mt-4 w-full text-sm">
          <thead>
            <tr class="text-left text-slate-600">
              <th class="py-2">Asset</th>
              <th class="py-2">Action</th>
              <th class="py-2">Result</th>
              <th class="py-2">Notes</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="it in props.clearance.items" :key="it.id" class="border-t border-slate-200/70">
              <td class="py-2">{{ it.asset?.asset_tag }} - {{ it.asset?.description || it.description }}</td>
              <td class="py-2">{{ props.clearance.status==='rejected' ? "—" : (it.action || "—") }}</td>
              <td class="py-2">{{ it.result || (props.clearance.status==='rejected' ? 'REJECTED' : '—') }}</td>
              <td class="py-2">{{ it.condition_note || '—' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </AppLayout>
</template>
