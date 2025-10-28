<template>
  <div>
    <h1>Audit Wizard</h1>

    <div v-if="currentStep === 'info'">
      <h2>Step 1: Audit Information</h2>
      <form @submit.prevent="nextStep">
        <div>
          <label for="name">Name</label>
          <input type="text" id="name" v-model="form.name">
        </div>
        <div>
          <label for="site_id">Site</label>
          <input type="text" id="site_id" v-model="form.site_id">
        </div>
        <div>
          <label for="location_id">Location</label>
          <input type="text" id="location_id" v-model="form.location_id">
        </div>
        <button type="submit">Next</button>
      </form>
    </div>

    <div v-if="currentStep === 'assets'">
      <h2>Step 2: Select Assets</h2>
      <!-- Add your asset selection logic here -->
      <button @click="prevStep">Previous</button>
      <button @click="nextStep">Next</button>
    </div>

    <div v-if="currentStep === 'review'">
      <h2>Step 3: Review and Start</h2>
      <!-- Add your review logic here -->
      <button @click="prevStep">Previous</button>
      <button @click="submit">Start Audit</button>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    steps: Array,
    initialData: Object,
  },
  data() {
    return {
      currentStep: 'info',
      form: {
        name: '',
        site_id: '',
        location_id: '',
        category_ids: [],
        asset_ids: [],
      },
    };
  },
  methods: {
    nextStep() {
      const currentIndex = this.steps.findIndex((step) => step.id === this.currentStep);
      if (currentIndex < this.steps.length - 1) {
        this.currentStep = this.steps[currentIndex + 1].id;
      }
    },
    prevStep() {
      const currentIndex = this.steps.findIndex((step) => step.id === this.currentStep);
      if (currentIndex > 0) {
        this.currentStep = this.steps[currentIndex - 1].id;
      }
    },
    submit() {
      this.$inertia.post(route('audits.wizard.store'), this.form);
    },
  },
};
</script>