<script setup lang="ts">
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Asset } from '@/types';
import { ref, watch } from 'vue';
import { router } from '@inertiajs/vue3';

interface Props {
    assets: {
        data: Asset[];
        links: [];
    };
    filters: { search: string };
}

const props = defineProps<Props>();

const search = ref(props.filters.search);

watch(search, (value) => {
    router.get(route('assets.index'), { search: value }, { preserveState: true });
});
</script>

<template>
    <Head title="Assets" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Assets</h1>
        <div class="flex justify-between items-center mt-4">
            <input type="text" v-model="search" placeholder="Search assets..." class="border px-2 py-1 rounded" />
            <div>
                <Link :href="route('assets.export')" class="px-4 py-2 bg-purple-500 text-white rounded mr-2">Export Assets</Link>
                <Link :href="route('assets.import')" class="px-4 py-2 bg-green-500 text-white rounded mr-2">Import Assets</Link>
                <Link :href="route('assets.create')" class="px-4 py-2 bg-blue-500 text-white rounded">Add Asset</Link>
            </div>
        </div>
        <div v-if="assets.data.length" class="mt-4">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Asset Tag</th>
                        <th class="py-2 px-4 border-b">Description</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="asset in assets.data" :key="asset.id">
                        <td class="py-2 px-4 border-b">{{ asset.asset_tag }}</td>
                        <td class="py-2 px-4 border-b">{{ asset.description }}</td>
                        <td class="py-2 px-4 border-b">{{ asset.status }}</td>
                        <td class="py-2 px-4 border-b">
                            <Link :href="route('assets.show', asset.id)" class="text-blue-500">View</Link>
                            <Link :href="route('assets.edit', asset.id)" class="text-yellow-500 ml-2">Edit</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4 flex justify-between items-center">
                <Link
                    v-for="link in assets.links"
                    :key="link.url"
                    :href="link.url"
                    v-html="link.label"
                    class="px-3 py-1 border rounded"
                    :class="{ 'bg-blue-500 text-white': link.active }"
                />
            </div>
        </div>
        <div v-else>
            <p>No assets found.</p>
        </div>
    </div>
</template>
