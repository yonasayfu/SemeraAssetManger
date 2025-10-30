<script setup lang="ts">
import { Head, useForm } from '@inertiajs/vue3';
import { Category } from '@/types';

const props = defineProps<{ category: Category; categories: Category[] }>();

const form = useForm({
    name: props.category.name,
    description: props.category.description,
    parent_id: props.category.parent_id,
});

const submit = () => {
    form.put(`/setup/categories/${props.category.id}`);
};
</script>

<template>
    <Head title="Edit Category" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Edit Category</h1>
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
                <label for="parent_id">Parent Category</label>
                <select id="parent_id" v-model="form.parent_id" class="w-full">
                    <option :value="null">Select a parent category</option>
                    <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                </select>
            </div>
            <div>
                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded">Update</button>
            </div>
        </form>
    </div>
</template>
