<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Asset, Location } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import { useToast } from '@/composables/useToast';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{ asset: Asset; locations: Location[] }>();

const { show } = useToast();

const form = useForm({
    to_location_id: null,
    reason: '',
});

const submit = () => {
    form.post(`/assets/${props.asset.id}/move`, {
        onSuccess: () => {
            show('Asset moved successfully.', 'success');
        },
        onError: () => {
            show('Failed to move asset.', 'danger');
        },
    });
};
</script>

<template>
    <Head :title="`Move ${asset.asset_tag}`" />
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: props.asset.asset_tag, href: `/assets/${props.asset.id}` }, { title: 'Move', href: `/assets/${props.asset.id}/move` }]">
        <div class="space-y-6">
            <form @submit.prevent="submit">
                <GlassCard class="space-y-4">
                    <div>
                        <label for="from_location" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Current Location</label>
                        <input id="from_location" type="text" :value="asset.location?.name ?? 'N/A'" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" disabled />
                    </div>
                    <div>
                        <label for="to_location_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200">To Location</label>
                        <select id="to_location_id" v-model="form.to_location_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100">
                            <option :value="null">Select Location</option>
                            <option v-for="location in locations" :key="location.id" :value="location.id">{{ location.name }}</option>
                        </select>
                        <InputError :message="form.errors.to_location_id" class="mt-2" />
                    </div>
                    <div>
                        <label for="reason" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Reason</label>
                        <textarea id="reason" v-model="form.reason" rows="3" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100"></textarea>
                        <InputError :message="form.errors.reason" class="mt-2" />
                    </div>
                </GlassCard>
                <div class="flex justify-end gap-3">
                    <GlassButton type="button" variant="secondary" @click="form.reset()">
                        Reset
                    </GlassButton>
                    <GlassButton type="submit" :disabled="form.processing">
                        Move Asset
                    </GlassButton>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
