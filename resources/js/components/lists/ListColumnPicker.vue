<script setup lang="ts">
import { computed, ref, watch } from 'vue';
import { Columns, CheckSquare } from 'lucide-vue-next';

interface ColumnOption {
    key: string;
    label: string;
    alwaysVisible?: boolean;
}

const props = defineProps<{
    columns: ColumnOption[];
    modelValue: string[];
}>();

const emit = defineEmits<{
    (event: 'update:modelValue', value: string[]): void;
}>();

const internal = ref<string[]>([...props.modelValue]);

watch(
    () => props.modelValue,
    (value) => {
        internal.value = [...value];
    },
);

const toggleColumn = (key: string) => {
    const exists = internal.value.includes(key);

    if (exists) {
        internal.value = internal.value.filter((column) => column !== key);
    } else {
        internal.value = [...internal.value, key];
    }

    emit('update:modelValue', [...internal.value]);
};

const visibleCount = computed(() => internal.value.length);
</script>

<template>
    <div class="relative inline-flex items-center gap-2 rounded-full border border-slate-200/70 bg-white px-3 py-2 text-xs text-slate-600 shadow-sm dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300">
        <Columns class="size-4 text-indigo-500 dark:text-indigo-300" />
        <span class="font-medium uppercase tracking-wide">Columns</span>
        <span class="rounded-full bg-slate-100 px-2 py-0.5 text-[10px] font-semibold text-slate-500 dark:bg-slate-800 dark:text-slate-300">
            {{ visibleCount }}
        </span>

        <div class="absolute left-0 top-full z-20 mt-2 w-64 rounded-xl border border-slate-200/80 bg-white p-3 text-sm shadow-xl ring-1 ring-black/5 dark:border-slate-700 dark:bg-slate-900/95">
            <p class="mb-2 text-xs font-semibold uppercase tracking-wide text-slate-500 dark:text-slate-400">
                Toggle visible columns
            </p>
            <ul class="flex max-h-60 flex-col gap-2 overflow-y-auto">
                <li
                    v-for="column in columns"
                    :key="column.key"
                    class="flex items-center gap-2 rounded-lg px-2 py-1 text-slate-600 transition hover:bg-slate-100 dark:text-slate-200 dark:hover:bg-slate-800/80"
                >
                    <input
                        :id="`column-${column.key}`"
                        class="size-3.5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 dark:border-slate-600 dark:bg-slate-900"
                        type="checkbox"
                        :checked="internal.includes(column.key)"
                        :disabled="column.alwaysVisible"
                        @change="toggleColumn(column.key)"
                    />
                    <label
                        :for="`column-${column.key}`"
                        class="flex items-center gap-2 text-xs font-medium text-slate-600 dark:text-slate-200"
                    >
                        <CheckSquare v-if="internal.includes(column.key)" class="size-4 text-indigo-500 dark:text-indigo-300" />
                        <span>{{ column.label }}</span>
                    </label>
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
div:hover > div {
    display: block;
}

div > div {
    display: none;
}
</style>
