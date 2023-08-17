<template>
  <inertia-data-table
    id="table_contact"
    :searchable="true"
    :pagination="TableData"
  >
    <template #header>
      <table-header name="created_at">Date</table-header>
      <table-header name="first_name">Expéditeur</table-header>
      <table-header name="object">Objet</table-header>
      <table-header name="content">Message</table-header>
      <table-header name="establishment_name">Centre</table-header>
      <th style="width: 127px" class="text-right">Actions</th>
    </template>
    <template #content>
      <tr
        v-for="item in TableData.data"
        :key="item.id"
        :id="'activity' + item.id"
      >
        <td class="text-capitalize">{{ dateHFr(item.created_at) }}</td>
        <td class="text-capitalize">{{ item.user.full_name }}</td>
        <td class="text-capitalize">{{ item.object }}</td>
        <td class="text-capitalize">{{ item.content_min }}</td>
        <td class="text-capitalize">
          {{ item.establishment ? item.establishment.name : "" }}
        </td>
        <td class="column-actions">
          <btn-show type="button" @click.prevent="showDetailContact(item.id)" />
          <btn-delete type="button" @click.prevent="deleteContact(item.id)" />
          <modal-detail-contact :contact="item" />
        </td>
      </tr>
    </template>
  </inertia-data-table>
</template>

<script>
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";
import ModalDetailContact from "./ModalDetailContact.vue";

export default {
  components: {
    InertiaDataTable,
    TableHeader,
    ModalDetailContact
  },
  props: ["TableData"],
  methods: {
    showDetailContact(id) {
      $("#modal-detail-contact-" + id).modal();
    },
    deleteContact(id) {
      this.$inertia.delete(route("contact.destroy", id), {
        onBefore: () => confirm("Supprimer le message?"),
        onSuccess: () => this.iReload(this)
      });
    }
  }
};
</script>


