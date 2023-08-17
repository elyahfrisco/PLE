<template>
  <form action="" class="text-black">
    <div class="row">
      <div class="form-group col-md-6">
        <label>Centre</label>
        <Multiselect
          v-model="form.establishment_id"
          placeholder="--centre--"
          :options="select.establishments"
          :searchable="true"
          :required="true"
          @change="getSeason"
        />
        <small class="validation-error" v-if="errors.establishment_id">{{
          errors.establishment_id
        }}</small>
      </div>
      <div class="form-group col-md-6">
        <label>Saison</label>
        <Multiselect
          v-model="form.season_id"
          placeholder="--saison--"
          :options="select.seasons"
          :searchable="true"
          :required="true"
          @change="getPass"
          :loading="loadSeason"
        />
        <small class="validation-error" v-if="errors.season_id">{{
          errors.season_id
        }}</small>
      </div>

      <div class="form-group col-md-12">
        <label>Pass</label>
        <Multiselect
          v-model="form.pass_id"
          placeholder="--pass--"
          :options="select.passes"
          :searchable="true"
          :required="true"
          :loading="loadPass"
        />
        <small class="validation-error" v-if="errors.pass_id">{{
          errors.pass_id
        }}</small>
      </div>

      <!-- <div class="form-group col-md-6">
        <label>Trimestre</label>
        <Multiselect
          v-model="form.trimester_id"
          placeholder="--saison--"
          :options="select.trimesters"
          :searchable="true"
          :required="true"
        />
        <small class="validation-error" v-if="errors.trimester_id">{{
          errors.trimester_id
        }}</small>
      </div> -->

      <div class="form-group col-md-6">
        <label>Date d'ouverture</label>
        <datepicker v-model="form.start_at" class="form-control" />
        <small class="validation-error" v-if="errors.start_at">{{
          errors.start_at
        }}</small>
      </div>

      <div class="form-group col-md-6">
        <label>Date de cloture</label>
        <datepicker v-model="form.end_at" class="form-control" />
        <small class="validation-error" v-if="errors.end_at">{{
          errors.end_at
        }}</small>
      </div>

      <div class="form-group col-md-12">
        <label>Déscription</label>
        <textarea v-model="form.description" class="form-control"></textarea>
        <small class="validation-error" v-if="errors.description">{{
          errors.description
        }}</small>
      </div>
    </div>

    <button
      class="btn btn-success"
      v-if="form.id"
      @click.prevent="updateSubscriptionParameter"
    >
      Modifier
    </button>
    <button
      class="btn btn-success"
      v-else
      @click.prevent="saveSubscriptionParameter"
    >
      Enregistrer
    </button>
  </form>
</template>

<script>
export default {
  props: ["subscriptionData"],
  data() {
    return {
      form: {
        start_at: null,
        end_at: null,
        description: null,
        establishment_id: null,
        season_id: null,
        pass_id: null
      },
      select: {},
      loadSeason: false,
      loadPass: false
    };
  },
  watch: {
    subscriptionData: {
      deep: true,
      handler() {
        this.form = cp(this.subscriptionData);
        this.getSeason(true);
        this.getPass(true);
        this.form.start_at = new Date(this.form.start_at);
        this.form.end_at = new Date(this.form.end_at);
      }
    }
  },
  beforeMount() {
    axios.get(route("api.establishments")).then(response => {
      var _data = response.data;
      this.select.establishments = _data.map(a => {
        return { value: a.id, label: a.name };
      });
    });
  },
  methods: {
    saveSubscriptionParameter() {
      this.$inertia.post(route("subscriptions.periodes.store"), this.form, {
        onSuccess: () => {
          this.iReload(this);
        }
      });
    },
    updateSubscriptionParameter() {
      var data = cp(this.form);
      data.start_at = this.dateAng(data.start_at);
      data.end_at = this.dateAng(data.end_at);
      this.$inertia.put(route("subscriptions.periodes.update", data), data, {
        onSuccess: () => {
          this.iReload(this);
        }
      });
    },
    getSeason(init = false) {
      setTimeout(() => {
        if (!init) {
          this.form.season_id = null;
          this.form.pass_id = null;
        }
        if (this.form.establishment_id) {
          this.loadSeason = true;
          axios
            .get(
              route("api.establishments.seasons", this.form.establishment_id)
            )
            .then(response => {
              this.select.seasons = response.data.map(a => {
                return {
                  value: a.id,
                  label: a.year_start + " - " + a.year_end
                };
              });
              this.loadSeason = false;
            });
        } else {
          this.select.seasons = {};
          this.select.passes = {};
        }
      }, 100);
    },
    getPass(init) {
      setTimeout(() => {
        if (!init) {
          this.form.pass_id = null;
        }
        if (this.form.establishment_id) {
          this.loadPass = true;
          axios
            .get(route("api.seasons.passes", this.form.season_id))
            .then(response => {
              this.select.passes = response.data.map(a => {
                return {
                  value: a.id,
                  label: a.name
                };
              });
              this.loadPass = false;
            });
        } else {
          this.select.passes = {};
        }
      }, 100);
    }
  }
};
</script>


