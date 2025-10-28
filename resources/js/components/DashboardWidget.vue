<template>
  <div>
    <h3>{{ metric.label }}</h3>
    <p>{{ metric.value }}</p>
  </div>
</template>

<script>
export default {
  props: {
    metric: Object,
  },
};
</script>
