<script setup lang="ts">
import { computed } from 'vue';
import { CheckCheck, Trash2 } from 'lucide-vue-next';

interface BulkAction {
    label: string;
    value: string;
    tone?: 'primary' | 'danger';
    confirmMessage?: string;
}

const props = defineProps<{
    selectedCount: number;
    actions: BulkAction[];
}>();

const emit = defineEmits<{
    (event: 'action', value: string): void;
    (event: 'clear'): void;
}>();

const hasSelection = computed(() => props.selectedCount > 0);

const handleAction = (value: string, confirmMessage?: string) => {
    if (! hasSelection.value) {
        return;
    }

    if (confirmMessage && ! window.confirm(confirmMessage)) {
        return;
    }

    emit('action', value);
};
</script>

<template>
    <div
        class="flex items-center justify-between rounded-2xl border border-indigo-200/70 bg-indigo-50/80 px-5 py-4 text-sm text-indigo-700 shadow-sm dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-200"
        :class="{ 'opacity-70': !hasSelection }"
    >
        <div class="flex items-center gap-3">
            <CheckCheck class="size-5" />
            <div>
                <p class="text-sm font-semibold">
                    {{ selectedCount }} selected
                </p>
                <p class="text-xs text-indigo-600/70 dark:text-indigo-100/70">
                    Choose a bulk action to apply to the selected records.
                </p>
            </div>
        </div>

        <div class="flex items-center gap-2">
            <button
                v-for="action in actions"
                :key="action.value"
                type="button"
                class="rounded-full px-3 py-2 text-xs font-semibold uppercase tracking-wide transition focus:outline-none focus:ring-2 focus:ring-indigo-400"
                :class="[
                    action.tone === 'danger'
                        ? 'bg-rose-100 text-rose-600 hover:bg-rose-200 dark:bg-rose-500/20 dark:text-rose-200 dark:hover:bg-rose-500/30'
                        : 'bg-indigo-100 text-indigo-600 hover:bg-indigo-200 dark:bg-indigo-500/20 dark:text-indigo-200 dark:hover:bg-indigo-500/30',
                    { 'pointer-events-none opacity-60': !hasSelection },
                ]"
                @click="handleAction(action.value, action.confirmMessage)"
            >
                {{ action.label }}
            </button>

            <button
                type="button"
                class="inline-flex items-center gap-1 rounded-full border border-transparent bg-white px-3 py-2 text-xs font-semibold uppercase tracking-wide text-slate-500 shadow-sm transition hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-indigo-400 dark:bg-slate-900 dark:text-slate-300 dark:hover:bg-slate-800"
                :disabled="!hasSelection"
                @click="emit('clear')"
            >
                <Trash2 class="size-4" />
                Clear
            </button>
        </div>
    </div>
</template>
