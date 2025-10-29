<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { Asset } from '@/types';

const props = defineProps<{ asset: Asset }>();

const form = useForm({
    notes: '',
});

const submit = () => {
    form.post(`/assets/${props.asset.id}/checkin`);
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: asset.asset_tag, href: `/assets/${asset.id}` }, { title: 'Checkin', href: `/assets/${asset.id}/checkin` }]">
    <Head :title="`Checkin ${asset.asset_tag}`" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Checkin Asset: {{ asset.asset_tag }}</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="notes">Notes</label>
                <textarea id="notes" v-model="form.notes" class="w-full"></textarea>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Checkin</button>
            </div>
        </form>
    </div>
    </AppLayout>
</template>
