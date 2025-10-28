<script setup lang="ts">
import { ref, watch } from 'vue';
import { ChevronDown, Filter, Search } from 'lucide-vue-next';

const props = withDefaults(
    defineProps<{
        modelValue: string;
        placeholder?: string;
        collapsible?: boolean;
        defaultOpen?: boolean;
        title?: string;
    }>(),
    {
        placeholder: 'Search…',
        collapsible: true,
        defaultOpen: true,
        title: 'Filters',
    },
);

const emit = defineEmits<{
    (event: 'update:modelValue', value: string): void;
    (event: 'toggle', open: boolean): void;
}>();

const internalSearch = ref(props.modelValue ?? '');
const open = ref(props.defaultOpen);

watch(
    () => props.modelValue,
    (value) => {
        if (value !== internalSearch.value) {
            internalSearch.value = value ?? '';
        }
    },
);

watch(internalSearch, (value) => {
    emit('update:modelValue', value);
});

const toggle = () => {
    if (! props.collapsible) {
        return;
    }

    open.value = ! open.value;
    emit('toggle', open.value);
};
</script>

<template>
    <div class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
        <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
            <div class="flex items-center gap-3">
                <span class="inline-flex size-8 items-center justify-center rounded-full bg-indigo-100 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-200">
                    <Filter class="size-4" />
                </span>
                <div>
                    <h2 class="text-sm font-semibold text-slate-900 dark:text-slate-100">
                        {{ title }}
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        Use the search box and advanced filters to refine this list.
                    </p>
                </div>
            </div>

            <div class="relative w-full max-w-md">
                <Search class="pointer-events-none absolute left-3 top-1/2 size-4 -translate-y-1/2 text-slate-400" />
                <input
                    v-model="internalSearch"
                    type="search"
                    class="w-full rounded-full border border-transparent bg-white/90 py-2.5 pl-10 pr-3 text-sm text-slate-700 shadow-sm outline-none transition focus:border-indigo-300 focus:ring-0 dark:bg-slate-950/80 dark:text-slate-200"
                    :placeholder="placeholder"
                />
            </div>

            <button
                v-if="collapsible"
                type="button"
                class="inline-flex items-center gap-2 rounded-full border border-slate-200/70 bg-white px-3 py-2 text-xs font-medium uppercase tracking-wide text-slate-600 transition hover:bg-slate-50 dark:border-slate-700 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                @click="toggle"
            >
                Advanced Filters
                <ChevronDown class="size-4 transition" :class="open ? 'rotate-180' : ''" />
            </button>
        </div>

        <transition name="fade">
            <div v-show="!collapsible || open" class="mt-6 grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <slot name="filters" />
            </div>
        </transition>

        <slot name="actions" />
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.2s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
