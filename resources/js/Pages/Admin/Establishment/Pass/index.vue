<template>
  <app-layout>
    <template #pageTitle>Passes centre {{ this.establishment.name }}</template>

    <attached-pass-table :TableData="passes" />

    <item-pass-unattached
      v-if="this.passesNotAttached.length"
      :passesNotAttached="this.passesNotAttached"
      :establishment_id="this.establishment.id"
    ></item-pass-unattached>
  </app-layout>
</template>

<script>
import itemPassUnattached from "./unattached.vue";
import AttachedPassTable from "./AttachedPassDatatable.vue";

export default {
  components: {
    itemPassUnattached,
    AttachedPassTable
  },
  props: ["passes", "establishment", "passesNotAttached"]
};
</script>


