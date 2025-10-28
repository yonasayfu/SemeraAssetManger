<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Asset } from '@/types';

defineProps<{ assets: Asset[] }>();
</script>

<template>
    <Head title="Images Gallery" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Images Gallery</h1>
        <div v-if="assets.length" class="mt-4 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div v-for="asset in assets" :key="asset.id" class="relative">
                <img :src="'/storage/' + asset.photo" class="w-full h-48 object-cover rounded" />
                <div class="absolute bottom-0 left-0 right-0 bg-black bg-opacity-50 text-white p-2 rounded-b">
                    {{ asset.asset_tag }}
                </div>
            </div>
        </div>
        <div v-else>
            <p>No images found.</p>
        </div>
    </div>
</template>
