<template>
  <app-layout>
    <template #pageTitle>Liste Facture réglée</template>
    <payment-filter />
    <payment-table :TableData="payments" />
  </app-layout>
</template>

<script>
import PaymentFilter from "./filter.vue";
import PaymentTable from "./PaymentDatatable.vue";
export default {
  components: {
    PaymentFilter,
    PaymentTable
  },
  props: ["payments"],
  data() {
    return {
      filter: {},
      payment_methods: []
    };
  },
  mounted() {
    this.getPaymentMethods();
  },
  methods: {
    getPaymentMethods() {
      axios.get(route("api.payments.methods")).then(response => {
        if (response.data) {
          this.payment_methods = this.toSelect(response.data, "name");
        }
      });
    }
  }
};
</script>


