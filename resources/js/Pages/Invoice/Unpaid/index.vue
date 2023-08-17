<template>
  <app-layout>
    <template #pageTitle>Liste Factures impayées</template>
    <invoice-filter />
    <unpaid-invoice-table
      :TableData="invoices"
      :payment_methods="payment_methods"
    />
  </app-layout>
</template>

<script>
import InvoiceFilter from "./filter.vue";
import UnpaidInvoiceTable from "./UnpaidInvoiceDatatable.vue";
export default {
  components: {
    InvoiceFilter,
    UnpaidInvoiceTable
  },
  props: ["invoices"],
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


