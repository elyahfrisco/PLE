<template>
  <app-layout>
    <template #pageTitle>Paramètre periode de souscription</template>
    <div class="row">
      <a
        href="#"
        class="btn btn-success text-white mb-2 rounded-0"
        @click.prevent="showCreateForm"
      >
        <i class="fa fa-plus"></i> Ajouter une période de souscription
      </a>
    </div>

    <subscription-table :TableData="subscriptionPeriod" />
    <jet-modal
      maxWidth="lg"
      id="createSubcriptionParameter"
      title="Ajouter une période de souscription"
    >
      <subscription-parameter-form />
    </jet-modal>
  </app-layout>
</template>

<script>
import SubscriptionParameterForm from "./form.vue";
import SubscriptionTable from "./ParameterSubscriptionDatatable.vue";
export default {
  components: {
    SubscriptionTable,
    SubscriptionParameterForm
  },
  props: ["subscriptionPeriod"],
  methods: {
    showCreateForm() {
      $("#createSubcriptionParameter").modal();
    }
  }
};
</script>


