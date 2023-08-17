<template>
  <app-layout>
    <template #pageTitle
      >Paramètre prix pass {{ pass.name.toUpperCase() }} de
      {{ season.establishment.name.toUpperCase() }} pendant la saison
      {{ season.year_start }} - {{ season.year_end }}</template
    >
    <div class="px-5">
      <form-price
        :title="pass.name + ' ' + season.establishment.name"
        :errors="errors"
        v-model:priceValue="form.price"
        v-model:reducedPriceValue="form.reduced_price"
      ></form-price>
      <button
        type="submit"
        @click.prevent="submitForms"
        class="btn btn-success"
      >
        Enregistrer
      </button>
    </div>
  </app-layout>
</template>

<script>
import itemPrice from "./item.vue";
import formPrice from "./form.vue";
export default {
  components: {
    itemPrice,
    formPrice
  },
  beforeMount() {
    if (this.price) {
      this.form = this.price;
    }
  },
  props: ["price", "season", "pass", "errors"],
  data() {
    return {
      form: {
        price: null,
        reduced_price: null,
        pass_id: this.pass.id,
        establishment_id: this.season.establishment.id
      }
    };
  },
  methods: {
    submitForms() {
      this.$inertia.post(
        route("establishments.seasons.passes.prices.store", {
          establishment: this.season.establishment_id,
          season_id: this.season.id,
          pass_id: this.pass.id
        }),
        this.form,
        {
          onSuccess: () => {
            this.iReload(this);
          }
        }
      );
    }
  }
};
</script>


