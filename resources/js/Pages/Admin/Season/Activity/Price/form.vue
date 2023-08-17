<template>
  <div class="card card-body rounded-0">
    <label class="py-2">{{ form.title }}</label>
    <div class="row">
      <div class="form-group col-lg-4">
        <input
          type="number"
          class="form-control"
          :placeholder="'Prix normal ' + form.title"
          :value="priceValue"
          v-on:input="$emit('update:priceValue', $event.target.value)"
        />
        <small class="validation-error" v-if="error">{{ error }}</small>
      </div>
      <template v-if="have_not_a_reduced_price !== true">
        <div class="form-group col-lg-4">
          <input
            type="number"
            class="form-control"
            :placeholder="'Prix réduit ' + form.title"
            :value="reducedPriceValue"
            v-on:input="$emit('update:reducedPriceValue', $event.target.value)"
          />
          <small class="validation-error" v-if="error">{{ error }}</small>
        </div>
        <div v-if="type === 'trimestre'" class="form-group col-lg-4">
          <input
            type="number"
            class="form-control"
            :placeholder="'Prix réduit-2 ' + form.title"
            :value="reducedPriceTwoValue"
            v-on:input="
              $emit('update:reducedPriceTwoValue', $event.target.value)
            "
          />
          <small class="validation-error" v-if="error">{{ error }}</small>
        </div>
      </template>
    </div>
  </div>
</template>

<script>
export default {
  props: [
    "title",
    "priceValue",
    "reducedPriceValue",
    "reducedPriceTwoValue",
    "error",
    "type",
    "have_not_a_reduced_price"
  ],
  data() {
    return {
      form: {
        title: null
      },
      error: ""
    };
  },
  beforeMount() {
    this.form.title = this.title;
  }
};
</script>


