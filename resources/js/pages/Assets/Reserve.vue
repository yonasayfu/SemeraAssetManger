<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Asset } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import AssetSummaryHeader from '@/components/Asset/AssetSummaryHeader.vue';
import { useToast } from '@/composables/useToast';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{ asset: Asset }>();

const { show } = useToast();

const form = useForm({
    start_at: '',
    end_at: '',
});

const submit = () => {
    form.post(`/assets/${props.asset.id}/reserve`, {
        onSuccess: () => {
            show('Asset reserved successfully.', 'success');
        },
        onError: () => {
            show('Failed to reserve asset.', 'danger');
        },
    });
};
</script>

<template>
    <Head :title="`Reserve ${asset.asset_tag}`" />
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: props.asset.asset_tag, href: `/assets/${props.asset.id}` }, { title: 'Reserve', href: `/assets/${props.asset.id}/reserve` }]">
        <div class="space-y-6">
            <AssetSummaryHeader :asset="asset" />
            <form @submit.prevent="submit">
                <GlassCard class="space-y-4">
                    <div>
                        <label for="start_at" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Start Date</label>
                        <input id="start_at" type="date" v-model="form.start_at" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                        <InputError :message="form.errors.start_at" class="mt-2" />
                    </div>
                    <div>
                        <label for="end_at" class="block text-sm font-medium text-slate-700 dark:text-slate-200">End Date</label>
                        <input id="end_at" type="date" v-model="form.end_at" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                        <InputError :message="form.errors.end_at" class="mt-2" />
                    </div>
                </GlassCard>
                <div class="flex justify-end gap-3">
                    <GlassButton type="button" variant="secondary" @click="form.reset()">
                        Reset
                    </GlassButton>
                    <GlassButton type="submit" :disabled="form.processing">
                        Reserve Asset
                    </GlassButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
