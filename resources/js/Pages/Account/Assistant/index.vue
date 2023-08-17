<template>
  <app-layout>
    <template #pageTitle>Liste des assistantes commerciales</template>
    <div class="row">
      <inertia-link
        :href="route('assistants.create', params)"
        class="btn btn-success rounded-0"
      >
        <i class="fa fa-plus"></i> Ajouter un assistante commerciale
      </inertia-link>
    </div>
    <assistant-table :TableData="users" />
  </app-layout>
</template>

<script>
import AssistantTable from "./AssistantDataTable.vue";
export default {
  components: {
    AssistantTable
  },
  props: ["users"]
};
</script>
