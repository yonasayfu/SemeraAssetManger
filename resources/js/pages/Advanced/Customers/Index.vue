<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Customer } from '@/types';

defineProps<{ customers: Customer[] }>();
</script>

<template>
    <Head title="Customers" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Customers</h1>
        <div v-if="customers.length" class="mt-4">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Name</th>
                        <th class="py-2 px-4 border-b">Contact Name</th>
                        <th class="py-2 px-4 border-b">Email</th>
                        <th class="py-2 px-4 border-b">Phone</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="customer in customers" :key="customer.id">
                        <td class="py-2 px-4 border-b">{{ customer.name }}</td>
                        <td class="py-2 px-4 border-b">{{ customer.contact_name }}</td>
                        <td class="py-2 px-4 border-b">{{ customer.email }}</td>
                        <td class="py-2 px-4 border-b">{{ customer.phone }}</td>
                        <td class="py-2 px-4 border-b">
                            <Link :href="route('advanced.customers.edit', customer.id)" class="text-blue-500">Edit</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p>No customers found.</p>
        </div>
    </div>
</template>
