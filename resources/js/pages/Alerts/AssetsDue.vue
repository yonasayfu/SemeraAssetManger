<script setup lang="ts">
import BaseList from './_BaseList.vue';
defineProps<{ alerts: any }>();
</script>

<template>
  <BaseList title="Assets Due" :alerts="alerts" />
</template>

