<template>
  <app-layout>
    <template #pageTitle>Liste des administrateurs</template>
    <div class="row">
      <inertia-link
        :href="route('admins.create', params)"
        class="btn btn-success rounded-0"
      >
        <i class="fa fa-plus"></i> Ajouter un administrateur
      </inertia-link>
    </div>
    <admin-table :TableData="users" />
  </app-layout>
</template>

<script>
import AdminTable from "./AdminDataTable.vue";
export default {
  components: {
    AdminTable
  },
  props: ["users"]
};
</script>
