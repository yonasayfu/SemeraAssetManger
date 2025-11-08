<script setup lang="ts">
import BaseList from './_BaseList.vue';
defineProps<{ alerts: any }>();
</script>

<template>
  <BaseList title="Maintenance Overdue" :alerts="alerts" />
</template>

