<script setup lang="ts">
import { Head, Link } from '@inertiajs/vue3';
import { Person } from '@/types';

defineProps<{ people: Person[] }>();
</script>

<template>
    <Head title="Persons / Employees" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Persons / Employees</h1>
        <div v-if="people.length" class="mt-4">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Name</th>
                        <th class="py-2 px-4 border-b">Employee ID</th>
                        <th class="py-2 px-4 border-b">Title</th>
                        <th class="py-2 px-4 border-b">Email</th>
                        <th class="py-2 px-4 border-b">Phone</th>
                        <th class="py-2 px-4 border-b">Department</th>
                        <th class="py-2 px-4 border-b">Site</th>
                        <th class="py-2 px-4 border-b">Location</th>
                        <th class="py-2 px-4 border-b">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="person in people" :key="person.id">
                        <td class="py-2 px-4 border-b">{{ person.name }}</td>
                        <td class="py-2 px-4 border-b">{{ person.employee_id }}</td>
                        <td class="py-2 px-4 border-b">{{ person.title }}</td>
                        <td class="py-2 px-4 border-b">{{ person.email }}</td>
                        <td class="py-2 px-4 border-b">{{ person.phone }}</td>
                        <td class="py-2 px-4 border-b">{{ person.department?.name }}</td>
                        <td class="py-2 px-4 border-b">{{ person.site?.name }}</td>
                        <td class="py-2 px-4 border-b">{{ person.location?.name }}</td>
                        <td class="py-2 px-4 border-b">
                            <Link :href="route('advanced.persons.edit', person.id)" class="text-blue-500">Edit</Link>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div v-else>
            <p>No persons found.</p>
        </div>
    </div>
</template>
