<template>
  <app-layout>
    <template #pageTitle
      >Paramètre prix de l'activité {{ activity.name.toUpperCase() }} de
      {{ season.establishment.name.toUpperCase() }} pendant la saison
      {{ season.year_start }} - {{ season.year_end }}</template
    >

    <div class="px-5 mt-4">
      <jet-validation-errors class="mb-3" />

      <div class="row">
        <div class="col-md-6">
          <h4>Prix par trimestre</h4>
          <template
            v-for="(trimester, index) in season.trimesters"
            :key="trimester.id"
          >
            <form-price
              :activity_id="activity.id"
              :title="'Trimestre ' + trimester.num_trimester"
              :error="'f'"
              type="trimestre"
              v-model:priceValue="form.trimesters[index].price"
              v-model:reducedPriceValue="form.trimesters[index].reduced_price"
              v-model:reducedPriceTwoValue="
                form.trimesters[index].reduced_price2
              "
            />
          </template>
        </div>
        <!-- +
            ' ' +
            dateDayFr(trimester.date_start) -->
        <!-- Passes has One session -->
        <div class="col-md-6">
          <h4>Prix séance à l'unité</h4>
          <form-price
            v-for="(pass, index) in passes_one_session"
            :key="pass.id"
            :activity_id="activity.id"
            :title="'1 ' + pass.name"
            :error="'f'"
            type="pass"
            :have_not_a_reduced_price="pass.name == 'COUP PAR COUP'"
            v-model:priceValue="form.passes[index].price"
            v-model:reducedPriceValue="form.passes[index].reduced_price"
          />
        </div>
      </div>
      <div class="row">
        <button
          type="submit"
          @click.prevent="submitForms"
          class="btn btn-success mx-auto"
        >
          Enregistrer
        </button>
      </div>
    </div>
  </app-layout>
</template>

<script>
import itemPrice from "./item.vue";
import formPrice from "./form.vue";
import JetValidationErrors from "@/Jetstream/ValidationErrors";

export default {
  components: {
    itemPrice,
    formPrice,
    JetValidationErrors
  },
  beforeMount() {
    for (let index = 0; index < this.passes_one_session.length; index++) {
      this.form.passes[index] = this.passes_one_session[index];
    }
    for (let index = 0; index < this.season.trimesters.length; index++) {
      this.form.trimesters[index] = this.season.trimesters[index];
    }
  },
  props: ["prices", "season", "activity", "passes_one_session", "errors"],
  data() {
    return {
      form: {
        trimesters: [],
        passes: []
      }
    };
  },
  methods: {
    submitForms() {
      this.$inertia.post(
        route("establishments.seasons.activities.prices.store", {
          establishment: this.season.establishment_id,
          season_id: this.season.id,
          activity_id: this.activity.id
        }),
        this.form
      );
    }
  }
};
</script>


