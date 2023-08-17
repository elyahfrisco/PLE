<template>
  <jet-modal
    :id="'modal-detail-invoice-' + bill_id"
    maxWidth="xl"
    :fade="false"
    title="Détail facture"
  >
    <div class="px-2">
      <info-invoice :bill_id="bill_id" :subscription_id="subscription_id" />
    </div>
  </jet-modal>
</template>

<script>
import InfoInvoice from "@/Pages/Components/InfoInvoice.vue";
export default {
  props: ["bill_id", "subscription_id"],
  components: {
    InfoInvoice
  }
};
</script>


