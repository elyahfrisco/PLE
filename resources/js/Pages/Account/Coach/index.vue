<template>
  <app-layout>
    <template #pageTitle>Liste des enseignants</template>
    <div class="row">
      <inertia-link
        :href="route('coach.create', params)"
        class="btn btn-success rounded-0"
      >
        <i class="fa fa-plus"></i> Ajouter un enseignant
      </inertia-link>
    </div>
    <coach-table :TableData="users" />
  </app-layout>
</template>

<script>
import profilPhoto from "@/Pages/Components/profilPhoto.vue";
import CoachTable from "./CoachDataTable.vue";
export default {
  components: {
    profilPhoto,
    CoachTable
  },
  props: ["users"]
};
</script>
