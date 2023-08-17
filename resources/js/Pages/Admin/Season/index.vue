<template>
  <app-layout>
    <template #pageTitle
      >Liste des saisons du centre
      <span class="text-uppercase">{{ establishment.name }}</span></template
    >
    <div class="row">
      <inertia-link
        :href="route('establishments.seasons.create', establishment.id)"
        class="btn btn-success text-white mb-2 rounded-0"
      >
        <i class="fa fa-plus"></i> Ajouter une saison
      </inertia-link>
    </div>
    <season-table :TableData="seasons" />
  </app-layout>
</template>

<script>
import itemSeason from "./item.vue";
import SeasonTable from "./SeasonDatatable.vue";

export default {
  components: {
    itemSeason,
    SeasonTable
  },
  props: ["seasons", "establishment"]
};
</script>


