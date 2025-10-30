<script setup lang="ts">
import { Head } from '@inertiajs/vue3';
import { Category } from '@/types';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import Pagination from '@/components/Pagination.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { confirmDialog } from '@/lib/confirm';
import { useTableFilters } from '@/composables/useTableFilters';
import { useToast } from '@/composables/useToast';
import { Link, router } from '@inertiajs/vue3';
import { Edit3, Eye, Search, Trash2 } from 'lucide-vue-next';
import { computed } from 'vue';

interface PaginationLink { url: string | null; label: string; active: boolean; }
interface PaginationMeta { from?: number | null; }

const props = defineProps<{
    categories: {
        data: Category[];
        links: PaginationLink[];
        meta?: PaginationMeta;
    };
    filters?: {
        search?: string;
        per_page?: number;
    };
    can: {
        create: boolean;
        edit: boolean;
        delete: boolean;
    };
}>();

const tableFilters = useTableFilters({
    route: '/setup/categories',
    initial: {
        search: props.filters?.search ?? '',
        per_page: props.filters?.per_page ?? 5,
    },
});

const { search, perPage, apply } = tableFilters;

const categories = computed<Category[]>(() => props.categories?.data ?? []);
const hasResults = computed<boolean>(() => categories.value.length > 0);
const paginationLinks = computed(() => props.categories?.links ?? []);
const paginationFrom = computed(() => props.categories?.meta?.from ?? 1);

const { show } = useToast();

const destroy = async (category: Category) => {
    const accepted = await confirmDialog({
        title: 'Delete category?',
        message: `This will permanently delete the category ${category.name} and all associated data.`,
        confirmText: 'Delete',
        cancelText: 'Cancel',
    });

    if (!accepted) {
        return;
    }

    router.delete(`/setup/categories/${category.id}`, {
        onSuccess: () => show('Category deleted successfully.', 'danger'),
        onError: () => show('Failed to delete category.', 'danger'),
    });
};

const exportCsv = () => {
    const params = new URLSearchParams();

    if (search.value) {
        params.set('search', search.value);
    }

    const query = params.toString();

    window.open(`/setup/categories/export${query ? `?${query}` : ''}`, '_blank', 'noopener=yes');
};
</script>

<template>
    <Head title="Categories" />
    <AppLayout :breadcrumbs="[{ title: 'Setup', href: '' }, { title: 'Categories', href: '/setup/categories' }]">
        <div class="space-y-6">
            <ResourceToolbar
                title="Category directory"
                description="Organize your assets into different categories."
                :create-route="can.create ? '/setup/categories/create' : undefined"
                :show-create="can.create"
                @export="exportCsv"
            />

            <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="search-glass relative w-full max-w-sm">
                    <span class="pointer-events-none absolute inset-y-0 left-3 flex items-center text-slate-400">
                        <Search class="size-4" />
                    </span>
                    <input
                        v-model="search"
                        type="text"
                        placeholder="Search categories"
                        class="w-full rounded-lg border border-transparent bg-white/80 py-2 pl-10 pr-3 text-sm text-slate-700 outline-none placeholder:text-slate-400 focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        @input="apply()"
                    />
                </div>

                <div class="flex items-center gap-2">
                    <label for="perPage" class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Per Page</label>
                    <select
                        id="perPage"
                        v-model.number="perPage"
                        class="rounded-lg border border-transparent bg-white/80 px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        @change="apply()"
                    >
                        <option :value="5">5</option>
                        <option :value="10">10</option>
                        <option :value="25">25</option>
                        <option :value="50">50</option>
                        <option :value="100">100</option>
                    </select>
                </div>
            </div>

            <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/70 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/50">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
                    <thead class="bg-slate-50/80 dark:bg-slate-900/80">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                #
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Name
                            </th>
                            <th class="px-6 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white/90 dark:divide-slate-800 dark:bg-slate-950/40">
                        <tr v-if="!hasResults">
                            <td colspan="3" class="px-6 py-8 text-center text-sm text-slate-500 dark:text-slate-300">
                                No categories match your filters yet. Add a new category to get started.
                            </td>
                        </tr>
                        <tr v-for="(category, index) in categories" v-else :key="category.id" class="hover:bg-slate-50/70 dark:hover:bg-slate-900/50">
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-slate-300">
                                {{ paginationFrom + index }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-slate-900 dark:text-slate-100">
                                <Link
                                    :href="`/setup/categories/${category.id}`"
                                    class="transition hover:text-indigo-600 dark:hover:text-indigo-300"
                                >
                                    {{ category.name }}
                                </Link>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <div class="flex justify-end gap-2">
                                    <Link
                                        :href="`/setup/categories/${category.id}`"
                                        class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300"
                                        title="View category"
                                    >
                                        <Eye class="size-4" />
                                        <span class="sr-only">View</span>
                                    </Link>
                                    <Link
                                        v-if="can.edit"
                                        :href="`/setup/categories/${category.id}/edit`"
                                        class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300"
                                        title="Edit category"
                                    >
                                        <Edit3 class="size-4" />
                                        <span class="sr-only">Edit</span>
                                    </Link>
                                    <button
                                        v-if="can.delete"
                                        type="button"
                                        class="inline-flex items-center rounded-md p-2 text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10"
                                        title="Delete category"
                                        @click="destroy(category)"
                                    >
                                        <Trash2 class="size-4" />
                                        <span class="sr-only">Delete</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="flex items-center justify-end">
                <Pagination :links="paginationLinks" />
            </div>
        </div>
    </AppLayout>
</template>
