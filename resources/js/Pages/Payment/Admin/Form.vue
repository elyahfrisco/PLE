<template>
  <div class="row">
    <div class="form-group col-md-6">
      <label>Date</label>
      <datepicker v-model="form.date" class="form-control" />

      <small class="validation-error" v-if="errors.amount">{{
        errors.amount
      }}</small>
    </div>
    <div class="form-group col-md-6">
      <label>Montant</label>
      <input
        type="number"
        v-model="form.amount"
        class="form-control"
        disabled
      />
      <small class="validation-error" v-if="errors.amount">{{
        errors.amount
      }}</small>
    </div>
    <div class="form-group col-md-12">
      <label>Mode de règlement</label>

      <Multiselect
        v-model="form.payment_method"
        :options="payment_methods"
        :canDeselect="false"
      />

      <small class="validation-error" v-if="errors.payment_method">{{
        errors.payment_method
      }}</small>
    </div>
    <div class="form-group col-md-12">
      <label>Note de paiement</label>
      <textarea v-model="form.description" class="form-control"></textarea>
      <small class="validation-error" v-if="errors.description">{{
        errors.description
      }}</small>
    </div>
    <div class="form-group col-md-12 text-center">
      <button class="btn btn-success" @click="submitPayment">
        Enregistrer
      </button>
    </div>
  </div>
</template>

<script>
export default {
  props: ["bill", "payment_methods"],
  data() {
    return {
      form: {},
      select: {}
    };
  },
  methods: {
    submitPayment() {
      let form = this.form;
      form.date = this.dateAng(form.date);
      this.$inertia.post(route("payments.store"), form, {
        onSuccess: () => {
          $("#add-payment" + this.bill.id).modal("hide");
          this.$emit("payment-saved");
        }
      });
    }
  },
  mounted() {
    this.form.amount = this.bill.amount;
    this.form.payment_method = this.bill.payment_method;
    this.form.bill_id = this.bill.id;
    this.form.user_id = this.bill.user_id;
    this.form.date = new Date();
    this.form.description = "";
  }
};
</script>


