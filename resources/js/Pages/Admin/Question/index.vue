<template>
  <app-layout>
    <template #pageTitle>Liste des questions</template>
    <div class="row">
      <inertia-link
        :href="route('questions.create')"
        class="btn btn-success text-white mb-2 rounded-0"
      >
        <i class="fa fa-plus"></i> Ajouter une question
      </inertia-link>
    </div>
    <question-table :TableData="questions" />
  </app-layout>
</template>

<script>
import QuestionTable from "./QuestionDatatable.vue";
export default {
  components: {
    QuestionTable
  },
  props: ["questions"]
};
</script>


