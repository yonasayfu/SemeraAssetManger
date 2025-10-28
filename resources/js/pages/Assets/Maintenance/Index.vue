<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Asset, Maintenance } from '@/types';

const props = defineProps<{ asset: Asset; maintenances: Maintenance[] }>();
</script>

<template>
    <Head :title="`Maintenance for ${asset.asset_tag}`" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Maintenance for Asset: {{ asset.asset_tag }}</h1>
        <Link :href="route('assets.maintenance.create', asset.id)" class="px-4 py-2 bg-blue-500 text-white rounded">Add Maintenance</Link>

        <div v-if="maintenances.length" class="mt-4">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Title</th>
                        <th class="py-2 px-4 border-b">Type</th>
                        <th class="py-2 px-4 border-b">Scheduled For</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Cost</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="maintenance in maintenances" :key="maintenance.id">
                        <td class="py-2 px-4 border-b">{{ maintenance.title }}</td>
                        <td class="py-2 px-4 border-b">{{ maintenance.maintenance_type }}</td>
                        <td class="py-2 px-4 border-b">{{ maintenance.scheduled_for }}</td>
                        <td class="py-2 px-4 border-b">{{ maintenance.status }}</td>
                        <td class="py-2 px-4 border-b">{{ maintenance.cost }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p>No maintenance records found for this asset.</p>
        </div>
    </div>
</template>
