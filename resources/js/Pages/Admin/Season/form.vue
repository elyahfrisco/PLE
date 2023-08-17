<template>
  <form>
    <div class="form-group">
      <label>Année saison</label>

      <div class="row">
        <div class="form-group col-md-6">
          <label>Debut</label>
          <input
            type="number"
            class="form-control"
            v-model="form.year_start"
            :placeholder="'Debut - ex: ' + $moment().format('Y')"
          />

          <small class="validation-error" v-if="errors.year_start">{{
            errors.year_start
          }}</small>
        </div>
        <div class="form-group col-md-6">
          <label>Fin</label>
          <input
            type="number"
            class="form-control"
            v-model="form.year_end"
            :placeholder="'Fin - ex: ' + (+$moment().format('Y') + 1)"
          />

          <small class="validation-error" v-if="errors.year_end">{{
            errors.year_end
          }}</small>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="form-group col-md-6">
        <label>Date debut</label>

        <datepicker
          :lower-limit="minDate"
          :upper-limit="maxDate"
          v-model="form.date_start"
          class="form-control"
        />

        <small class="validation-error" v-if="errors.date_start">{{
          errors.date_start
        }}</small>
      </div>
      <div class="form-group col-md-6">
        <label>Date fin</label>

        <datepicker
          :lower-limit="minDateEnd"
          :upper-limit="maxDate"
          v-model="form.date_end"
          class="form-control"
        />

        <small class="validation-error" v-if="errors.date_end">{{
          errors.date_end
        }}</small>
      </div>
    </div>
    <div class="row" v-if="!form.id">
      <div class="form-group col-md-6">
        <div class="icheck-primary icheck-sm">
          <input
            type="checkbox"
            v-model="form.copy_preced_season_parameters"
            id="copy_preced_season_parameters"
          />
          <label for="copy_preced_season_parameters"
            >Copier les paramètres de la saison précédente</label
          >
        </div>
      </div>
    </div>

    <button
      type="submit"
      @click.prevent="submit"
      v-if="!form.id"
      class="btn btn-success"
    >
      Enregistrer
    </button>
    <button
      type="submit"
      @click.prevent="update"
      v-else
      class="btn btn-success"
    >
      Modifier
    </button>
  </form>
</template>

<script>
export default {
  props: {
    errors: Object,
    establishment: Object,
    season: Object
  },
  data() {
    return {
      form: {
        date_start: null,
        date_end: null,
        year_start: null,
        year_end: null,
        copy_preced_season_parameters: true
      },
      establishment_id: null
    };
  },
  beforeMount() {
    if (this.season) {
      this.form = this.season;
      this.form.date_start = new Date(this.form.date_start);
      this.form.date_end = new Date(this.form.date_end);
      this.establishment_id = this.season.establishment_id;
    }
    if (this.establishment) {
      this.establishment_id = this.establishment.id;
    }
  },
  methods: {
    submit() {
      var form = { ...this.form };

      form.date_start = this.dateAng(form.date_start);
      form.date_end = this.dateAng(form.date_end);

      this.$inertia.post(
        route("establishments.seasons.store", this.establishment_id),
        form
      );
    },
    update() {
      this.$inertia.put(route("establishments.seasons.update", this.form.id), {
        establishment: this.form.establishment_id,
        date_start: this.dateAng(this.form.date_start),
        date_end: this.dateAng(this.form.date_end),
        year_start: this.form.year_start,
        year_end: this.form.year_end
      });
    }
  },
  computed: {
    minDate() {
      if (this.form.year_start) {
        return new Date(this.form.year_start + "-01-01");
      }
    },
    maxDate() {
      if (this.form.year_end) {
        return new Date(this.form.year_end + "-12-31");
      }
    },
    minDateEnd() {
      if (this.form.date_start) {
        return new Date(this.$moment(this.form.date_start).add(1, "days"));
      }
    }
  }
};
</script>


