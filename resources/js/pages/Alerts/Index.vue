<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Alert } from '@/types';

defineProps<{ alerts: Alert[] }>();
</script>

<template>
    <Head title="Alerts" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Alerts</h1>
        <div v-if="alerts.length" class="mt-4">
            <ul>
                <li v-for="alert in alerts" :key="alert.id">
                    {{ alert.type }} - {{ alert.due_date }}
                </li>
            </ul>
        </div>
        <div v-else>
            <p>No alerts found.</p>
        </div>
    </div>
</template>
