<template>
  <app-layout>
    <template #pageTitle>Liste souscription</template>
    <div class="row">
      <inertia-link
        :href="route('subscriptions.create')"
        class="btn btn-success text-white mb-2 rounded-0"
      >
        <i class="fa fa-plus"></i> Souscrir à un PASS/Activité
      </inertia-link>
    </div>
    <subscription-filter />
    <subscription-table :is_facture="false" :TableData="subscriptions" />
  </app-layout>
</template>

<script>
import SubscriptionFilter from "@/Pages/Admin/Subscription/Filter.vue";
import SubscriptionTable from "./SubscriptionDatatable.vue";
export default {
  props: ["subscriptions"],
  components: {
    SubscriptionTable,
    SubscriptionFilter
  }
};
</script>


