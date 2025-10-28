<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{ title: string }>();
</script>

<template>
    <Head :title="title" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">{{ title }}</h1>
        <p>This is a placeholder page.</p>
    </div>
</template>
