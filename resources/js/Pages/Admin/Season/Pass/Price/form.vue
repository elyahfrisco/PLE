<template>
  <div class="">
    <label class="py-3">{{ form.title }}</label>
    <div class="row">
      <div class="form-group col-md-6">
        <input
          type="number"
          class="form-control"
          :placeholder="'Prix normal ' + form.title"
          :value="priceValue"
          v-on:input="$emit('update:priceValue', $event.target.value)"
        />
        <small class="validation-error" v-if="errors.price">{{
          errors.price
        }}</small>
      </div>
      <div class="form-group col-md-6">
        <input
          type="number"
          class="form-control"
          :placeholder="'Prix reduit ' + form.title"
          :value="reducedPriceValue"
          v-on:input="$emit('update:reducedPriceValue', $event.target.value)"
        />
        <small class="validation-error" v-if="errors.reduced_price">{{
          errors.reduced_price
        }}</small>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["title", "priceValue", "reducedPriceValue", "errors"],
  data() {
    return {
      form: {
        title: null
      }
    };
  },
  beforeMount() {
    this.form.title = this.title;
  }
};
</script>


