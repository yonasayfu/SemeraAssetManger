<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
  entity: 'persons',
  format: 'csv',
});

const submit = () => {
  // Logic to trigger export
};
</script>

<template>
    <Head title="Export" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Export</h1>
        <form @submit.prevent="submit">
            <div>
                <h2>Entity</h2>
                <select v-model="form.entity">
                    <option value="persons">Persons</option>
                    <option value="sites">Sites</option>
                    <option value="locations">Locations</option>
                    <option value="categories">Categories</option>
                    <option value="departments">Departments</option>
                    <option value="maintenances">Maintenances</option>
                    <option value="warranties">Warranties</option>
                </select>
            </div>
            <div>
                <h2>Format</h2>
                <select v-model="form.format">
                    <option value="csv">CSV</option>
                    <option value="xlsx">XLSX</option>
                    <option value="pdf">PDF</option>
                </select>
            </div>
            <button type="submit">Export</button>
        </form>
    </div>
</template>
