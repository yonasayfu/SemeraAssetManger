<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
  entity: 'persons',
  file: null,
});

const submit = () => {
  form.post(route(`tools.import.${form.entity}`));
};
</script>

<template>
    <Head title="Import" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Import</h1>
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
                <h2>File</h2>
                <input type="file" @input="form.file = $event.target.files[0]" />
            </div>
            <button type="submit">Import</button>
        </form>
    </div>
</template>
