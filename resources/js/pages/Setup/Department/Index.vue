<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Department } from '@/types';

defineProps<{ departments: Department[] }>();
</script>

<template>
    <Head title="Departments" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Departments</h1>
        <div v-if="departments.length" class="mt-4">
            <ul>
                <li v-for="department in departments" :key="department.id">
                    {{ department.name }}
                </li>
            </ul>
        </div>
        <div v-else>
            <p>No departments found.</p>
        </div>
    </div>
</template>
