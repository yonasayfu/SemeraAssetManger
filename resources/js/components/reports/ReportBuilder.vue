<script setup lang="ts">
import axios from 'axios';
import { computed, reactive, ref, watch } from 'vue';

declare const route: (name: string, params?: Record<string, unknown>) => string;

type FilterType = 'text' | 'number' | 'select' | 'date' | 'date-range';

interface FilterOption {
    label: string;
    value: string | number | null;
}

interface FilterDefinition {
    key: string;
    label: string;
    type: FilterType;
    description?: string;
    options?: FilterOption[];
    placeholder?: string;
}

const props = withDefaults(
    defineProps<{
        family: string;
        filterDefinitions: FilterDefinition[];
        columns?: string[];
        initialPreview?: Array<Record<string, unknown>>;
        initialFilters?: Record<string, unknown>;
        notes?: string;
    }>(),
    {
        columns: () => [],
        initialPreview: () => [],
        initialFilters: () => ({}),
        notes: '',
    },
);

type FilterState = Record<string, unknown>;

const filterState = reactive<FilterState>({});
const filterDefinitions = computed(() => props.filterDefinitions);
const columns = computed(() => props.columns ?? []);
const previewRows = ref<Array<Record<string, unknown>>>(props.initialPreview ?? []);
const isPreviewLoading = ref(false);
const isExporting = ref(false);
const previewError = ref<string | null>(null);

const normaliseInitialFilters = () => {
    filterDefinitions.value.forEach((definition) => {
        const value = props.initialFilters?.[definition.key];

        switch (definition.type) {
            case 'date-range':
                filterState[definition.key] = {
                    start: value?.start ?? '',
                    end: value?.end ?? '',
                };
                break;
            default:
                filterState[definition.key] = value ?? '';
        }
    });
};

normaliseInitialFilters();

watch(
    () => props.initialFilters,
    () => normaliseInitialFilters(),
    { deep: true },
);

const hasPreviewData = computed(() => (previewRows.value?.length ?? 0) > 0);

const buildPayload = () => {
    const filters: Record<string, unknown> = {};

    filterDefinitions.value.forEach((definition) => {
        const value = filterState[definition.key];

        if (definition.type === 'date-range') {
            const range = value as { start?: string; end?: string } | undefined;
            const start = range?.start ?? '';
            const end = range?.end ?? '';

            if (start || end) {
                filters[definition.key] = {
                    start: start || null,
                    end: end || null,
                };
            }

            return;
        }

        if (value === undefined || value === null || value === '') {
            return;
        }

        if (definition.type === 'number') {
            const numeric = Number(value);
            if (! Number.isNaN(numeric)) {
                filters[definition.key] = numeric;
            }

            return;
        }

        filters[definition.key] = value;
    });

    return {
        family: props.family,
        filters,
    };
};

const handlePreview = async () => {
    isPreviewLoading.value = true;
    previewError.value = null;

    try {
        const response = await axios.post(route('reports.preview'), {
            ...buildPayload(),
            limit: 25,
        });

        previewRows.value = response.data?.data ?? [];
    } catch (error: any) {
        previewError.value = error?.response?.data?.message ?? 'Unable to load preview. Please try again.';
    } finally {
        isPreviewLoading.value = false;
    }
};

const handleExport = async (format: string) => {
    isExporting.value = true;
    previewError.value = null;

    try {
        const response = await axios.post(route('reports.export'), {
            ...buildPayload(),
            format,
        }, {
            responseType: 'blob',
        });

        const filename =
            response.headers['x-report-filename'] ??
            `report-${props.family}-${Date.now()}.${format.toLowerCase()}`;

        const blob = new Blob([response.data], {
            type: response.headers['content-type'] ?? 'text/csv',
        });

        const url = window.URL.createObjectURL(blob);
        const anchor = document.createElement('a');
        anchor.href = url;
        anchor.download = filename;
        anchor.click();
        window.URL.revokeObjectURL(url);
    } catch (error: any) {
        previewError.value = error?.response?.data?.message ?? 'Unable to export report.';
    } finally {
        isExporting.value = false;
    }
};
</script>

<template>
    <div class="space-y-6">
        <section class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Filters
            </h3>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                Configure the parameters for this report. Additional filters can be added later as we gather requirements.
            </p>

            <div class="mt-4 grid gap-4 sm:grid-cols-2">
                <div
                    v-for="definition in filterDefinitions"
                    :key="definition.key"
                    class="flex flex-col gap-2 rounded-xl border border-slate-200/60 bg-slate-50/70 p-3 text-sm dark:border-slate-700/60 dark:bg-slate-800/40"
                >
                    <label class="text-xs font-semibold uppercase tracking-wide text-slate-600 dark:text-slate-300">
                        {{ definition.label }}
                    </label>
                    <p v-if="definition.description" class="text-xs text-slate-500 dark:text-slate-400">
                        {{ definition.description }}
                    </p>

                    <template v-if="definition.type === 'select'">
                        <select
                            v-model="filterState[definition.key]"
                            class="rounded-lg border border-transparent bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        >
                            <option value="">Any</option>
                            <option
                                v-for="option in definition.options"
                                :key="String(option.value ?? option.label)"
                                :value="option.value"
                            >
                                {{ option.label }}
                            </option>
                        </select>
                    </template>

                    <template v-else-if="definition.type === 'date'">
                        <input
                            v-model="filterState[definition.key]"
                            type="date"
                            class="rounded-lg border border-transparent bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        />
                    </template>

                    <template v-else-if="definition.type === 'date-range'">
                        <div class="flex gap-2">
                            <input
                                v-model="(filterState[definition.key] as any).start"
                                type="date"
                                class="flex-1 rounded-lg border border-transparent bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                            />
                            <input
                                v-model="(filterState[definition.key] as any).end"
                                type="date"
                                class="flex-1 rounded-lg border border-transparent bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                            />
                        </div>
                    </template>

                    <template v-else>
                        <input
                            v-model="filterState[definition.key]"
                            :type="definition.type === 'number' ? 'number' : 'text'"
                            :placeholder="definition.placeholder ?? 'Enter value'"
                            class="rounded-lg border border-transparent bg-white px-3 py-2 text-sm text-slate-700 outline-none focus:border-indigo-300 focus:ring-0 dark:bg-slate-900/70 dark:text-slate-200"
                        />
                    </template>
                </div>
            </div>
        </section>

        <section class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <div class="flex flex-col gap-2">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                    Actions
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    Preview the dataset or export it for deeper analysis. CSV is available now; additional formats can be added later.
                </p>
            </div>

            <div class="mt-4 flex flex-wrap items-center gap-3">
                <button
                    type="button"
                    class="rounded-full bg-indigo-600 px-4 py-2 text-xs font-semibold uppercase tracking-wide text-white transition hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400 disabled:opacity-70"
                    :disabled="isPreviewLoading"
                    @click="handlePreview"
                >
                    <span v-if="isPreviewLoading">Loading&hellip;</span>
                    <span v-else>Preview Report</span>
                </button>

                <div class="flex items-center gap-1 rounded-full border border-indigo-200/70 bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-indigo-600 shadow-sm dark:border-indigo-500/40 dark:bg-slate-900 dark:text-indigo-200">
                    <span>Export</span>
                    <button
                        type="button"
                        class="rounded-full px-2 py-1 text-indigo-500 transition hover:bg-indigo-50 focus:outline-none focus:ring-1 focus:ring-indigo-400 dark:text-indigo-200 dark:hover:bg-indigo-500/20 disabled:opacity-70"
                        :disabled="isExporting"
                        @click="handleExport('csv')"
                    >
                        CSV
                    </button>
                </div>

                <span v-if="isExporting" class="text-xs text-indigo-500 dark:text-indigo-200">
                    Preparing export&hellip;
                </span>
            </div>

            <p v-if="previewError" class="mt-3 text-xs text-rose-500">
                {{ previewError }}
            </p>
        </section>

        <section class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
            <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">
                Preview
            </h3>
            <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                This table shows the first few rows returned by the API using the selected filters.
            </p>

            <div class="mt-4 overflow-hidden rounded-xl border border-slate-200/60 dark:border-slate-700/60">
                <table class="min-w-full divide-y divide-slate-200 text-xs dark:divide-slate-700">
                    <thead class="bg-slate-50 dark:bg-slate-800/60">
                        <tr>
                            <th
                                v-for="column in columns"
                                :key="column"
                                class="px-3 py-2 text-left font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-300"
                            >
                                {{ column }}
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                        <tr v-if="!hasPreviewData" class="bg-white/60 dark:bg-slate-900/50">
                            <td :colspan="columns.length || 1" class="px-3 py-6 text-center text-slate-500 dark:text-slate-400">
                                Run a preview to see recent data.
                            </td>
                        </tr>
                        <tr
                            v-for="(row, rowIndex) in previewRows"
                            :key="rowIndex"
                            class="bg-white/60 dark:bg-slate-900/50"
                        >
                            <td
                                v-for="column in columns"
                                :key="`${rowIndex}-${column}`"
                                class="px-3 py-2 text-slate-600 dark:text-slate-300"
                            >
                                {{ String(row[column] ?? row[column.toLowerCase()] ?? '—') }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <p v-if="notes" class="mt-3 text-xs text-slate-500 dark:text-slate-400">
                {{ notes }}
            </p>
        </section>
    </div>
</template>
