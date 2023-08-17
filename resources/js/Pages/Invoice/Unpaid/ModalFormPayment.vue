<template>
  <jet-modal
    :id="'add-payment' + billData.id"
    maxWidth="xl"
    title="Ajouter un paiement"
  >
    <template v-if="billData.id">
      <payment-form
        :bill="billData"
        :payment_methods="payment_methods"
        @paymen-saved="$emit('payment-saved')"
      />
      <div class="px-md-5">
        <info-invoice
          :bill_id="billData.id"
          :payment_method="billData.payment_method"
        />
      </div>
    </template>
  </jet-modal>
</template>

<script>
import PaymentForm from "./Form.vue";
import InfoInvoice from "@/Pages/Components/InfoInvoice.vue";
export default {
  components: {
    InfoInvoice,
    PaymentForm
  },
  props: ["bill", "bill_id", "payment_methods"],
  data() {
    return {
      billData: {}
    };
  },
  mounted() {
    this.bill ? (this.billData = this.bill) : this.getBillDetail();
  },
  methods: {
    getBillDetail() {
      axios
        .get(route("api.bill.detail", { bill_id: this.bill_id }))
        .then(response => {
          this.billData = response.data.data;
        });
    }
  }
};
</script>
