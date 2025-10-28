<template>
  <div>
    <h1>Audit Report</h1>

    <h2>Summary</h2>
    <p>Total: {{ summary.total }}</p>
    <p>Found: {{ summary.found }}</p>
    <p>Missing: {{ summary.missing }}</p>

    <h2>Found Assets</h2>
    <ul>
      <li v-for="asset in found" :key="asset.id">
        {{ asset.asset_tag }} - {{ asset.description }}
      </li>
    </ul>

    <h2>Missing Assets</h2>
    <ul>
      <li v-for="asset in missing" :key="asset.id">
        {{ asset.asset_tag }} - {{ asset.description }}
      </li>
    </ul>

    <h2>Extra Assets</h2>
    <ul>
      <li v-for="asset in extras" :key="asset.id">
        {{ asset.asset_tag }} - {{ asset.description }}
      </li>
    </ul>
  </div>
</template>

<script>
export default {
  props: {
    audit: Object,
    summary: Object,
    found: Array,
    missing: Array,
    extras: Array,
  },
};
</script>
