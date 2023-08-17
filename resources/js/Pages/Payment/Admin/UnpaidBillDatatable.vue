<template>
  <table id="table_paid_facture" class="display table datatable">
    <thead>
      <tr>
        <table-header
          v-for="label in labels"
          :style="'width:' + (label.width ? label.width + 'px' : '')"
          :key="label"
          :name="label.name"
        >
          {{ label.label }}
        </table-header>
        <th style="width: 127px" class="text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      <template v-if="TableData">
        <tr v-for="item in TableData" :key="item.id">
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
    </tbody>
  </table>
</template>

<script>
import ModalFormPayment from "./ModalFormPayment.vue";
export default {
  components: {
    ModalFormPayment
  },
  props: ["TableData", "payment_methods"],
  data() {
    return {
      labels: [],
      activeShowId: null,
      subscriptionData: {}
    };
  },

  methods: {
    showModalAddPayment(bill_id) {
      this.activeShowId = bill_id;
      setTimeout(() => {
        $("#add-payment" + bill_id).modal();
      }, 200);
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


