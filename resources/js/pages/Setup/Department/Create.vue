<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Department } from '@/types';

defineProps<{ departments: Department[] }>();

const form = useForm({
    name: '',
    description: '',
    parent_id: null,
});

const submit = () => {
    form.post(route('departments.store'));
};
</script>

<template>
    <Head title="Create Department" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Create Department</h1>
        <form @submit.prevent="submit" class="mt-4 space-y-4">
            <div>
                <label for="name">Name</label>
                <input id="name" type="text" v-model="form.name" class="w-full" />
            </div>
            <div>
                <label for="description">Description</label>
                <textarea id="description" v-model="form.description" class="w-full"></textarea>
            </div>
            <div>
                <label for="parent_id">Parent Department</label>
                <select id="parent_id" v-model="form.parent_id" class="w-full">
                    <option :value="null">Select a parent department</option>
                    <option v-for="department in departments" :key="department.id" :value="department.id">{{ department.name }}</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Create</button>
            </div>
        </form>
    </div>
</template>
