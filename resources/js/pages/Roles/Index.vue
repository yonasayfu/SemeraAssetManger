<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassButton from '@/components/GlassButton.vue';
import GlassCard from '@/components/GlassCard.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { confirmDialog } from '@/lib/confirm';
import { useToast } from '@/composables/useToast';
import { Head, Link, router } from '@inertiajs/vue3';
import { Edit3, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

interface RoleSummary {
    id: number;
    name: string;
    permissions: string[];
}

const props = defineProps<{
    roles: RoleSummary[];
    permissions: string[];
    can: {
        create: boolean;
        edit: boolean;
        delete: boolean;
    };
}>();

const roles = computed(() => props.roles ?? []);
const permissions = computed(() => props.permissions ?? []);
const { show } = useToast();

const remove = async (role: RoleSummary) => {
    if (!(await confirmDialog({
        title: 'Delete role?',
        message: `Removing ${role.name} will detach the permissions from any users assigned to it.`,
        confirmText: 'Delete',
        cancelText: 'Cancel',
    }))) {
        return;
    }

    router.delete(`/roles/${role.id}`, {
        onSuccess: () => show('Role deleted successfully.', 'danger'),
        onError: () => show('Failed to delete role.', 'danger'),
    });
};
</script>

<template>
    <Head title="Roles" />

    <AppLayout :breadcrumbs="[{ title: 'Roles', href: '/roles' }]">
    <div class="space-y-6">
        <ResourceToolbar
            title="Role management"
            description="Define access profiles and attach permissions to your team."
            :create-route="can.create ? '/roles/create' : undefined"
            :show-create="can.create"
        />

        <GlassCard
            variant="lite"
            padding="px-6 py-5"
            content-class="space-y-4"
        >
            <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
                <div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        Available permissions
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Permissions seeded in the system. Attach them to roles as needed.
                    </p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <span
                        v-for="permission in permissions"
                        :key="permission"
                        class="inline-flex items-center rounded-full bg-slate-200/70 px-2 py-0.5 text-xs font-medium text-slate-700 dark:bg-slate-800/60 dark:text-slate-200"
                    >
                        {{ permission }}
                    </span>
                </div>
            </div>
        </GlassCard>

        <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/70 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/50">
            <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                <thead class="bg-slate-50/80 dark:bg-slate-900/80">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Role
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Permissions
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 bg-white/90 dark:divide-slate-800 dark:bg-slate-950/40">
                    <tr v-if="roles.length === 0">
                        <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-300">
                            No roles created yet. Start by defining a custom role.
                        </td>
                    </tr>
                    <tr v-for="role in roles" v-else :key="role.id" class="hover:bg-slate-50/70 dark:hover:bg-slate-900/50">
                        <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">
                            {{ role.name }}
                        </td>
                        <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                            <div v-if="role.permissions.length" class="flex flex-wrap gap-2">
                                <span
                                    v-for="permission in role.permissions"
                                    :key="permission"
                                    class="inline-flex items-center rounded-full bg-emerald-100/80 px-2 py-0.5 text-xs font-medium text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200"
                                >
                                    {{ permission }}
                                </span>
                            </div>
                            <span v-else class="text-xs text-slate-400 dark:text-slate-500">
                                No direct permissions (inherits from defaults)
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right text-sm">
                            <div class="flex justify-end gap-2">
                                <Link
                                    v-if="can.edit"
                                    :href="`/roles/${role.id}/edit`"
                                    class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300"
                                    title="Edit role"
                                >
                                    <Edit3 class="h-4 w-4" />
                                    <span class="sr-only">Edit</span>
                                </Link>
                                <button
                                    v-if="can.delete"
                                    type="button"
                                    class="inline-flex items-center rounded-md p-2 text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                                    title="Delete role"
                                    @click="remove(role)"
                                >
                                    <Trash2 class="h-4 w-4" />
                                    <span class="sr-only">Delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    </AppLayout>
</template>
