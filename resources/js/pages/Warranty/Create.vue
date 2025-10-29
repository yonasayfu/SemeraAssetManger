<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useToast } from '@/composables/useToast';
import { computed } from 'vue';

declare const route: any;

interface AssetOption {
    id: number;
    asset_tag: string;
}

interface Defaults {
    asset_id?: number | null;
    start_date?: string | null;
}

const props = defineProps<{
    assets: AssetOption[];
    defaults?: Defaults;
}>();

const assetOptions = computed(() => props.assets ?? []);

const form = useForm({
    asset_id: props.defaults?.asset_id ?? '',
    description: '',
    provider: '',
    length_months: 12,
    start_date: props.defaults?.start_date ?? '',
    expiry_date: '',
    notes: '',
});

const { show } = useToast();

const submit = () => {
    form.transform((data) => ({
        ...data,
        asset_id: data.asset_id ? Number(data.asset_id) : data.asset_id,
        expiry_date: data.expiry_date || null,
    })).post('/warranties', {
        preserveScroll: true,
        onFinish: () => form.transform((data) => data),
        onSuccess: () => show('Warranty created successfully.', 'success'),
        onError: () => show('Failed to create warranty.', 'danger'),
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Add Warranty" />

        <div class="space-y-6 p-6">
            <div>
                <Link
                    href="/warranties"
                    class="inline-flex items-center text-sm font-medium text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200"
                >
                    ← Back to warranties
                </Link>
            </div>

            <GlassCard>
                <header class="mb-6 border-b border-slate-200 pb-4 dark:border-slate-700">
                    <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                        Add Warranty
                    </h1>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                        Record warranty coverage for an asset, including provider details, coverage duration, and notes.
                    </p>
                </header>

                <form class="space-y-6" @submit.prevent="submit">
                    <div class="grid gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Asset<span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.asset_id"
                                class="block w-full rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                required
                            >
                                <option value="" disabled>Select an asset</option>
                                <option
                                    v-for="asset in assetOptions"
                                    :key="asset.id"
                                    :value="asset.id"
                                >
                                    {{ asset.asset_tag }}
                                </option>
                            </select>
                            <InputError :message="form.errors.asset_id" class="mt-1" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Provider
                            </label>
                            <input
                                v-model="form.provider"
                                type="text"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                placeholder="Manufacturer or vendor"
                            />
                            <InputError :message="form.errors.provider" class="mt-1" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Coverage Length (Months)<span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model.number="form.length_months"
                                type="number"
                                min="0"
                                max="120"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                required
                            />
                            <InputError :message="form.errors.length_months" class="mt-1" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Start Date<span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.start_date"
                                type="date"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                required
                            />
                            <InputError :message="form.errors.start_date" class="mt-1" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Expiry Date
                            </label>
                            <input
                                v-model="form.expiry_date"
                                type="date"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            />
                            <InputError :message="form.errors.expiry_date" class="mt-1" />
                            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                Leave blank to auto-calculate using the coverage length.
                            </p>
                        </div>
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Warranty Description<span class="text-rose-500">*</span>
                        </label>
                        <input
                            v-model="form.description"
                            type="text"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            placeholder="Example: Extended hardware protection"
                            required
                        />
                        <InputError :message="form.errors.description" class="mt-1" />
                    </div>

                    <div>
                        <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Notes
                        </label>
                        <textarea
                            v-model="form.notes"
                            rows="4"
                            class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            placeholder="Add coverage details, renewal instructions, or point-of-contact."
                        />
                        <InputError :message="form.errors.notes" class="mt-1" />
                    </div>

                    <div class="flex justify-end gap-3">
                        <Link
                            href="/warranties"
                            class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-indigo-400"
                            :disabled="form.processing"
                        >
                            Save Warranty
                        </button>
                    </div>
                </form>
            </GlassCard>
        </div>
    </AppLayout>
</template>
