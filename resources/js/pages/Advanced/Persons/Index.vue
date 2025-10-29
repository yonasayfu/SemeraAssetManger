<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { confirmDialog } from '@/lib/confirm';
import { useToast } from '@/composables/useToast';
import { Eye, Edit3, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

interface PersonRow {
    id: number;
    name: string;
    employee_id?: string | null;
    title?: string | null;
    email?: string | null;
    phone?: string | null;
    department?: { name?: string | null } | null;
    site?: { name?: string | null } | null;
    location?: { name?: string | null } | null;
}

defineProps<{ people: PersonRow[] }>();

const page = usePage();
const userPermissions = computed<string[]>(() => (page.props as any).auth?.permissions || []);
const can = (perm: string) => userPermissions.value.includes(perm);

const { show } = useToast();

const destroy = async (person: PersonRow) => {
    const accepted = await confirmDialog({
        title: 'Delete person?',
        message: `This will delete ${person.name}.`,
        confirmText: 'Delete',
        cancelText: 'Cancel',
    });
    if (!accepted) return;
    router.delete(`/advanced/persons/${person.id}`, {
        preserveScroll: true,
        onSuccess: () => show('Person deleted.', 'danger'),
        onError: () => show('Failed to delete person.', 'danger'),
    });
};
</script>

<template>
    <AppLayout>
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
                            <td class="py-2 px-4 border-b text-right">
                                <div class="inline-flex items-center gap-2">
                                    <Link :href="`/advanced/persons/${person.id}`" class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300" title="View">
                                        <Eye class="size-4" />
                                        <span class="sr-only">View</span>
                                    </Link>
                                    <Link v-if="can('advanced.view')" :href="`/advanced/persons/${person.id}/edit`" class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300" title="Edit">
                                        <Edit3 class="size-4" />
                                        <span class="sr-only">Edit</span>
                                    </Link>
                                    <button v-if="can('advanced.delete')" type="button" class="inline-flex items-center rounded-md p-2 text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10" title="Delete" @click="destroy(person)">
                                        <Trash2 class="size-4" />
                                        <span class="sr-only">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else>
                <p>No persons found.</p>
            </div>
        </div>
    </AppLayout>
</template>
