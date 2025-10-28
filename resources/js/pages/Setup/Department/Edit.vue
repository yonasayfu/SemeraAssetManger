<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Department } from '@/types';

const props = defineProps<{ department: Department; departments: Department[] }>();

const form = useForm({
    name: props.department.name,
    description: props.department.description,
    parent_id: props.department.parent_id,
});

const submit = () => {
    form.put(route('departments.update', props.department.id));
};
</script>

<template>
    <Head title="Edit Department" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Edit Department</h1>
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
                    <option v-for="dept in departments" :key="dept.id" :value="dept.id">{{ dept.name }}</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</template>
