<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import AssetSummaryHeader from '@/components/Asset/AssetSummaryHeader.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Asset } from '@/types';
import { ref, computed } from 'vue';
import { useToast } from '@/composables/useToast';

interface StaffOption { id: number; name: string }

const props = defineProps<{ asset: Asset; staff: StaffOption[] }>();

const form = useForm({
  assignee_id: null as number | null,
  assignee_type: 'staff',
  due_at: '',
  notes: '',
});

const { show } = useToast();

const submit = () => {
  form.post(`/assets/${props.asset.id}/checkout`, {
    onSuccess: () => show('Asset checked out successfully.', 'success'),
    onError: () => show('Failed to checkout asset.', 'danger'),
  });
};

const staffFilter = ref('');
const filteredStaff = computed(() => {
  const q = staffFilter.value.trim().toLowerCase();
  if (!q) return props.staff;
  return props.staff.filter(s => s.name.toLowerCase().includes(q));
});
const currentHolder = computed(() => {
  // @ts-ignore Asset has staff_id in our types
  const id = (props.asset as any).staff_id as number | null | undefined;
  return id ? props.staff.find(s => s.id === id) || null : null;
});
</script>

<template>
  <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: asset.asset_tag, href: `/assets/${asset.id}` }, { title: 'Checkout', href: `/assets/${asset.id}/checkout` }]">
    <Head :title="`Checkout ${asset.asset_tag}`" />
    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
      <AssetSummaryHeader :asset="asset" />
      <div>
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Checkout Asset</h1>
        <p class="text-sm text-slate-600 dark:text-slate-300">Assign to a staff member, set a due date, and add notes.</p>
      </div>

      <form @submit.prevent="submit">
        <GlassCard class="space-y-4">
          <div class="flex flex-wrap items-center gap-2 text-sm">
            <span class="text-slate-500 dark:text-slate-400">Currently assigned:</span>
            <span v-if="currentHolder" class="inline-flex items-center rounded-full bg-indigo-100 px-2 py-1 font-medium text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-200">{{ currentHolder?.name }}</span>
            <span v-else class="inline-flex items-center rounded-full bg-slate-100 px-2 py-1 font-medium text-slate-600 dark:bg-slate-800/60 dark:text-slate-200">Unassigned</span>
          </div>
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label for="assignee_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Assign To</label>
              <input
                type="text"
                v-model="staffFilter"
                placeholder="Search staff..."
                class="mb-2 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100"
              />
              <select id="assignee_id" v-model="form.assignee_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100">
                <option :value="null">Select Staff</option>
                <option v-for="person in filteredStaff" :key="person.id" :value="person.id">{{ person.name }}</option>
              </select>
              <InputError :message="form.errors.assignee_id" class="mt-2" />
            </div>
            <div>
              <label for="due_at" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Due Date</label>
              <input id="due_at" type="date" v-model="form.due_at" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
              <InputError :message="form.errors.due_at" class="mt-2" />
            </div>
          </div>

          <div>
            <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Notes</label>
            <textarea id="notes" v-model="form.notes" rows="4" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100"></textarea>
            <InputError :message="form.errors.notes" class="mt-2" />
          </div>
        </GlassCard>

        <div class="mt-4 flex justify-end gap-3">
          <GlassButton type="button" variant="secondary" @click="form.reset()">Reset</GlassButton>
          <GlassButton type="submit" :disabled="form.processing">Checkout</GlassButton>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
