<template>
  <inertia-data-table id="table_contact" :pagination="TableData">
    <template #header>
      <table-header style="width: 127px">Creation</table-header>
      <table-header style="width: 127px">Utilisateur</table-header>
      <table-header style="width: 300px">Objet</table-header>
      <table-header>Bug</table-header>
      <th style="width: 127px" class="text-right">Actions</th>
    </template>
    <template #content>
      <tr
        v-for="item in TableData.data"
        :key="item.id"
        :id="'activity' + item.id"
      >
        <td class="text-capitalize">{{ item.created_at }}</td>
        <td class="text-capitalize">{{ item.user.full_name }}</td>
        <td class="text-capitalize">{{ item.title }}</td>
        <td class="text-capitalize" v-html="item.content"></td>
        <td class="column-actions">
          <!-- visit bug source page  -->
          <inertia-link
            :href="item.page"
            class="btn m-1 btn-primary"
            data-toggle="tooltip"
            data-placement="bottom"
            title="Voir la page source du bug"
          >
            <i class="fas fa-link"></i>
          </inertia-link>

          <button
            type="button"
            class="btn m-1"
            :class="{
              'btn-secondary': !item.resolved,
              'btn-success': item.resolved
            }"
            @click.prevent="toggleStatus(item.id, item.resolved)"
          >
            <i v-if="item.resolved" class="fa fa-check-square"></i>
            <i v-else class="fa fa-square"></i>
          </button>
          <btn-delete
            v-if="auth_user.id === item.user_id"
            type="button"
            @click.prevent="deleteBug(item.id)"
          />
        </td>
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
  props: ["TableData"],
  methods: {
    toggleStatus(id, resolved) {
      this.$inertia.put(
        route("bugs.update", id),
        { resolved: !resolved },
        {
          onBefore: () => confirm("Changer le statut du bug?"),
          onSuccess: () => this.iReload(this)
        }
      );
    },
    deleteBug(id) {
      this.$inertia.delete(route("bugs.destroy", id), {
        onBefore: () => confirm("Supprimer le message?"),
        onSuccess: () => this.iReload(this)
      });
    }
  }
};
</script>


