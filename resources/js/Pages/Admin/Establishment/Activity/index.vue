<template>
  <app-layout>
    <template #pageTitle
      >Activité
      <span v-if="!this.establishment.relaxation_center">du centre </span>
      {{ establishment.name }}</template
    >
    <attached-activity-table :TableData="activities" />

    <item-activity-unattached
      v-if="this.activitiesNotAttached.length"
      :activitiesNotAttached="this.activitiesNotAttached"
      :establishment_id="this.establishment.id"
    ></item-activity-unattached>
  </app-layout>
</template>

<script>
import itemActivityUnattached from "./unattached.vue";
import AttachedActivityTable from "./AttachedActivityDatatable.vue";

export default {
  components: {
    itemActivityUnattached,
    AttachedActivityTable
  },
  props: ["activities", "establishment", "activitiesNotAttached"]
};
</script>


