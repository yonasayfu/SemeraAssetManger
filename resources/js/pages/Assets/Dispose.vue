<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import AssetSummaryHeader from '@/components/Asset/AssetSummaryHeader.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { confirmDialog } from '@/lib/confirm';
import { Asset } from '@/types';
import { useToast } from '@/composables/useToast';

const props = defineProps<{ asset: Asset }>();

const form = useForm({
  notes: '',
  disposal_type: '',
});

const { show } = useToast();

const submit = async () => {
  const accepted = await confirmDialog({
    title: 'Dispose asset?',
    message: `This will mark ${props.asset.asset_tag} as disposed.`,
    confirmText: 'Dispose',
    cancelText: 'Cancel',
  });
  if (!accepted) return;
  form.post(`/assets/${props.asset.id}/dispose`, {
    onSuccess: () => show('Asset disposed successfully.', 'success'),
    onError: () => show('Failed to dispose asset.', 'danger'),
  });
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: asset.asset_tag, href: `/assets/${asset.id}` }, { title: 'Dispose', href: `/assets/${asset.id}/dispose` }]">
    <Head :title="`Dispose ${asset.asset_tag}`" />
    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
      <AssetSummaryHeader :asset="asset" />
      <div>
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Dispose Asset</h1>
        <p class="text-sm text-slate-600 dark:text-slate-300">Choose disposal type and add notes.</p>
      </div>
      <form @submit.prevent="submit">
        <GlassCard class="space-y-4">
          <div class="grid gap-4 md:grid-cols-2">
            <div>
              <label for="disposal_type" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Disposal Type</label>
              <select id="disposal_type" v-model="form.disposal_type" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100">
                <option value="">Select Disposal Type</option>
                <option value="Sold">Sold</option>
                <option value="Donated">Donated</option>
                <option value="Lost">Lost</option>
                <option value="Broken">Broken</option>
              </select>
              <InputError :message="form.errors.disposal_type" class="mt-2" />
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
          <GlassButton type="submit" :disabled="form.processing">Dispose</GlassButton>
        </div>
      </form>
    </div>
    </AppLayout>
</template>
