<template>
  <app-layout>
    <template #pageTitle
      >Paramètre des jours de fermeture: Trimestre
      {{ trimester.num_trimester }}</template
    >
    <a href="#" class="btn btn-success text-white mb-2" @click="showAddForm"
      ><i class="fa fa-plus"></i> Ajouter un jour de fermeture</a
    >
    <closing-table :TableData="closings" />

    <jet-modal
      id="addClosing"
      title="Ajouter un jour de fermeture"
      minHeight="425px"
    >
      <form-closing :trimester_id="this.trimester_id"></form-closing>
    </jet-modal>
  </app-layout>
</template>

<script>
import JetModal from "@/Jetstream/Modal";
import formClosing from "./form.vue";
import ClosingTable from "./ClosingDatatable.vue";

export default {
  components: {
    JetModal,
    formClosing,
    ClosingTable
  },
  props: ["closings", "trimester_id", "trimester"],
  methods: {
    showAddForm() {
      $("#addClosing").modal();
    }
  }
};
</script>


