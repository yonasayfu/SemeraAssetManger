<script setup lang="ts">
import { computed, ref } from 'vue';
import axios from 'axios';
import { confirmDialog } from '@/lib/confirm';
import { useToast } from '@/composables/useToast';

interface PhotoItem {
    id: number | string;
    caption?: string | null;
    url: string;
    uploaded_at?: string | null;
}

const props = withDefaults(
    defineProps<{
        data: { photos?: PhotoItem[] } | null;
        loading: boolean;
        assetId: number;
    }>(),
    {
        data: null,
        loading: false,
    },
);

const photos = computed<PhotoItem[]>(() => props.data?.photos ?? []);

// Uploader (visible if parent has edit rights; rely on server to authorize)
const file = ref<File | null>(null);
const caption = ref('');
const uploading = ref(false);
const error = ref<string | null>(null);
const { show: toast } = useToast();

const upload = async () => {
    if (!file.value) return;
    uploading.value = true; error.value = null;
    const form = new FormData();
    form.append('photo', file.value);
    if (caption.value) form.append('caption', caption.value);
    try {
        const res = await axios.post(`/assets/${props.assetId}/tabs/photos`, form, { headers: { 'Content-Type': 'multipart/form-data' } });
        // Replace list with fresh data
        (props as any).data.photos = res.data.photos || [];
        file.value = null; caption.value = '';
        toast('Photo uploaded', 'success');
    } catch (e: any) {
        error.value = 'Upload failed';
        toast('Failed to upload photo', 'danger');
    } finally {
        uploading.value = false;
    }
};

const deleting = ref<number | null>(null);
const remove = async (photoId: number | string) => {
    const ok = await confirmDialog({ message: 'Delete this photo? This action cannot be undone.' });
    if (!ok) return;
    deleting.value = Number(photoId);
    try {
        const res = await axios.delete(`/assets/${props.assetId}/tabs/photos/${photoId}`);
        (props as any).data.photos = res.data.photos || [];
        toast('Photo deleted', 'success');
    } catch (e: any) {
        toast('Failed to delete photo', 'danger');
    } finally {
        deleting.value = null;
    }
};

// Inline edit caption
const editingId = ref<number | null>(null);
const editCaption = ref('');
const savingEdit = ref(false);
const startEdit = (p: PhotoItem) => {
    editingId.value = Number(p.id);
    editCaption.value = p.caption ?? '';
};
const cancelEdit = () => {
    editingId.value = null;
    editCaption.value = '';
};
const saveEdit = async (photoId: number | string) => {
    savingEdit.value = true;
    try {
        const res = await axios.patch(`/assets/${props.assetId}/tabs/photos/${photoId}`, { caption: editCaption.value || null });
        (props as any).data.photos = res.data.photos || [];
        toast('Caption updated', 'success');
        cancelEdit();
    } catch (e: any) {
        toast('Failed to update caption', 'danger');
    } finally {
        savingEdit.value = false;
    }
};
</script>

<template>
    <div>
        <div class="mb-4 rounded-xl border border-slate-200/70 bg-white/70 p-3 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <div class="flex flex-wrap items-center gap-2 text-sm">
                <input type="file" accept="image/*" @change="(e: any) => file = e.target.files?.[0] ?? null" class="text-xs" />
                <input v-model="caption" placeholder="Caption (optional)" class="rounded border border-slate-300 px-2 py-1 text-sm dark:border-slate-700 dark:bg-slate-900/70" />
                <button :disabled="!file || uploading" @click="upload" class="rounded-md bg-indigo-600 px-3 py-1.5 text-white disabled:opacity-50">Upload Photo</button>
                <span v-if="error" class="text-rose-600">{{ error }}</span>
            </div>
        </div>
        <div v-if="loading" class="grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
            <div v-for="i in 6" :key="i" class="h-48 animate-pulse rounded-2xl bg-slate-200/70 dark:bg-slate-700/50" />
        </div>

        <div v-else-if="!photos.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 text-sm text-slate-500 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 dark:text-slate-300">
            No photos have been uploaded for this asset yet.
        </div>

        <div v-else class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <div
                v-for="photo in photos"
                :key="photo.id"
                class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60"
            >
                <div class="h-48 w-full overflow-hidden bg-slate-200/80 dark:bg-slate-800/40">
                    <template v-if="photo.url">
                        <img :src="photo.url" :alt="photo.caption ?? 'Asset photo'" class="h-full w-full object-cover" />
                    </template>
                    <template v-else>
                        <div class="flex h-full w-full items-center justify-center text-xs text-slate-500 dark:text-slate-300">
                            No image available
                        </div>
                    </template>
                </div>
                <div class="px-4 py-3 text-xs text-slate-600 dark:text-slate-300">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <template v-if="editingId === Number(photo.id)">
                                <div class="flex items-center gap-2">
                                    <input v-model="editCaption" class="flex-1 rounded border border-slate-300 px-2 py-1 text-xs dark:border-slate-700 dark:bg-slate-900/70" />
                                    <button
                                        class="rounded-md bg-emerald-600 px-2 py-1 text-[11px] font-semibold uppercase tracking-wide text-white disabled:opacity-50"
                                        :disabled="savingEdit"
                                        @click="saveEdit(photo.id)"
                                    >
                                        Save
                                    </button>
                                    <button
                                        class="rounded-md border border-slate-400 px-2 py-1 text-[11px] font-semibold uppercase tracking-wide text-slate-600 dark:border-slate-600 dark:text-slate-300"
                                        :disabled="savingEdit"
                                        @click="cancelEdit"
                                    >
                                        Cancel
                                    </button>
                                </div>
                            </template>
                            <template v-else>
                                <p class="font-medium text-slate-700 dark:text-slate-100">{{ photo.caption ?? 'Untitled photo' }}</p>
                                <p class="mt-1 text-[11px] uppercase tracking-wide text-slate-500 dark:text-slate-400">
                                    {{ photo.uploaded_at ? new Date(photo.uploaded_at).toLocaleString() : 'Upload date unknown' }}
                                </p>
                            </template>
                        </div>
                        <button
                            class="rounded-full border border-rose-500 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-rose-600 transition hover:bg-rose-50 disabled:opacity-50 dark:border-rose-400 dark:text-rose-200 dark:hover:bg-rose-500/10"
                            :disabled="deleting === Number(photo.id)"
                            @click="remove(photo.id)"
                        >
                            Delete
                        </button>
                        <button
                            v-if="editingId !== Number(photo.id)"
                            class="rounded-full border border-slate-400 px-3 py-1 text-[11px] font-semibold uppercase tracking-wide text-slate-600 transition hover:bg-slate-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700/20"
                            @click="startEdit(photo)"
                        >
                            Edit
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
