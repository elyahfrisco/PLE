<template>
  <app-layout>
    <template #pageTitle
      >Paramètre des jours de fermeture:
      <template v-if="trimester"
        >Trimestre {{ trimester.num_trimester }}</template
      >
      <template v-if="season">
        Saison {{ season.year_start }}-{{ season.year_end }}</template
      >
      <template v-if="establishment">{{ establishment.name }}</template>
    </template>
    <div class="row">
      <a
        href="#"
        class="btn btn-success text-white mb-2 rounded-0"
        @click="showAddForm"
        ><i class="fa fa-plus"></i> Ajouter un jour de fermeture</a
      >
    </div>
    <closing-table :TableData="closings" />

    <jet-modal
      id="addClosing"
      title="Ajouter un jour de fermeture"
      minHeight="425px"
    >
      <form-closing
        :trimester="trimester"
        :establishment_id="establishment_id"
      ></form-closing>
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
  props: ["closings", "establishment", "season", "trimester"],
  methods: {
    showAddForm() {
      $("#addClosing").modal();
    }
  },
  data() {
    return {
      establishment_id: null
    };
  },
  beforeMount() {
    if (this.establishment) {
      this.establishment_id = this.establishment.id;
    } else if (this.season) {
      this.establishment_id = this.season.establishment_id;
    }
  }
};
</script>


