<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
  widgets: [],
  layout: '2-col',
});

const submit = () => {
  form.post(route('setup.manage-dashboard.store'));
};
</script>

<template>
    <Head title="Manage Dashboard" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Manage Dashboard</h1>
        <form @submit.prevent="submit">
            <div>
                <h2>Widgets</h2>
                <!-- Add your widget selection here -->
            </div>
            <div>
                <h2>Layout</h2>
                <select v-model="form.layout">
                    <option value="2-col">2 Columns</option>
                    <option value="3-col">3 Columns</option>
                    <option value="4-col">4 Columns</option>
                </select>
            </div>
            <button type="submit">Save</button>
        </form>
    </div>
</template>
