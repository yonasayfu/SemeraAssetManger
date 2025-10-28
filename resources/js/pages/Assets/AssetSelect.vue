<script setup lang="ts">
import AppLayout from '@/Layouts/AppLayout.vue';
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
    router.get(route(`assets.${props.operation}.select`), { search: searchTerm.value }, {
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
