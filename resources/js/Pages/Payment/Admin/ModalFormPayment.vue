<template>
  <jet-modal
    :id="'add-payment' + bill.id"
    maxWidth="xl"
    title="Ajouter un paiement"
  >
    <payment-form
      :bill="bill"
      :payment_methods="payment_methods"
      @paymen-saved="$emit('payment-saved')"
    />
    <div class="px-md-5">
      <info-invoice :bill_id="bill.id" :payment_method="bill.payment_method" />
    </div>
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
  props: ["bill", "payment_methods"]
};
</script>
