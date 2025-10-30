<template>
  <Head title="Audit Wizard" />

  <AppLayout :breadcrumbs="[{ title: 'Audits', href: '/audits' }, { title: 'Audit Wizard', href: '/audits/wizard' }]">
    <div class="space-y-6">
      <ResourceToolbar
        title="Audit wizard"
        description="Create a new audit by following the steps below."
      />

      <div class="overflow-hidden rounded-xl border border-slate-200/70 bg-white/70 shadow-sm dark:border-slate-800/60 dark:bg-slate-900/50">
        <nav class="border-b border-slate-200/70 dark:border-slate-800/60">
          <ol role="list" class="flex items-center divide-x divide-slate-200/70 dark:divide-slate-800/60">
            <li v-for="(step, index) in steps" :key="step.id" class="flex-1">
              <a
                :class="[
                  'group flex items-center justify-center py-4 text-sm font-medium',
                  index <= currentStepIndex ? 'text-indigo-600 dark:text-indigo-300' : 'text-slate-500 dark:text-slate-400',
                  index < currentStepIndex ? 'hover:text-indigo-700 dark:hover:text-indigo-200' : '',
                ]"
              >
                <span
                  :class="[
                    'flex h-8 w-8 items-center justify-center rounded-full',
                    index <= currentStepIndex
                      ? 'bg-indigo-600 text-white dark:bg-indigo-300 dark:text-slate-900'
                      : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
                    index < currentStepIndex ? 'group-hover:bg-indigo-700 dark:group-hover:bg-indigo-200' : '',
                    'mr-3',
                  ]"
                >
                  {{ index + 1 }}
                </span>
                <span>{{ step.name }}</span>
              </a>
            </li>
          </ol>
        </nav>

        <form @submit.prevent="submit">
          <div v-if="currentStep === 'info'" class="px-6 py-5 space-y-4">
            <GlassCard>
              <div>
                <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Audit Name</label>
                <input id="name" type="text" v-model="form.name" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100" />
                <InputError :message="form.errors.name" class="mt-2" />
              </div>
              <div>
                <label for="site_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Site</label>
                <select id="site_id" v-model="form.site_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100">
                  <option :value="null">Select a site</option>
                  <option v-for="site in props.sites" :key="site.id" :value="site.id">{{ site.name }}</option>
                </select>
                <InputError :message="form.errors.site_id" class="mt-2" />
              </div>
              <div>
                <label for="location_id" class="block text-sm font-medium text-slate-700 dark:text-slate-200">Location</label>
                <select id="location_id" v-model="form.location_id" class="mt-1 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-400/40 dark:border-slate-700 dark:bg-slate-900/40 dark:text-slate-100">
                  <option :value="null">Select a location</option>
                  <option v-for="location in filteredLocations" :key="location.id" :value="location.id">{{ location.name }}</option>
                </select>
                <InputError :message="form.errors.location_id" class="mt-2" />
              </div>
            </GlassCard>
            <div class="flex justify-end">
              <GlassButton type="button" @click="nextStep">Next</GlassButton>
            </div>
          </div>

          <div v-if="currentStep === 'assets'" class="px-6 py-5 space-y-4">
            <GlassCard>
              <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Select Assets to Audit</h3>
              <p class="text-sm text-slate-600 dark:text-slate-300">Choose the assets you want to include in this audit.</p>
              <div class="mt-4 grid gap-2 md:grid-cols-2">
                <label
                  v-for="asset in props.assets"
                  :key="asset.id"
                  class="flex cursor-pointer items-center gap-3 rounded-lg border border-slate-200/70 bg-white/70 px-3 py-2 text-sm text-slate-700 transition hover:border-indigo-300 hover:bg-white dark:border-slate-700 dark:bg-slate-900/60 dark:text-slate-200"
                >
                  <input
                    type="checkbox"
                    class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                    :checked="form.asset_ids.includes(asset.id)"
                    @change="toggleAsset(asset.id)"
                  />
                  <span>{{ asset.name }} ({{ asset.asset_tag }})</span>
                </label>
              </div>
              <InputError :message="form.errors.asset_ids" class="mt-2" />
            </GlassCard>
            <div class="flex justify-between">
              <GlassButton type="button" variant="secondary" @click="prevStep">Previous</GlassButton>
              <GlassButton type="button" @click="nextStep">Next</GlassButton>
            </div>
          </div>

          <div v-if="currentStep === 'review'" class="px-6 py-5 space-y-4">
            <GlassCard>
              <h3 class="text-lg font-semibold text-slate-900 dark:text-slate-100">Review Audit Details</h3>
              <p class="text-sm text-slate-600 dark:text-slate-300">Confirm the information below before starting the audit.</p>
              <div class="mt-4 space-y-2">
                <p><strong>Audit Name:</strong> {{ form.name }}</p>
                <p><strong>Site:</strong> {{ props.sites.find(s => s.id === form.site_id)?.name ?? 'N/A' }}</p>
                <p><strong>Location:</strong> {{ props.locations.find(l => l.id === form.location_id)?.name ?? 'N/A' }}</p>
                <p><strong>Selected Assets:</strong>
                  <span v-if="form.asset_ids.length === 0">None</span>
                  <ul v-else class="list-disc list-inside">
                    <li v-for="assetId in form.asset_ids" :key="assetId">
                      {{ props.assets.find(a => a.id === assetId)?.name }} ({{ props.assets.find(a => a.id === assetId)?.asset_tag }})
                    </li>
                  </ul>
                </p>
              </div>
            </GlassCard>
            <div class="flex justify-between">
              <GlassButton type="button" variant="secondary" @click="prevStep">Previous</GlassButton>
              <GlassButton type="submit" :disabled="form.processing">Start Audit</GlassButton>
            </div>
          </div>
        </form>
      </div>
    </div>
  </AppLayout>
</template>

<script setup lang="ts">
import { Head, useForm, router } from '@inertiajs/vue3';
import AppLayout from '@/layouts/AppLayout.vue';
import GlassCard from '@/components/GlassCard.vue';
import GlassButton from '@/components/GlassButton.vue';
import InputError from '@/components/InputError.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import { useToast } from '@/composables/useToast';
import { ref, computed } from 'vue';
import { Location, Site, Category, Asset } from '@/types';

interface Props {
    sites: Site[];
    locations: Location[];
    categories: Category[];
    assets: Asset[];
}

const props = defineProps<Props>();

const { show } = useToast();

const steps = [
    { id: 'info', name: 'Audit Information' },
    { id: 'assets', name: 'Select Assets' },
    { id: 'review', name: 'Review and Start' },
];

const currentStepIndex = ref(0);
const currentStep = computed(() => steps[currentStepIndex.value].id);

const form = useForm({
    name: '',
    site_id: null,
    location_id: null,
    category_ids: [],
    asset_ids: [],
});

const nextStep = () => {
    if (currentStepIndex.value < steps.length - 1) {
        currentStepIndex.value++;
    }
};

const prevStep = () => {
    if (currentStepIndex.value > 0) {
        currentStepIndex.value--;
    }
};

const submit = () => {
    form.post('/audits/wizard', {
        onSuccess: () => {
            show('Audit created successfully.', 'success');
        },
        onError: () => {
            show('Failed to create audit.', 'danger');
        },
    });
};

const filteredLocations = computed(() => {
    if (!form.site_id) {
        return props.locations;
    }
    return props.locations.filter(location => location.site_id === form.site_id);
});

const toggleAsset = (assetId: number) => {
    const index = form.asset_ids.indexOf(assetId);
    if (index === -1) {
        form.asset_ids.push(assetId);
    } else {
        form.asset_ids.splice(index, 1);
    }
};
</script>