<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import { Head, Link, usePage, router } from '@inertiajs/vue3';
import { confirmDialog } from '@/lib/confirm';
import { useToast } from '@/composables/useToast';
import { Edit3, Eye, Trash2 } from 'lucide-vue-next';
import { Asset } from '@/types';
import { computed, ref, watch } from 'vue';

interface LinkItem { url: string | null; label: string; active: boolean }
interface Props {
    assets: {
        data: Asset[];
        links: LinkItem[];
    };
    filters: { search: string };
}

const props = defineProps<Props>();

const search = ref(props.filters.search);

const page = usePage();
const userPermissions = computed<string[]>(() => (page.props as any).auth?.permissions || []);
const can = (perm: string) => userPermissions.value.includes(perm);

watch(search, (value) => {
    router.get('/assets', { search: value }, { preserveState: true });
});

const { show } = useToast();
const destroy = async (asset: Asset) => {
    const accepted = await confirmDialog({
        title: 'Delete asset?',
        message: `This will delete ${asset.asset_tag}.`,
        confirmText: 'Delete',
        cancelText: 'Cancel',
    });
    if (!accepted) return;
    router.delete(`/assets/${asset.id}`, {
        preserveScroll: true,
        onSuccess: () => show('Asset deleted.', 'danger'),
        onError: () => show('Failed to delete asset.', 'danger'),
    });
};
</script>

<template>
    <AppLayout>
    <Head title="Assets" />
    <div class="p-4">
        <h1 class="text-2xl font-bold">Assets</h1>
        <div class="flex justify-between items-center mt-4">
            <input type="text" v-model="search" placeholder="Search assets..." class="border px-2 py-1 rounded" />
            <div>
                <Link href="/exports" class="px-4 py-2 bg-purple-500 text-white rounded mr-2">Export Assets</Link>
                <Link v-if="can('assets.create')" href="/assets/import" class="px-4 py-2 bg-green-500 text-white rounded mr-2">Import Assets</Link>
                <Link v-if="can('assets.create')" href="/assets/create" class="px-4 py-2 bg-blue-500 text-white rounded">Add Asset</Link>
            </div>
        </div>
        <div v-if="assets.data.length" class="mt-4">
            <table class="min-w-full bg-white">
                <thead>
                    <tr>
                        <th class="py-2 px-4 border-b">Asset Tag</th>
                        <th class="py-2 px-4 border-b">Description</th>
                        <th class="py-2 px-4 border-b">Status</th>
                        <th class="py-2 px-4 border-b text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="asset in assets.data" :key="asset.id">
                        <td class="py-2 px-4 border-b">{{ asset.asset_tag }}</td>
                        <td class="py-2 px-4 border-b">{{ asset.description }}</td>
                        <td class="py-2 px-4 border-b">{{ asset.status }}</td>
                        <td class="py-2 px-4 border-b text-right">
                            <div class="inline-flex items-center gap-2">
                                <Link :href="`/assets/${asset.id}`" class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300" title="View">
                                    <Eye class="size-4" />
                                    <span class="sr-only">View</span>
                                </Link>
                                <Link v-if="can('assets.update')" :href="`/assets/${asset.id}/edit`" class="inline-flex items-center rounded-md p-2 text-slate-500 transition hover:bg-slate-100 hover:text-indigo-600 dark:text-slate-300 dark:hover:bg-slate-800/70 dark:hover:text-indigo-300" title="Edit">
                                    <Edit3 class="size-4" />
                                    <span class="sr-only">Edit</span>
                                </Link>
                                <button v-if="can('assets.delete')" type="button" class="inline-flex items-center rounded-md p-2 text-red-500 transition hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-500/10" title="Delete" @click="destroy(asset)">
                                    <Trash2 class="size-4" />
                                    <span class="sr-only">Delete</span>
                                </button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
            <div class="mt-4 flex justify-between items-center">
                <Link
                    v-for="link in assets.links"
                    :key="`${link.label}-${link.url ?? ''}`"
                    :href="link.url ?? '#'"
                    v-html="link.label"
                    class="px-3 py-1 border rounded"
                    :class="{ 'bg-blue-500 text-white': link.active, 'pointer-events-none opacity-50': !link.url }"
                />
            </div>
        </div>
        <div v-else>
            <p>No assets found.</p>
        </div>
    </div>
    </AppLayout>
</template>
