<template>
  <div>
    <h1>Audit Scan</h1>

    <input type="text" v-model="search" @input="searchAssets" placeholder="Search for assets...">

    <ul>
      <li v-for="asset in assets" :key="asset.id">
        {{ asset.asset.asset_tag }} - {{ asset.asset.description }}
        <button @click="markAsFound(asset)">Found</button>
        <button @click="markAsMissing(asset)">Missing</button>
        <input type="text" v-model="asset.notes" @blur="updateNotes(asset)" placeholder="Add notes...">
      </li>
    </ul>

    <button @click="completeAudit">Complete Audit</button>
  </div>
</template>

<script>
export default {
  props: {
    audit: Object,
  },
  data() {
    return {
      search: '',
      assets: [],
    };
  },
  created() {
    this.assets = this.audit.audit_assets;
  },
  methods: {
    searchAssets() {
      this.$inertia.get(`/audits/${this.audit.id}/scan/search`, { query: this.search }, { preserveState: true, replace: true });
    },
    markAsFound(asset) {
      this.$inertia.post(`/audits/${this.audit.id}/scan/assets/${asset.id}`, { found: true }, { preserveState: true, replace: true });
    },
    markAsMissing(asset) {
      this.$inertia.post(`/audits/${this.audit.id}/scan/assets/${asset.id}`, { found: false }, { preserveState: true, replace: true });
    },
    updateNotes(asset) {
      this.$inertia.post(`/audits/${this.audit.id}/scan/assets/${asset.id}`, { notes: asset.notes }, { preserveState: true, replace: true });
    },
    completeAudit() {
      this.$inertia.post(`/audits/${this.audit.id}/scan/complete`);
    },
  },
};
</script>