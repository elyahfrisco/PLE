<template>
  <div class="row">
    <div class="form-group col-lg-3 col-md-3">
      <label>Activités</label>
      <Multiselect
        v-model="form.activity_id"
        placeholder="--activités--"
        :options="select.activities"
        label="name"
        valueProp="id"
      />
    </div>
    <div class="form-group col-lg-3 col-md-3">
      <label>Date de création</label>
      <input
        type="date"
        class="form-control"
        placeholder=""
        required
        v-model="form.created_date"
      />
    </div>
    <div class="form-group col-lg-3 col-md-3">
      <label>Status</label>
      <Multiselect
        v-model="form.subscription_status"
        placeholder="--statut--"
        :options="select.status"
        label="label"
        valueProp="value"
      />
    </div>
    <div class="form-group col-lg-3 col-md-3">
      <label>Groupe</label>
      <Multiselect
        v-model="form.planning_id"
        :options="
          async function(query) {
            return await getPlannings(query);
          }
        "
        placeholder="--Saisir le code groupe--"
        :filterResults="false"
        :resolveOnLoad="false"
        :minChars="2"
        :searchable="true"
        :disabled="disableSelectPlanning"
        :loading="loadPlanning"
        :clearOnSelect="false"
        delay="500"
        trackBy="group_name"
        label="group_name"
        valueProp="id"
        ref="selectPlanning"
        required
      />
    </div>
    <div class="form-group offset-md-6 col-lg-6 col-md-6 text-right">
      <label class="transparent d-block">_</label>
      <button @click.prevent="applyFilters" class="btn btn-success mb-1">
        <i class="fa fa-filter mr-2"></i> Appliquer les filtres
      </button>
      <button @click.prevent="resetFilters" class="btn btn-primary ml-2">
        <i class="fa fa-trash mr-2"></i> Retirer les filtres
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      form: {
        establishment_id: null,
        activity_id: null,
        pass_id: null,
        planning_id: null,
        renewed: false,
        renewed_subscription_saved: false,
        created_date: null
      },
      disableSelectPlanning: false,
      loadPlanning: false,
      select: {
        status: [
          { label: "Client", value: "customer" },
          { label: "Prospect", value: "prospect" },
          { label: "Ancien client", value: "old_customer" },
          { label: "List d'attente", value: "waiting_customer" }
        ]
      },
      mounted: false,
      loadSeason: false
    };
  },
  methods: {
    getSeasons(init = false) {
      setTimeout(() => {
        if (this.form.establishment_id) {
          this.loadSeason = !init;
          axios
            .get(
              route("api.establishments.seasons", this.form.establishment_id)
            )
            .then(response => {
              this.select.seasons = this.toSelect(
                response.data,
                "id",
                "season"
              );
              this.loadSeason = false;
            });
        } else {
          this.select.seasons = [];
        }
      }, 100);
    },
    getActivities() {
      axios.get(route("activities.index")).then(response => {
        if (response.data != undefined) {
          this.select.activities = response.data.activities;
        }
      });
    },
    getPass() {
      axios.get(route("passes.index")).then(response => {
        if (response.data != undefined) {
          this.select.passes = response.data.passes;
        }
      });
    },
    getPlannings(query = "") {
      if (
        (!this.mounted && this.q_.filterBy && this.q_.filterBy.planning_id) ||
        query.length
      ) {
        this.loadPlanning = query ? true : false;
        let params =
          !this.mounted && !query
            ? {
                planning_id: this.q_.filterBy?.planning_id
              }
            : {
                q: query
              };
        return axios.get(route("api.plannings.list", params)).then(response => {
          this.select.plannings = response.data;
          this.loadPlanning = false;
          if (!this.mounted && !query) {
            this.form.planning_id = +this.q_.filterBy.planning_id;
            this.mounted = true;
          }
          return this.select.plannings;
        });
      }
      return [];
    },
    applyFilters() {
      var params = {
        data: {
          q: this.q,
          account_type: this.form.subscription_status,
          filterBy: this.filterEmpty(this.form)
        },
        preserveScroll: true
      };

      params.page ? delete params.page : 0;
      this.$emitter.emit("PageLoading");
      this.$inertia.visit(this.href, params);
    },
    resetFilters() {
      this.form.establishment_id = this.form.activity_id = this.form.pass_id = this.form.renewed = this.form.renewed_subscription_saved = this.form.created_date = null;
      this.applyFilters();
    }
  },
  mounted: function() {
    this.href = this.url ? this.url : this.current_url;

    if (this.$page.props.default_establishment_id) {
      this.form.establishment_id = this.$page.props.default_establishment_id;
    } else if (this.q_.filterBy && this.q_.filterBy.establishment_id) {
      this.form.establishment_id = this.q_.filterBy.establishment_id;
    }

    if (this.q_.filterBy && this.q_.filterBy.season_id) {
      this.form.season_id = this.q_.filterBy.season_id;
    }
    if (this.q_.filterBy && this.q_.filterBy.season_id) {
      this.form.season_id = this.q_.filterBy.season_id;
    }
    if (this.q_.filterBy && this.q_.filterBy.created_date) {
      this.form.created_date = this.q_.filterBy.created_date;
    }

    if (this.q_.filterBy && this.q_.filterBy.planning_id) {
      this.$refs.selectPlanning.refreshOptions(() => {
        this.$refs.selectPlanning.select(this.form.planning_id);
      });
    }

    if (this.q_.filterBy && this.q_.filterBy.subscription_status) {
      this.form.subscription_status = this.q_.filterBy.subscription_status;
    }

    if (this.q_.filterBy && this.q_.filterBy.activity_id) {
      this.form.activity_id = this.q_.filterBy.activity_id;
    }

    if (this.q_.filterBy && this.q_.filterBy.pass_id) {
      this.form.pass_id = this.q_.filterBy.pass_id;
    }

    if (this.q_.filterBy && this.q_.filterBy.renewed === "true") {
      this.form.renewed = true;
    }

    if (
      this.q_.filterBy &&
      this.q_.filterBy.renewed_subscription_saved === "true"
    ) {
      this.form.renewed_subscription_saved = true;
    }

    // this.getSeasons(true);
    this.getActivities();
    // this.getPass();
    this.getPlannings();
  }
};
</script>

<style scoped></style>
