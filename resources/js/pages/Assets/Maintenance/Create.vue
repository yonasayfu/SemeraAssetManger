<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Asset } from '@/types';

const props = defineProps<{ asset: Asset }>();

const form = useForm({
    title: '',
    description: '',
    maintenance_type: '',
    scheduled_for: '',
    cost: null as number | null,
    vendor: '',
});

const submit = () => {
    form.post(`/assets/${props.asset.id}/maintenance`);
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: asset.asset_tag, href: `/assets/${asset.id}` }, { title: 'Maintenance', href: `/assets/${asset.id}/maintenance` }, { title: 'Add', href: `/assets/${asset.id}/maintenance/create` }]">
    <Head :title="`Add Maintenance for ${asset.asset_tag}`" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Add Maintenance for Asset: {{ asset.asset_tag }}</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="title">Title</label>
                <input id="title" type="text" v-model="form.title" class="w-full" />
            </div>
            <div>
                <label for="description">Description</label>
                <textarea id="description" v-model="form.description" class="w-full"></textarea>
            </div>
            <div>
                <label for="maintenance_type">Maintenance Type</label>
                <select id="maintenance_type" v-model="form.maintenance_type" class="w-full">
                    <option value="">Select Type</option>
                    <option value="Preventive">Preventive</option>
                    <option value="Corrective">Corrective</option>
                </select>
            </div>
            <div>
                <label for="scheduled_for">Scheduled For</label>
                <input id="scheduled_for" type="date" v-model="form.scheduled_for" class="w-full" />
            </div>
            <div>
                <label for="cost">Cost</label>
                <input id="cost" type="number" v-model="form.cost" class="w-full" />
            </div>
            <div>
                <label for="vendor">Vendor</label>
                <input id="vendor" type="text" v-model="form.vendor" class="w-full" />
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Add Maintenance</button>
            </div>
        </form>
    </div>
    </AppLayout>
</template>
