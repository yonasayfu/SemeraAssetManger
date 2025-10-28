<script setup lang="ts">
import { Head } from '@inertiajs/vue3';

defineProps<{ title: string }>();
</script>

<template>
    <Head :title="title" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">{{ title }}</h1>
        <p>This is a static page for {{ title }}. Content will be added here later.</p>
    </div>
</template>
