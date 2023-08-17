<template>
  <app-layout>
    <template #pageTitle>Liste des activités</template>
    <div class="row">
      <inertia-link
        :href="route('activities.create')"
        class="btn btn-success text-white mb-2 rounded-0"
      >
        <i class="fa fa-plus"></i> Ajouter activité
      </inertia-link>
    </div>
    <activity-table :TableData="this.activities" />
  </app-layout>
</template>

<script>
import ActivityTable from "./ActivityDatatable.vue";
export default {
  components: {
    ActivityTable
  },
  props: ["activities"]
};
</script>


