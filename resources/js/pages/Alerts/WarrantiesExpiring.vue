<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { Link } from '@inertiajs/vue3';
import AlertTable from '@/components/alerts/AlertTable.vue';

const props = defineProps<{
    alerts: any; // Paginated list of alerts
}>();
</script>

<template>
    <AppLayout title="Warranties Expiring">
        <ResourceToolbar
            title="Warranties Expiring"
            description="Warranties expiring within the next 60 days."
            :show-create="false"
            :show-export="false"
            :show-print="false"
        />

        <div class="mx-auto mt-6 w-full max-w-6xl px-4 pb-12">
          <AlertTable :alerts="alerts" />
        </div>
    </AppLayout>
</template>
