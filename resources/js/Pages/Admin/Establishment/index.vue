<template>
  <app-layout>
    <template #pageTitle>Liste des centres</template>
    <div class="row">
      <inertia-link
        :href="route('establishments.create')"
        class="btn btn-success text-white mb-2 rounded-0"
      >
        <i class="fa fa-plus"></i> Ajouter Centre
      </inertia-link>
    </div>
    <establishment-table :TableData="establishments" />
    <!-- <a
      :href="route('establishments.plannings.index', establishment.id)"
      class="underline"
      >Planning général</a
    >
    <br />
    <a
      :href="route('establishments.plannings.sessions.index', establishment.id)"
      class="underline"
      >Calendrier des séances</a
    > -->
  </app-layout>
</template>

<script>
import EstablishmentTable from "./EstablishmentDatatable.vue";
export default {
  components: {
    EstablishmentTable
  },
  props: ["establishments"]
};
</script>


