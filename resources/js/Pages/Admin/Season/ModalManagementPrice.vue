<template>
  <jet-modal
    :id="'modal-parameter-management-price-' + season.id"
    maxWidth="lg"
    :fade="false"
    :title="
      'Paramètre frais de GESTION ' +
        establishment.name +
        ',<br> Saison : ' +
        season.year_start +
        ' - ' +
        season.year_end
    "
  >
    <div class="row">
      <div class="col-md-6">
        <h4>Adulte</h4>
        <hr />
        <div class="form-group">
          <label>Frais normal adulte</label>
          <input
            type="number"
            class="form-control"
            v-model="form_adult.price_adult"
            required
            placeholder="Frais normal adulte"
          />
          <small class="validation-error" v-if="errors.price_adult">{{
            errors.price_adult
          }}</small>
        </div>
        <!-- <div class="form-group">
          <label>Frais réduit adulte</label>
          <input
            type="number"
            class="form-control"
            v-model="form_adult.reduced_price_adult"
            placeholder="Frais réduit adulte"
          />
          <small class="validation-error" v-if="errors.reduced_price_adult">{{
            errors.reduced_price_adult
          }}</small>
        </div> -->
      </div>
      <div class="col-md-6">
        <h4>Enfant</h4>
        <hr />
        <div class="form-group">
          <label>Frais normal enfant</label>
          <input
            type="number"
            class="form-control"
            v-model="form_child.price_child"
            required
            placeholder="Frais normal enfant"
          />
          <small class="validation-error" v-if="errors.price_child">{{
            errors.price_child
          }}</small>
        </div>
        <!-- <div class="form-group">
          <label>Frais réduit enfant</label>
          <input
            type="number"
            class="form-control"
            v-model="form_child.reduced_price"
            placeholder="Frais réduit enfant"
          />
          <small class="validation-error" v-if="errors.reduced_price_child">{{
            errors.reduced_price_child
          }}</small>
        </div> -->
      </div>
      <div class="col-12 text-center">
        <button type="button" @click.prevent="submit" class="btn btn-success">
          Enregistrer
        </button>
      </div>
    </div>
  </jet-modal>
</template>

<script>
export default {
  props: ["season"],
  data() {
    return {
      establishment: this.$page.props.establishment,
      form_adult: {
        price_adult: null,
        reduced_price_adult: null,
        category: "adult"
      },
      form_child: {
        price_child: null,
        reduced_price_child: null,
        category: "child"
      }
    };
  },
  beforeMount() {
    if (this.season.management_price) {
      for (var price of this.season.management_price) {
        if (price.category == "adult") {
          this.form_adult = price;
          this.form_adult.price_adult = price.price;
          this.form_adult.reduced_price_adult = price.reduced_price;
        } else if (price.category == "child") {
          this.form_child = price;
          this.form_child.price_child = price.price;
          this.form_child.reduced_price_child = price.reduced_price;
        }
      }
    }
  },
  methods: {
    submit() {
      this.save(this.form_adult, () => this.save(this.form_child));
    },
    save(form_data, callback = null) {
      this.$inertia.post(
        route("establishments.seasons.prices.management", {
          establishment: this.season.establishment_id,
          season_id: this.season.id
        }),
        form_data,
        {
          preserveState: true,
          onSuccess: () => {
            if (callback) callback();
          }
        }
      );
    }
  }
};
</script>


