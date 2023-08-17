<template>
  <app-layout>
    <template #pageTitle>Liste des Pass</template>
    <div class="row">
      <inertia-link
        :href="route('passes.create')"
        class="btn btn-success text-white mb-2 rounded-0"
      >
        <i class="fa fa-plus"></i> Ajouter un Pass
      </inertia-link>
    </div>
    <pass-table :TableData="passes" />
  </app-layout>
</template>

<script>
import itemPass from "./item.vue";
import PassTable from "./PassDatatable.vue";
export default {
  components: {
    itemPass,
    PassTable
  },
  props: ["passes"]
};
</script>


