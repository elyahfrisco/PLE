<template>
  <inertia-data-table id="table_export" :pagination="TableData">
    <template #header>
      <table-header>Fichier</table-header>
      <table-header>Date de création</table-header>
      <table-header>Taille</table-header>
      <table-header>Auto-suppression</table-header>
      <th style="width: 127px" class="text-right">Actions</th>
    </template>

    <template #content>
      <template v-if="TableData">
        <tr v-for="item in TableData" :key="item.name">
          <td>
            <a
              :href="route('dowload-export-file', { file_name: item.name })"
              class="text-success pointer"
              data-toggle="tooltip"
              :title="'Cliquez pour télécharger : ' + item.name"
              target="_blank"
              >{{ item.name }}</a
            >
          </td>
          <td>{{ dateHFr(item.lastModified) }}</td>
          <td>{{ item.size }}</td>
          <td>{{ item.delete_at }}</td>
          <td class="column-actions">
            <a
              :href="route('dowload-export-file', { file_name: item.name })"
              class="btn btn-success btn-sm btn-action"
              data-toggle="tooltip"
              title="Télécharger"
              target="_blank"
            >
              <i class="fa fa-download"></i>
            </a>
          </td>
        </tr>
      </template>
      <tr v-else>
        <td colspan="6" class="text-center">Aucun fichier exporté</td>
      </tr>
    </template>
  </inertia-data-table>
</template>

<script>
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";

export default {
  components: {
    InertiaDataTable,
    TableHeader
  },
  props: ["TableData"]
};
</script>

<style scoped>
.ps {
  height: 100px;
}
</style>
