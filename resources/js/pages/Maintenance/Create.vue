<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import InputError from '@/components/InputError.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

interface AssetOption {
    id: number;
    asset_tag: string;
}

interface SelectOption {
    label: string;
    value: string | null;
}

interface Defaults {
    asset_id?: number | null;
    scheduled_for?: string | null;
}

const props = defineProps<{
    assets: AssetOption[];
    statusOptions: SelectOption[];
    typeOptions: SelectOption[];
    frequencyOptions: SelectOption[];
    defaults?: Defaults;
}>();

const assetOptions = computed(() => props.assets ?? []);
const statusOptions = computed(() => props.statusOptions ?? []);
const typeOptions = computed(() => props.typeOptions ?? []);
const frequencyOptions = computed(() => props.frequencyOptions ?? []);

const normaliseDateTime = (value?: string | null) => {
    if (!value) {
        return '';
    }

    if (value.includes('T')) {
        return value.slice(0, 16);
    }

    return value.replace(' ', 'T').slice(0, 16);
};

const form = useForm({
    asset_id: props.defaults?.asset_id ?? '',
    title: '',
    description: '',
    maintenance_type: typeOptions.value[0]?.value ?? 'Preventive',
    status: statusOptions.value[0]?.value ?? 'Open',
    scheduled_for: normaliseDateTime(props.defaults?.scheduled_for ?? ''),
    completed_at: '',
    vendor: '',
    cost: '',
    labor_cost: '',
    parts_cost: '',
    is_recurring: false,
    recurrence_frequency: frequencyOptions.value[0]?.value ?? 'monthly',
    recurrence_interval: 1,
});

watch(
    () => form.is_recurring,
    (isRecurring) => {
        if (!isRecurring) {
            form.recurrence_frequency = frequencyOptions.value[0]?.value ?? 'monthly';
            form.recurrence_interval = 1;
        }
    },
);

const submit = () => {
    form.transform((data) => ({
        ...data,
        asset_id: data.asset_id ? Number(data.asset_id) : data.asset_id,
        scheduled_for: data.scheduled_for || null,
        completed_at: data.completed_at || null,
        cost: data.cost === '' ? null : data.cost,
        labor_cost: data.labor_cost === '' ? null : data.labor_cost,
        parts_cost: data.parts_cost === '' ? null : data.parts_cost,
        recurrence_frequency: data.is_recurring ? data.recurrence_frequency : null,
        recurrence_interval: data.is_recurring ? data.recurrence_interval : null,
    })).post(route('maintenance.store'), {
        preserveScroll: true,
        onFinish: () => form.transform((data) => data),
    });
};
</script>

<template>
    <AppLayout>
        <Head title="Add Maintenance" />

        <div class="space-y-6 p-6">
            <div>
                <Link
                    :href="route('maintenance.index')"
                    class="inline-flex items-center text-sm font-medium text-indigo-600 transition hover:text-indigo-500 dark:text-indigo-300 dark:hover:text-indigo-200"
                >
                    ← Back to maintenance
                </Link>
            </div>

            <GlassCard>
                <header class="mb-6 border-b border-slate-200 pb-4 dark:border-slate-700">
                    <h1 class="text-2xl font-semibold text-slate-900 dark:text-slate-100">
                        Add Maintenance Record
                    </h1>
                    <p class="mt-1 text-sm text-slate-600 dark:text-slate-400">
                        Capture maintenance work, assign it to an asset, and configure recurring schedules if needed.
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
                                Title<span class="text-rose-500">*</span>
                            </label>
                            <input
                                v-model="form.title"
                                type="text"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                placeholder="Quarterly HVAC servicing"
                                required
                            />
                            <InputError :message="form.errors.title" class="mt-1" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Maintenance Type<span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.maintenance_type"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            >
                                <option
                                    v-for="option in typeOptions"
                                    :key="option.value ?? option.label"
                                    :value="option.value ?? ''"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                            <InputError :message="form.errors.maintenance_type" class="mt-1" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Status<span class="text-rose-500">*</span>
                            </label>
                            <select
                                v-model="form.status"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            >
                                <option
                                    v-for="option in statusOptions"
                                    :key="option.value ?? option.label"
                                    :value="option.value ?? ''"
                                >
                                    {{ option.label }}
                                </option>
                            </select>
                            <InputError :message="form.errors.status" class="mt-1" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Scheduled For
                            </label>
                            <input
                                v-model="form.scheduled_for"
                                type="datetime-local"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            />
                            <InputError :message="form.errors.scheduled_for" class="mt-1" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Completed At
                            </label>
                            <input
                                v-model="form.completed_at"
                                type="datetime-local"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                            />
                            <InputError :message="form.errors.completed_at" class="mt-1" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Description
                            </label>
                            <textarea
                                v-model="form.description"
                                rows="4"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                placeholder="Outline the maintenance scope, technician instructions, or specific tasks."
                            />
                            <InputError :message="form.errors.description" class="mt-1" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Vendor
                            </label>
                            <input
                                v-model="form.vendor"
                                type="text"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                placeholder="Preferred vendor or technician"
                            />
                            <InputError :message="form.errors.vendor" class="mt-1" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Total Cost
                            </label>
                            <input
                                v-model="form.cost"
                                type="number"
                                step="0.01"
                                min="0"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                placeholder="0.00"
                            />
                            <InputError :message="form.errors.cost" class="mt-1" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Labor Cost
                            </label>
                            <input
                                v-model="form.labor_cost"
                                type="number"
                                step="0.01"
                                min="0"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                placeholder="0.00"
                            />
                            <InputError :message="form.errors.labor_cost" class="mt-1" />
                        </div>

                        <div>
                            <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                Parts Cost
                            </label>
                            <input
                                v-model="form.parts_cost"
                                type="number"
                                step="0.01"
                                min="0"
                                class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                placeholder="0.00"
                            />
                            <InputError :message="form.errors.parts_cost" class="mt-1" />
                        </div>
                    </div>

                    <div class="rounded-lg border border-slate-200 p-4 dark:border-slate-700">
                        <label class="flex items-center gap-3 text-sm font-medium text-slate-700 dark:text-slate-200">
                            <input
                                v-model="form.is_recurring"
                                type="checkbox"
                                class="size-4 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-900"
                            />
                            This maintenance recurs on a schedule
                        </label>

                        <div
                            class="mt-4 grid gap-4 md:grid-cols-2"
                            :class="{ 'opacity-50': !form.is_recurring }"
                        >
                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Frequency
                                </label>
                                <select
                                    v-model="form.recurrence_frequency"
                                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                    :disabled="!form.is_recurring"
                                >
                                    <option
                                        v-for="option in frequencyOptions"
                                        :key="option.value ?? option.label"
                                        :value="option.value ?? ''"
                                    >
                                        {{ option.label }}
                                    </option>
                                </select>
                                <InputError :message="form.errors.recurrence_frequency" class="mt-1" />
                            </div>

                            <div>
                                <label class="mb-1 block text-sm font-medium text-slate-700 dark:text-slate-300">
                                    Interval
                                </label>
                                <input
                                    v-model.number="form.recurrence_interval"
                                    type="number"
                                    min="1"
                                    max="12"
                                    class="block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm shadow-sm focus:border-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-100"
                                    :disabled="!form.is_recurring"
                                />
                                <InputError :message="form.errors.recurrence_interval" class="mt-1" />
                                <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                                    Number of frequency intervals between each occurrence.
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3">
                        <Link
                            :href="route('maintenance.index')"
                            class="inline-flex items-center rounded-lg border border-slate-300 px-4 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-100 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-700"
                        >
                            Cancel
                        </Link>
                        <button
                            type="submit"
                            class="inline-flex items-center rounded-lg bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500/70 focus:ring-offset-2 disabled:cursor-not-allowed disabled:bg-indigo-400"
                            :disabled="form.processing"
                        >
                            Save Maintenance
                        </button>
                    </div>
                </form>
            </GlassCard>
        </div>
    </AppLayout>
</template>
