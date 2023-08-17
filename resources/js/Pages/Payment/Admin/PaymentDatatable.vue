<template>
  <inertia-data-table
    :searchable="true"
    id="table_paid_facture"
    :pagination="TableData"
  >
    <template #header>
      <table-header>Date de règlement</table-header>
      <table-header>Client</table-header>
      <table-header>Saison</table-header>
      <table-header>Date de facture</table-header>
      <table-header>Montant</table-header>
      <table-header>Statut</table-header>
      <table-header class="text-right">Actions</table-header>
    </template>
    <template #content>
      <template v-if="TableData.data.length != 0">
        <tr v-for="item in TableData.data" :key="item.id">
          <td>{{ dateFr(item.date) }}</td>
          <td class="text-capitalize">
            <template v-if="item.user">
              {{ item.user.first_name }} {{ item.user.name }}
            </template>
            <template v-else> _client_ </template>
          </td>
          <td>{{ item.season.year_start }}-{{ item.season.year_end }}</td>
          <td>{{ dateFr(item.created_at) }}</td>
          <td>{{ item.amount }}€</td>
          <td>
            <span class="badge badge-success">réglé</span>
          </td>
          <td class="column-actions">
            <button
              href="#"
              class="btn btn-info"
              data-toggle="tooltip"
              title="Détail de la facture"
              @click.prevent="showDetailInvoice(item)"
            >
              <i class="fa fa-eye"></i>
            </button>
            <modal-invoice
              v-if="activeBillDetailDisplayed == item.id"
              :bill_id="item.id"
            />
            <button
              href="#"
              class="btn btn-danger"
              data-toggle="tooltip"
              title="Supprimer le paiement"
              @click.prevent="deletePayment(item)"
            >
              <i class="fa fa-trash"></i>
            </button>
          </td>
        </tr>
      </template>
    </template>
  </inertia-data-table>
</template>
<script>
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import ModalFormPayment from "./ModalFormPayment.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";
import ModalInvoice from "@/Pages/Components/ModalInvoice.vue";

export default {
  components: {
    ModalFormPayment,
    InertiaDataTable,
    TableHeader,
    ModalInvoice
  },
  props: ["TableData"],
  data() {
    return {
      activeBillDetailDisplayed: {}
    };
  },
  methods: {
    deletePayment(id) {
      this.$inertia.delete(route("payments.destroy", id), {
        onBefore: () => confirm("Supprimer le paiement?"),
        onSuccess: () => this.$emit("refresh-list")
      });
    },
    showDetailInvoice(bill) {
      this.activeBillDetailDisplayed = bill.id;
      setTimeout(() => {
        $("#modal-detail-invoice-" + bill.id).modal();
      }, 50);
    }
  }
};
</script>


