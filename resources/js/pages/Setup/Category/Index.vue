<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Category } from '@/types';

defineProps<{ categories: Category[] }>();
</script>

<template>
    <Head title="Categories" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Categories</h1>
        <div v-if="categories.length" class="mt-4">
            <ul>
                <li v-for="category in categories" :key="category.id">
                    {{ category.name }}
                </li>
            </ul>
        </div>
        <div v-else>
            <p>No categories found.</p>
        </div>
    </div>
</template>
