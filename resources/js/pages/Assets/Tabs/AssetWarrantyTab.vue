<script setup lang="ts">
import { computed, ref } from 'vue';
import axios from 'axios';
import { confirmDialog } from '@/lib/confirm';
import { useToast } from '@/composables/useToast';

interface WarrantyItem {
    id: number | string;
    provider?: string | null;
    description?: string | null;
    length_months?: number | null;
    start_date?: string | null;
    expiry_date?: string | null;
    active?: boolean;
    notes?: string | null;
}

const props = withDefaults(
    defineProps<{
        data: { warranties?: WarrantyItem[] } | null;
        loading: boolean;
        assetId: number;
    }>(),
    {
        data: null,
        loading: false,
    },
);

const warranties = computed<WarrantyItem[]>(() => props.data?.warranties ?? []);
const { show: toast } = useToast();

function formatDate(value?: string | null) {
    if (!value) return '—';
    try {
        const d = new Date(value);
        if (isNaN(d.getTime())) return value as string;
        return new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(d);
    } catch {
        return value as string;
    }
}

// Simple add warranty form
const provider = ref('');
const lengthMonths = ref<number | null>(null);
const startDate = ref('');
const expiryDate = ref('');
const active = ref(true);
const notes = ref('');
const saving = ref(false);
const error = ref<string | null>(null);

const addWarranty = async () => {
    if (!provider.value) { error.value = 'Provider required'; return; }
    saving.value = true; error.value = null;
    try {
        const res = await axios.post(`/assets/${props.assetId}/tabs/warranty`, {
            provider: provider.value,
            length_months: lengthMonths.value,
            start_date: startDate.value || null,
            expiry_date: expiryDate.value || null,
            active: active.value,
            notes: notes.value || null,
        });
        (props as any).data.warranties = res.data.warranties || [];
        provider.value=''; lengthMonths.value=null; startDate.value=''; expiryDate.value=''; active.value=true; notes.value='';
        toast('Warranty added', 'success');
    } catch (e: any) {
        error.value = 'Failed to add warranty';
        toast('Failed to add warranty', 'danger');
    } finally {
        saving.value = false;
    }
};

const toggling = ref<number | null>(null);
const toggleActive = async (w: WarrantyItem) => {
    toggling.value = Number(w.id);
    try {
        const res = await axios.patch(`/assets/${props.assetId}/tabs/warranty/${w.id}`, { active: !w.active });
        (props as any).data.warranties = res.data.warranties || [];
        toast('Warranty updated', 'success');
    } catch (e: any) {
        toast('Failed to update warranty', 'danger');
    } finally {
        toggling.value = null;
    }
};

const deleting = ref<number | null>(null);
const remove = async (warrantyId: number | string) => {
    const ok = await confirmDialog({ message: 'Delete this warranty? This action cannot be undone.' });
    if (!ok) return;
    deleting.value = Number(warrantyId);
    try {
        const res = await axios.delete(`/assets/${props.assetId}/tabs/warranty/${warrantyId}`);
        (props as any).data.warranties = res.data.warranties || [];
        toast('Warranty deleted', 'success');
    } catch (e: any) {
        toast('Failed to delete warranty', 'danger');
    } finally {
        deleting.value = null;
    }
};
</script>

<template>
    <div>
        <div class="mb-4 rounded-xl border border-slate-200/70 bg-white/70 p-3 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <div class="grid gap-2 md:grid-cols-5">
                <input v-model="provider" placeholder="Provider" class="rounded border border-slate-300 px-2 py-1 text-sm dark:border-slate-700 dark:bg-slate-900/70" />
                <input v-model.number="lengthMonths" type="number" min="0" placeholder="Months" class="rounded border border-slate-300 px-2 py-1 text-sm dark:border-slate-700 dark:bg-slate-900/70" />
                <input v-model="startDate" type="date" class="rounded border border-slate-300 px-2 py-1 text-sm dark:border-slate-700 dark:bg-slate-900/70" />
                <input v-model="expiryDate" type="date" class="rounded border border-slate-300 px-2 py-1 text-sm dark:border-slate-700 dark:bg-slate-900/70" />
                <label class="inline-flex items-center gap-2 text-sm"><input type="checkbox" v-model="active" /> Active</label>
            </div>
            <div class="mt-2 flex items-center gap-2">
                <input v-model="notes" placeholder="Notes (optional)" class="flex-1 rounded border border-slate-300 px-2 py-1 text-sm dark:border-slate-700 dark:bg-slate-900/70" />
                <button :disabled="saving || !provider" @click="addWarranty" class="rounded-md bg-indigo-600 px-3 py-1.5 text-white disabled:opacity-50">Add Warranty</button>
                <span v-if="error" class="text-rose-600">{{ error }}</span>
            </div>
        </div>
        <div v-if="loading" class="space-y-3">
            <div v-for="i in 4" :key="i" class="h-16 animate-pulse rounded-xl bg-slate-200/70 dark:bg-slate-700/50" />
        </div>

        <div v-else-if="!warranties.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-6 text-sm text-slate-500 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60 dark:text-slate-300">
            No warranties recorded for this asset.
        </div>

        <div v-else class="overflow-hidden rounded-2xl border border-slate-200/70 bg-white/80 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <table class="min-w-full divide-y divide-slate-200 text-sm dark:divide-slate-700">
                <thead class="bg-slate-50 dark:bg-slate-800/60">
                    <tr class="text-left text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300">
                        <th class="px-4 py-3">Provider</th>
                        <th class="px-4 py-3">Coverage</th>
                        <th class="px-4 py-3">Start</th>
                        <th class="px-4 py-3">Expires</th>
                        <th class="px-4 py-3">Status</th>
                        <th class="px-4 py-3 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    <tr
                        v-for="warranty in warranties"
                        :key="warranty.id"
                        class="bg-white/60 text-slate-700 dark:bg-slate-900/50 dark:text-slate-200"
                    >
                        <td class="px-4 py-3 font-medium">{{ warranty.provider ?? 'Unknown provider' }}</td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ warranty.length_months ? `${warranty.length_months} months` : '—' }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ formatDate(warranty.start_date) }}
                        </td>
                        <td class="px-4 py-3 text-xs text-slate-500 dark:text-slate-400">
                            {{ formatDate(warranty.expiry_date) }}
                        </td>
                        <td class="px-4 py-3">
                            <span
                                :class="[
                                    'inline-flex items-center rounded-full px-2 py-1 text-xs font-semibold uppercase tracking-wide',
                                    warranty.active ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-200' : 'bg-slate-200 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                                ]"
                            >
                                {{ warranty.active ? 'Active' : 'Inactive' }}
                            </span>
                        </td>
                        <td class="px-4 py-3 text-right space-x-2">
                            <button
                                class="inline-flex items-center rounded-full border border-slate-400 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-slate-600 transition hover:bg-slate-50 disabled:opacity-50 dark:border-slate-600 dark:text-slate-300 dark:hover:bg-slate-700/20"
                                :disabled="toggling === Number(warranty.id)"
                                @click="toggleActive(warranty)"
                            >
                                {{ warranty.active ? 'Set Inactive' : 'Set Active' }}
                            </button>
                            <button
                                class="inline-flex items-center rounded-full border border-rose-500 px-3 py-1 text-xs font-semibold uppercase tracking-wide text-rose-600 transition hover:bg-rose-50 disabled:opacity-50 dark:border-rose-400 dark:text-rose-200 dark:hover:bg-rose-500/10"
                                :disabled="deleting === Number(warranty.id)"
                                @click="remove(warranty.id)"
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
