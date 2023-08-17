<template>
  <app-layout>
    <template #pageTitle
      >Trimestres saison {{ season.year_start }} - {{ season.year_end }}
    </template>
    <div class="row">
      <a
        href="#"
        class="btn btn-success text-white mb-2 rounded-0"
        @click.prevent="showCreateForm"
      >
        <i class="fa fa-plus"></i> Ajouter un trimestre
      </a>
    </div>
    <div class="row">
      <!-- <item-trimester
        v-for="(trimester, index) in trimesters"
        v-bind:key="trimester.id"
        :is_last="index == trimesters.length - 1"
        :trimester="trimester"
        v-on:close_other_form="closeForms"
      ></item-trimester> -->
    </div>

    <trimester-table :TableData="trimesters" />

    <jet-modal id="addTrimester" title="Ajouter trimestre">
      <form-trimester :seasonId="this.season_id"> </form-trimester>
    </jet-modal>
  </app-layout>
</template>

<script>
import itemTrimester from "./item.vue";
import formTrimester from "./form.vue";
import JetModal from "@/Jetstream/Modal";
import TrimesterTable from "./TrimesterDatatable.vue";

export default {
  components: {
    itemTrimester,
    formTrimester,
    JetModal,
    TrimesterTable
  },
  props: ["trimesters", "season_id", "season"],
  methods: {
    showCreateForm() {
      $("#addTrimester").modal();
    }
  }
};
</script>


