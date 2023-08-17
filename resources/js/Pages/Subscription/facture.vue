<template>
  <app-layout>
    <template #pageTitle>Liste de facture</template>
    <subscription-table :is_facture="true" :TableData="subscriptions" />
  </app-layout>
</template>

<script>
import SubscriptionTable from "./SubscriptionDatatable.vue";
export default {
  props: ["subscriptions"],
  components: {
    SubscriptionTable
  }
};
</script>


