<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Company } from '@/types';

defineProps<{ company: Company }>();
</script>

<template>
    <Head title="Company Info" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Company Info</h1>
        <div v-if="company" class="mt-4">
            <p><strong>Name:</strong> {{ company.name }}</p>
            <p><strong>Address:</strong> {{ company.address }}</p>
            <p><strong>City:</strong> {{ company.city }}</p>
            <p><strong>State:</strong> {{ company.state }}</p>
            <p><strong>Postal Code:</strong> {{ company.postal_code }}</p>
            <p><strong>Country:</strong> {{ company.country }}</p>
            <p><strong>Timezone:</strong> {{ company.timezone }}</p>
            <p><strong>Currency:</strong> {{ company.currency }}</p>
            <p><strong>Date Format:</strong> {{ company.date_format }}</p>
            <p><strong>Financial Year Start:</strong> {{ company.financial_year_start }}</p>
        </div>
        <div v-else>
            <p>No company information found.</p>
        </div>
    </div>
</template>
