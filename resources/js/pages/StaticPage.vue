<script setup lang="ts">
import AppLayout from '@/layouts/AppLayout.vue';
import ResourceToolbar from '@/components/ResourceToolbar.vue';
import GlassCard from '@/components/GlassCard.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps<{ title: string; content?: string }>();

const breadcrumbs = [
  { title: 'Help & Support', href: '/help/about' },
  { title: props.title, href: location?.pathname || '#' },
];
</script>

<template>
  <Head :title="props.title" />
  <AppLayout :breadcrumbs="breadcrumbs">
    <div class="space-y-6">
      <ResourceToolbar
        :title="props.title"
        description="Helpful information, policies, and support resources."
        :show-create="false"
        :show-export="false"
        :show-print="false"
      />

      <GlassCard>
        <div class="prose max-w-none dark:prose-invert" v-html="props.content || ''" />
      </GlassCard>
    </div>
  </AppLayout>
</template>
