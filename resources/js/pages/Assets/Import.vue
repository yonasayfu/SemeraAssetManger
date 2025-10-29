<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    file: null as File | null,
});

const submit = () => {
    form.post('/assets/import', { forceFormData: true });
};
</script>

<template>
    <AppLayout :breadcrumbs="[{ title: 'Assets', href: '/assets' }, { title: 'Import', href: '/assets/import' }]">
    <Head title="Import Assets" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Import Assets</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="file">Upload File</label>
                <input id="file" type="file" @input="form.file = ($event.target as HTMLInputElement).files?.[0] ?? null" class="w-full" />
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Import</button>
            </div>
        </form>
    </div>
    </AppLayout>
</template>
