<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { AssetDocument } from '@/types';

defineProps<{ documents: AssetDocument[] }>();
</script>

<template>
    <Head title="Documents Gallery" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Documents Gallery</h1>
        <div v-if="documents.length" class="mt-4">
            <ul>
                <li v-for="document in documents" :key="document.id">
                    <a :href="'/storage/' + document.file_path" target="_blank">{{ document.title }} ({{ document.asset?.asset_tag }})</a>
                </li>
            </ul>
        </div>
        <div v-else>
            <p>No documents found.</p>
        </div>
    </div>
</template>
