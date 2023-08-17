<template>
  <app-layout>
    <template #pageTitle>Liste souscription</template>
    <div class="row">
      <inertia-link
        :href="route('subscriptions.create')"
        class="btn btn-success text-white mb-2 rounded-0"
      >
        <i class="fa fa-plus"></i> Abonnez un client à un PASS/Activité
      </inertia-link>
    </div>
    <subscription-filter />
    <subscription-table :TableData="subscriptions" />
  </app-layout>
</template>

<script>
import SubscriptionTable from "./SubscriptionDatatable.vue";
import SubscriptionFilter from "./Filter.vue";
export default {
  props: ["subscriptions"],
  components: {
    SubscriptionTable,
    SubscriptionFilter
  }
};
</script>


