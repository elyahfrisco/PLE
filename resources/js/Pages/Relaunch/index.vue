<template>
  <app-layout>
    <template #pageTitle>Liste des relance programmé</template>
    <div class="row">
      <inertia-link
        :href="route('relaunchs.create')"
        class="btn btn-success text-white mb-2 rounded-0"
      >
        <i class="fa fa-plus"></i> Nouvelle relance/mail
      </inertia-link>
    </div>
    <relaunch-table :TableData="relaunchs" />
  </app-layout>
</template>

<script>
import RelaunchTable from "./RelaunchDatatable.vue";
export default {
  props: ["relaunchs"],
  components: {
    RelaunchTable
  }
};
</script>


