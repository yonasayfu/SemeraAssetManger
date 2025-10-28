<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Location } from '@/types';

defineProps<{ locations: Location[] }>();
</script>

<template>
    <Head title="Locations" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Locations</h1>
        <div v-if="locations.length" class="mt-4">
            <ul>
                <li v-for="location in locations" :key="location.id">
                    {{ location.name }}
                </li>
            </ul>
        </div>
        <div v-else>
            <p>No locations found.</p>
        </div>
    </div>
</template>
