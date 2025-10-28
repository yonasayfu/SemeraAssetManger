<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Audit } from '@/types';

defineProps<{ audits: Audit[] }>();
</script>

<template>
    <Head title="Audits" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Audits</h1>
        <div v-if="audits.length" class="mt-4">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Name</th>
                        <th class="py-2 px-4 border-b">Site</th>
                        <th class="py-2 px-4 border-b">Location</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b">Started At</th>
                        <th class="py-2 px-4 border-b">Completed At</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="audit in audits" :key="audit.id">
                        <td class="py-2 px-4 border-b">{{ audit.name }}</td>
                        <td class="py-2 px-4 border-b">{{ audit.site?.name }}</td>
                        <td class="py-2 px-4 border-b">{{ audit.location?.name }}</td>
                        <td class="py-2 px-4 border-b">{{ audit.status }}</td>
                        <td class="py-2 px-4 border-b">{{ audit.started_at }}</td>
                        <td class="py-2 px-4 border-b">{{ audit.completed_at }}</td>
                        <td class="py-2 px-4 border-b">
                            <Link :href="route('tools.audits.show', audit.id)" class="text-blue-500">View</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p>No audits found.</p>
        </div>
    </div>
</template>
