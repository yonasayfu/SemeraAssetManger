<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Asset } from '@/types';

const props = defineProps<{ asset: Asset }>();

const form = useForm({
    notes: '',
    disposal_type: '',
});

const submit = () => {
    form.post(`/assets/${props.asset.id}/dispose`);
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: asset.asset_tag, href: `/assets/${asset.id}` }, { title: 'Dispose', href: `/assets/${asset.id}/dispose` }]">
    <Head :title="`Dispose ${asset.asset_tag}`" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Dispose Asset: {{ asset.asset_tag }}</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="disposal_type">Disposal Type</label>
                <select id="disposal_type" v-model="form.disposal_type" class="w-full">
                    <option value="">Select Disposal Type</option>
                    <option value="Sold">Sold</option>
                    <option value="Donated">Donated</option>
                    <option value="Lost">Lost</option>
                    <option value="Broken">Broken</option>
                </select>
            </div>
            <div>
                <label for="notes">Notes</label>
                <textarea id="notes" v-model="form.notes" class="w-full"></textarea>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Dispose</button>
            </div>
        </form>
    </div>
    </AppLayout>
</template>
