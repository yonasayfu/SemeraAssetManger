<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import AssetSummaryHeader from '@/components/Asset/AssetSummaryHeader.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { confirmDialog } from '@/lib/confirm';
import { Asset } from '@/types';
import { computed } from 'vue';
import { useToast } from '@/composables/useToast';

const props = defineProps<{ asset: Asset }>();

const form = useForm({ notes: '' });

const { show } = useToast();

const submit = async () => {
  const accepted = await confirmDialog({
    title: 'Check in asset?',
    message: `Confirm check-in for ${props.asset.asset_tag}.`,
    confirmText: 'Check in',
    cancelText: 'Cancel',
  });
  if (!accepted) return;
  form.post(`/assets/${props.asset.id}/checkin`, {
    onSuccess: () => show('Asset checked in successfully.', 'success'),
    onError: () => show('Failed to check in asset.', 'danger'),
  });
};

const currentHolder = computed(() => {
  // @ts-ignore asset has staff_id in our types
  const id = (props.asset as any).staff_id as number | null | undefined;
  return id || null;
});
</script>

<template>
  <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: asset.asset_tag, href: `/assets/${asset.id}` }, { title: 'Checkin', href: `/assets/${asset.id}/checkin` }]">
    <Head :title="`Checkin ${asset.asset_tag}`" />
    <div class="space-y-6 p-4 sm:p-6 lg:p-8">
      <AssetSummaryHeader :asset="asset" />
      <div>
        <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">Check-in Asset</h1>
        <p class="text-sm text-slate-600 dark:text-slate-300">Record the return of an asset to inventory and add notes.</p>
        <p v-if="!currentHolder" class="mt-1 text-xs text-amber-600 dark:text-amber-400">This asset is not currently assigned. You can still record a check-in note.</p>
      </div>

      <form @submit.prevent="submit">
        <GlassCard class="space-y-4">
          <div>
            <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Notes</label>
            <textarea id="notes" v-model="form.notes" rows="4" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100"></textarea>
            <InputError :message="form.errors.notes" class="mt-2" />
          </div>
        </GlassCard>
        <div class="mt-4 flex justify-end gap-3">
          <GlassButton type="button" variant="secondary" @click="form.reset()">Reset</GlassButton>
          <GlassButton type="submit" :disabled="form.processing">Check in</GlassButton>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
