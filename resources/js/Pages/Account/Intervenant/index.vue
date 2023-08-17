<template>
  <app-layout>
    <template #pageTitle>Liste des intervenants exterieurs</template>
    <div class="row">
      <inertia-link
        :href="route('intervenants.create', params)"
        class="btn btn-success rounded-0"
      >
        <i class="fa fa-plus"></i> Ajouter un intervenant exterieur
      </inertia-link>
    </div>
    <intervenant-table :TableData="users" />
  </app-layout>
</template>

<script>
import IntervenantTable from "./IntervenantDataTable.vue";
export default {
  components: {
    IntervenantTable
  },
  props: ["users"]
};
</script>
