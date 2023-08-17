<template>
  <app-layout>
    <template #pageTitle
      >Passes de la saison {{ this.season.year_start }}-{{
        this.season.year_end
      }}</template
    >
    <attached-pass-table :TableData="passes" :season="season" />
    <item-pass-unattached
      v-if="this.passesNotAttached.length"
      :passesNotAttached="this.passesNotAttached"
      :season_id="this.season.id"
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
  props: ["passes", "season", "passesNotAttached"]
};
</script>


