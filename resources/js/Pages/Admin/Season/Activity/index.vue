<template>
  <app-layout>
    <template #pageTitle
      >Activités du centre {{ establishment.name.toUpperCase() }} pendant la
      saison {{ season.year_start }} - {{ season.year_end }}</template
    >
    <activity-table :TableData="activities" :season="season" />
  </app-layout>
</template>

<script>
import ActivityTable from "./ActivityDatatable.vue";
export default {
  props: ["establishment", "season", "activities", "activitiesNotAttached"],
  components: {
    ActivityTable
  }
};
</script>


