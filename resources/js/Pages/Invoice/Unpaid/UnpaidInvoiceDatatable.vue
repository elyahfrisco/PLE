<template>
  <inertia-data-table
    :searchable="true"
    id="table_unpaid_facture"
    :pagination="TableData"
  >
    <template #header>
      <table-header>Date facture</table-header>
      <table-header>Client</table-header>
      <table-header>Saison</table-header>
      <table-header>Date previsible de règlement</table-header>
      <table-header>Montant</table-header>
      <table-header>Statut</table-header>
      <th style="width: 127px" class="text-right">Actions</th>
    </template>

    <template #content>
      <template v-if="TableData.data">
        <tr v-for="item in TableData.data" :key="item.id">
          <td>{{ dateHFr(item.created_at) }}</td>
          <td class="text-capitalize">
            <template v-if="item.user">
              {{ item.user.first_name }} {{ item.user.name }}
            </template>
            <template v-else> _client_ </template>
          </td>
          <td>{{ item.season.year_start }}-{{ item.season.year_end }}</td>
          <td>
            {{
              item.predictable_payment_date
                ? dateFr(item.predictable_payment_date)
                : "non défini"
            }}
          </td>
          <td>{{ item.amount }}€</td>
          <td>
            <span class="badge badge-danger">non réglé</span>
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
              class="btn btn-success"
              @click="showModalAddPayment(item.id)"
            >
              <i class="fa fa-money-check-alt"></i> Payer
            </button>
            <modal-form-payment
              v-if="activeShowId == item.id"
              :payment_methods="payment_methods"
              :bill="item"
              @payment-saved="$emit('refresh-list')"
            />
          </td>
        </tr>
      </template>
      <tr v-else>
        <td colspan="7" class="text-center">Aucun paiement à afficher</td>
      </tr>
    </template>
  </inertia-data-table>
</template>

<script>
import ModalFormPayment from "./ModalFormPayment.vue";
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";
import ModalInvoice from "@/Pages/Components/ModalInvoice.vue";

export default {
  components: {
    ModalFormPayment,
    InertiaDataTable,
    TableHeader,
    ModalInvoice
  },
  props: ["TableData", "payment_methods"],
  data() {
    return {
      labels: [],
      activeShowId: null,
      subscriptionData: {},
      activeBillDetailDisplayed: {}
    };
  },

  methods: {
    showModalAddPayment(bill_id) {
      this.activeShowId = bill_id;
      setTimeout(() => {
        $("#add-payment" + bill_id).modal();
      }, 200);
    },
    showDetailInvoice(bill) {
      this.activeBillDetailDisplayed = bill.id;
      setTimeout(() => {
        $("#modal-detail-invoice-" + bill.id).modal();
      }, 50);
    }
  },

  beforeMount() {
    this.labels.push({ label: "Date facture" });
    this.labels.push({ label: "Client" });
    this.labels.push({ label: "Saison" });
    this.labels.push({ label: "Date previsible de règlement" });
    this.labels.push({ label: "Montant" });
    this.labels.push({ label: "Statut" });
  }
};
</script>


