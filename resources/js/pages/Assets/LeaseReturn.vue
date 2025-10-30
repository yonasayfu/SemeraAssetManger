<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Asset } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import { useToast } from '@/composables/useToast';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{ asset: Asset }>();

const { show } = useToast();

const form = useForm({
    notes: '',
});

const submit = () => {
    form.post(`/assets/${props.asset.id}/lease-return`, {
        onSuccess: () => {
            show('Asset lease returned successfully.', 'success');
        },
        onError: () => {
            show('Failed to return asset lease.', 'danger');
        },
    });
};
</script>

<template>
    <Head :title="`Lease Return ${asset.asset_tag}`" />
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: props.asset.asset_tag, href: `/assets/${props.asset.id}` }, { title: 'Lease Return', href: `/assets/${props.asset.id}/lease-return` }]">
        <div class="space-y-6">
            <form @submit.prevent="submit">
                <GlassCard class="space-y-4">
                    <div>
                        <label for="notes" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Notes</label>
                        <textarea id="notes" v-model="form.notes" rows="5" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100"></textarea>
                        <InputError :message="form.errors.notes" class="mt-2" />
                    </div>
                </GlassCard>
                <div class="flex justify-end gap-3">
                    <GlassButton type="button" variant="secondary" @click="form.reset()">
                        Reset
                    </GlassButton>
                    <GlassButton type="submit" :disabled="form.processing">
                        Return Lease
                    </GlassButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
