<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Site } from '@/types';

defineProps<{ sites: Site[] }>();
</script>

<template>
    <Head title="Sites" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Sites</h1>
        <div v-if="sites.length" class="mt-4">
            <ul>
                <li v-for="site in sites" :key="site.id">
                    {{ site.name }}
                </li>
            </ul>
        </div>
        <div v-else>
            <p>No sites found.</p>
        </div>
    </div>
</template>
