<script setup lang="ts">
import { computed } from 'vue';

interface StatCard {
    label: string;
    value: number | string;
    tone?: 'primary' | 'success' | 'warning' | 'muted';
}

interface DetailsPayload {
    asset?: Record<string, unknown>;
    stats?: StatCard[];
    warranties?: Array<Record<string, unknown>>;
    upcoming_maintenance?: Record<string, unknown> | null;
    upcoming_reservation?: Record<string, unknown> | null;
}

const props = withDefaults(
    defineProps<{
        data: DetailsPayload | null;
        loading: boolean;
    }>(),
    {
        data: null,
        loading: false,
    },
);

const asset = computed(() => props.data?.asset ?? {});
const stats = computed<StatCard[]>(() => props.data?.stats ?? []);
const warranties = computed(() => props.data?.warranties ?? []);
const upcomingMaintenance = computed(() => props.data?.upcoming_maintenance ?? null);
const upcomingReservation = computed(() => props.data?.upcoming_reservation ?? null);

const statToneClass = (tone?: string) => {
    switch (tone) {
        case 'success':
            return 'bg-emerald-500/10 text-emerald-600 dark:bg-emerald-500/20 dark:text-emerald-200';
        case 'warning':
            return 'bg-amber-500/10 text-amber-600 dark:bg-amber-500/20 dark:text-amber-200';
        case 'primary':
            return 'bg-indigo-500/10 text-indigo-600 dark:bg-indigo-500/20 dark:text-indigo-200';
        default:
            return 'bg-slate-500/10 text-slate-600 dark:bg-slate-500/20 dark:text-slate-200';
    }
};
</script>

<template>
    <div>
        <div v-if="loading" class="space-y-4">
            <div class="h-6 w-48 animate-pulse rounded bg-slate-200/70 dark:bg-slate-700/50" />
            <div class="h-32 w-full animate-pulse rounded bg-slate-200/70 dark:bg-slate-700/50" />
            <div class="h-24 w-full animate-pulse rounded bg-slate-200/70 dark:bg-slate-700/50" />
        </div>

        <div v-else class="space-y-6">
            <section class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Profile</h3>
                <dl class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Description</dt>
                        <dd class="text-sm text-slate-700 dark:text-slate-200">{{ asset.description ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Status</dt>
                        <dd class="text-sm text-slate-700 dark:text-slate-200">{{ asset.status ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Site</dt>
                        <dd class="text-sm text-slate-700 dark:text-slate-200">{{ asset.site ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Location</dt>
                        <dd class="text-sm text-slate-700 dark:text-slate-200">{{ asset.location ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Category</dt>
                        <dd class="text-sm text-slate-700 dark:text-slate-200">{{ asset.category ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Department</dt>
                        <dd class="text-sm text-slate-700 dark:text-slate-200">{{ asset.department ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Assigned To</dt>
                        <dd class="text-sm text-slate-700 dark:text-slate-200">{{ asset.assignee ?? '—' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">Purchase Date</dt>
                        <dd class="text-sm text-slate-700 dark:text-slate-200">{{ asset.purchase_date ?? '—' }}</dd>
                    </div>
                </dl>
            </section>

            <section v-if="stats.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">At a Glance</h3>
                <div class="mt-4 grid gap-3 sm:grid-cols-3">
                    <div
                        v-for="stat in stats"
                        :key="stat.label"
                        :class="['rounded-xl px-4 py-3 text-sm font-semibold', statToneClass(stat.tone)]"
                    >
                        <div class="text-xs uppercase tracking-wide opacity-80">{{ stat.label }}</div>
                        <div class="mt-1 text-lg">{{ stat.value }}</div>
                    </div>
                </div>
            </section>

            <section v-if="warranties.length" class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Recent Warranties</h3>
                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                    <div
                        v-for="warranty in warranties"
                        :key="warranty.id"
                        class="rounded-lg border border-slate-200/60 bg-slate-50/70 p-3 text-sm dark:border-slate-700/60 dark:bg-slate-800/40"
                    >
                        <p class="font-medium text-slate-700 dark:text-slate-200">{{ warranty.provider ?? 'Unknown provider' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400">
                            Expires {{ warranty.expiry_date ? new Intl.DateTimeFormat(undefined, { dateStyle: 'medium' }).format(new Date(warranty.expiry_date as string)) : 'N/A' }}
                        </p>
                        <p class="mt-1 text-xs text-slate-500 dark:text-slate-400">
                            Status: {{ warranty.is_active ? 'Active' : 'Inactive' }}
                        </p>
                    </div>
                </div>
            </section>

            <section v-if="upcomingMaintenance || upcomingReservation" class="rounded-2xl border border-slate-200/70 bg-white/80 p-5 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/60">
                <h3 class="text-sm font-semibold text-slate-700 dark:text-slate-200">Upcoming</h3>
                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                    <div v-if="upcomingMaintenance" class="rounded-lg border border-indigo-200/70 bg-indigo-50/60 p-3 text-sm text-indigo-700 dark:border-indigo-500/30 dark:bg-indigo-500/10 dark:text-indigo-200">
                        <p class="font-semibold">Maintenance</p>
                        <p class="text-xs opacity-80">{{ upcomingMaintenance.title ?? 'Scheduled maintenance' }}</p>
                        <p class="mt-1 text-xs">Target: {{ upcomingMaintenance.scheduled_for ? new Date(upcomingMaintenance.scheduled_for as string).toLocaleString() : 'N/A' }}</p>
                        <p class="text-xs">Status: {{ upcomingMaintenance.status ?? 'Scheduled' }}</p>
                    </div>
                    <div v-if="upcomingReservation" class="rounded-lg border border-emerald-200/70 bg-emerald-50/60 p-3 text-sm text-emerald-700 dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-200">
                        <p class="font-semibold">Reservation</p>
                        <p class="text-xs opacity-80">{{ upcomingReservation.requester ?? 'Reservation' }}</p>
                        <p class="mt-1 text-xs">
                            Starts: {{ upcomingReservation.start_at ? new Date(upcomingReservation.start_at as string).toLocaleString() : 'N/A' }}
                        </p>
                        <p class="text-xs">
                            Ends: {{ upcomingReservation.end_at ? new Date(upcomingReservation.end_at as string).toLocaleString() : 'N/A' }}
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</template>
