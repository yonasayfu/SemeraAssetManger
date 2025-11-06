<script setup lang="ts">
import { computed, ref } from 'vue';
import axios from 'axios';
import { confirmDialog } from '@/lib/confirm';
import { useToast } from '@/composables/useToast';

interface DocumentItem {
    id: number | string;
    title: string;
    url?: string | null;
    mime_type?: string | null;
    uploaded_at?: string | null;
}

const props = withDefaults(
    defineProps<{
        data: { documents?: DocumentItem[] } | null;
        loading: boolean;
        assetId: number;
    }>(),
    {
        data: null,
        loading: false,
    },
);

const documents = computed<DocumentItem[]>(() => props.data?.documents ?? []);

const file = ref<File | null>(null);
const title = ref('');
const uploading = ref(false);
const error = ref<string | null>(null);
const deleting = ref<number | null>(null);
const { show: toast } = useToast();
const upload = async () => {
    if (!file.value || !title.value) { error.value = 'Title and file required'; return; }
    uploading.value = true; error.value = null;
    const form = new FormData();
    form.append('file', file.value);
    form.append('title', title.value);
    try {
        const res = await axios.post(`/assets/${props.assetId}/tabs/documents`, form, { headers: { 'Content-Type': 'multipart/form-data' } });
        (props as any).data.documents = res.data.documents || [];
        file.value = null; title.value = '';
        toast('Document uploaded', 'success');
    } catch (e: any) {
        error.value = 'Upload failed';
        toast('Failed to upload document', 'danger');
    } finally {
        uploading.value = false;
    }
};

// Inline rename
const editingId = ref<number | null>(null);
const editTitle = ref('');
const savingEdit = ref(false);
const startEdit = (d: DocumentItem) => {
    editingId.value = Number(d.id);
    editTitle.value = d.title || '';
};
const cancelEdit = () => {
    editingId.value = null;
    editTitle.value = '';
};
const saveEdit = async (docId: number | string) => {
    savingEdit.value = true;
    try {
        const res = await axios.patch(`/assets/${props.assetId}/tabs/documents/${docId}`, { title: editTitle.value });
        (props as any).data.documents = res.data.documents || [];
        toast('Document renamed', 'success');
        cancelEdit();
    } catch (e: any) {
        toast('Failed to rename document', 'danger');
    } finally {
        savingEdit.value = false;
    }
};

const remove = async (documentId: number | string) => {
    const ok = await confirmDialog({ message: 'Delete this document? This action cannot be undone.' });
    if (!ok) return;
    deleting.value = Number(documentId);
    try {
        const res = await axios.delete(`/assets/${props.assetId}/tabs/documents/${documentId}`);
        (props as any).data.documents = res.data.documents || [];
        toast('Document deleted', 'success');
    } catch (e: any) {
        toast('Failed to delete document', 'danger');
    } finally {
        deleting.value = null;
    }
};
</script>

<template>
    <div>
        <div class="mb-4 rounded-xl border border-slate-200/70 bg-white/70 p-3 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <input v-model="title" placeholder="Title" class="rounded border border-slate-300 px-2 py-1 text-sm dark:border-slate-700 dark:bg-slate-900/70" />
                <input type="file" @change="(e: any) => file = e.target.files?.[0] ?? null" class="text-xs" />
                <button :disabled="!file || !title || uploading" @click="upload" class="rounded-md bg-indigo-600 px-3 py-1.5 text-white disabled:opacity-50">Upload Document</button>
                <span v-if="error" class="text-rose-600">{{ error }}</span>
            </div>
        </div>
        <div v-if="loading" class="space-y-3">
            <div v-for="i in 5" :key="i" class="h-12 animate-pulse rounded-xl bg-slate-200/70 dark:bg-slate-700/50" />
        </div>

        <div v-else-if="!documents.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 text-sm text-slate-500 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 dark:text-slate-300">
            No documents have been uploaded for this asset.
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        <th class="px-4 py-3">Title</th>
                        <th class="px-4 py-3">Type</th>
                        <th class="px-4 py-3">Uploaded</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr
                        v-for="document in documents"
                        :key="document.id"
                        class="bg-white/60 text-slate-700 dark:bg-slate-900/50 dark:text-slate-200"
                    >
                        <td class="px-4 py-3 font-medium">
                            <template v-if="editingId === Number(document.id)">
                                <div class="flex items-center gap-2">
                                    <input v-model="editTitle" class="flex-1 rounded border border-slate-300 px-2 py-1 text-sm dark:border-slate-700 dark:bg-slate-900/70" />
                                    <button
                                        class="rounded-md bg-emerald-600 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-white disabled:opacity-50"
                                        :disabled="savingEdit"
                                        @click="saveEdit(document.id)"
                                    >Save</button>
                                    <button
                                        class="rounded-md border border-slate-400 px-2 py-1 text-xs font-semibold uppercase tracking-wide text-slate-600 dark:border-slate-600 dark:text-slate-300"
                                        :disabled="savingEdit"
                                        @click="cancelEdit"
                                    >Cancel</button>
                                </div>
                            </template>
                            <template v-else>
                                {{ document.title }}
                            </template>
                        </td>
                        <td class="px-4 py-3 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                            {{ document.mime_type ?? 'Unknown' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ document.uploaded_at ? new Date(document.uploaded_at).toLocaleString() : 'N/A' }}
                        </td>
                        <td class="px-4 py-3 text-right">
                            <a
                                :href="document.url ?? '#'"
                                target="_blank"
                                rel="noopener noreferrer"
                                :class="[
                                    'inline-flex items-center rounded-full border border-indigo-500 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-indigo-600 transition hover:bg-indigo-50 focus:outline-none focus:ring-1 focus:ring-indigo-300 dark:border-indigo-400 dark:text-indigo-200 dark:hover:bg-indigo-500/10',
                                    !document.url ? 'pointer-events-none opacity-60' : '',
                                ]"
                            >
                                {{ document.url ? 'Download' : 'Unavailable' }}
                            </a>
                            <button
                                v-if="editingId !== Number(document.id)"
                                class="ml-2 inline-flex items-center rounded-full border border-slate-400 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-600 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700/20"
                                @click="startEdit(document)"
                            >
                                Rename
                            </button>
                            <button
                                class="ml-2 inline-flex items-center rounded-full border border-rose-500 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-rose-600 transition hover:bg-rose-50 disabled:opacity-50 dark:border-rose-400 dark:text-rose-200 dark:hover:bg-rose-500/10"
                                :disabled="deleting === Number(document.id)"
                                @click="remove(document.id)"
                            >
                                Delete
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>
