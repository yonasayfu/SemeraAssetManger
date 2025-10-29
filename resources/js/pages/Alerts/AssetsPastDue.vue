<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps<{
    alerts: any; // Paginated list of alerts
}>();
</script>

<template>
    <AppLayout title="Assets Past Due Alerts">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Assets Past Due Alerts</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div v-if="alerts.data.length">
                        <ul class="divide-y divide-gray-200">
                            <li
                                v-for="alert in alerts.data"
                                :key="alert.id"
                                class="py-4 flex justify-between items-center"
                            >
                                <div>
                                    <p class="text-lg font-semibold">{{ alert.message }}</p>
                                    <p class="text-sm text-gray-500">Asset: {{ alert.asset.asset_tag }} - {{ alert.asset.description }}</p>
                                    <p class="text-sm text-gray-500">Due Date: {{ alert.due_date }}</p>
                                </div>
                                <Link :href="`/assets/${alert.asset.id}`" class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                    View Asset
                                </Link>
                            </li>
                        </ul>
                        <!-- Pagination links -->
                        <div class="mt-4">
                            <template v-for="(link, key) in alerts.links" :key="key">
                                <Link
                                    v-if="link.url"
                                    :href="link.url"
                                    v-html="link.label"
                                    class="px-3 py-2 mx-1 border rounded-md"
                                    :class="{ 'bg-blue-500 text-white': link.active }"
                                />
                                <span
                                    v-else
                                    v-html="link.label"
                                    class="px-3 py-2 mx-1 border rounded-md text-gray-400"
                                />
                            </template>
                        </div>
                    </div>
                    <div v-else class="text-center text-gray-500 py-8">
                        No assets past due alerts found.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
