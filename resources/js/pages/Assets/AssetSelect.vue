<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';
import { debounce } from 'lodash';

const props = defineProps<{
    assets: any; // Paginated list of assets
    operation: string; // e.g., 'checkout', 'checkin', 'lease'
    search: string;
}>();

const searchTerm = ref(props.search);

const selectAsset = (assetId: number) => {
    router.visit(`/assets/${assetId}/${props.operation}`);
};

watch(searchTerm, debounce(() => {
    router.get(`/assets/${props.operation}/select`, { search: searchTerm.value }, {
        preserveState: true,
        replace: true,
    });
}, 300));
</script>

<template>
    <AppLayout :title="`Select Asset for ${operation}`">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Select Asset for {{ operation.charAt(0).toUpperCase() + operation.slice(1) }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                    <div v-if="operation === 'lease'" class="mb-4 p-4 bg-blue-100 border-l-4 border-blue-500 text-blue-700">
                        <p>Please select an asset to initiate a new lease agreement.</p>
                    </div>
                    <div v-else-if="operation === 'dispose'" class="mb-4 p-4 bg-red-100 border-l-4 border-red-500 text-red-700">
                        <p>Select an asset to mark it for disposal. This action cannot be undone.</p>
                    </div>
                    <div v-else-if="operation === 'maintenance'" class="mb-4 p-4 bg-yellow-100 border-l-4 border-yellow-500 text-yellow-700">
                        <p>Choose an asset to schedule or record maintenance activities.</p>
                    </div>
                    <div v-else-if="operation === 'reserve'" class="mb-4 p-4 bg-green-100 border-l-4 border-green-500 text-green-700">
                        <p>Select an asset to reserve it for future use.</p>
                    </div>
                    <div v-else-if="operation === 'lease-return'" class="mb-4 p-4 bg-purple-100 border-l-4 border-purple-500 text-purple-700">
                        <p>Select an asset to process its lease return.</p>
                    </div>

                    <div class="mb-4">
                        <input
                            type="text"
                            v-model="searchTerm"
                            placeholder="Search assets..."
                            class="form-input w-full"
                        />
                    </div>

                    <div v-if="assets.data.length">
                        <ul class="divide-y divide-gray-200">
                            <li
                                v-for="asset in assets.data"
                                :key="asset.id"
                                class="py-4 flex justify-between items-center hover:bg-gray-50 cursor-pointer"
                                @click="selectAsset(asset.id)"
                            >
                                <div>
                                    <p class="text-lg font-semibold">{{ asset.asset_tag }} - {{ asset.description }}</p>
                                    <p class="text-sm text-gray-500">Serial: {{ asset.serial_no }}</p>
                                </div>
                                <button class="px-4 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600">
                                    Select
                                </button>
                            </li>
                        </ul>
                        <!-- Pagination links -->
                        <div class="mt-4">
                            <template v-for="(link, key) in assets.links" :key="key">
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
                        No assets found.
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>
