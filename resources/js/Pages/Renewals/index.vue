<template>
  <app-layout>
    <template #pageTitle>Liste des renouvellements</template>
    <filter-renewal />
    <renewal-table :TableData="renewals.data" />
  </app-layout>
</template>

<script>
import RenewalTable from "./RenewalDataTable";
import FilterRenewal from "./Filter";
export default {
  components: {
    FilterRenewal,
    RenewalTable
  },
  props: ["renewals"]
};
</script>


