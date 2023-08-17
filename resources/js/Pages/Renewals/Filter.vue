<template>
  <div class="row">
    <div class="form-group col-lg-3 col-md-4">
      <label>Centres</label>
      <Multiselect
        v-model="form.establishment_id"
        placeholder="--centres--"
        :options="$page.props.establishments_list"
        label="name"
        valueProp="id"
        @change="getSeasons"
      />
    </div>
    <div class="form-group col-lg-3 col-md-4">
      <label>Saison</label>
      <Multiselect
        v-model="form.season_id"
        placeholder="--saisons--"
        :options="select.seasons"
        :searchable="true"
        :required="true"
        :disabled="!select.seasons?.length"
        :loading="loadSeason"
        @change="getTrimesters"
      />
    </div>
    <div class="form-group col-lg-3 col-md-4">
      <label>Trimestre</label>

      <Multiselect
        v-model="form.num_trimester"
        placeholder="--Trimestres--"
        :options="select.trimesters"
        :disabled="!form.season_id || !select.trimesters?.length"
        :loading="loadTrimesters"
        :canDeselect="false"
        :searchable="true"
      />
    </div>
    <div class="form-group col-lg-3 col-md-4">
      <label>Activités</label>
      <Multiselect
        v-model="form.activity_id"
        placeholder="--activités--"
        :options="select.activities"
        :searchable="true"
        label="name"
        valueProp="id"
      />
    </div>
    <div class="form-group col-lg-3 col-md-4">
      <label>Status</label>
      <Multiselect
        v-model="form.renewal_status"
        placeholder="--statut--"
        :options="select.renewal_status"
        label="label"
        trackBy="label"
        valueProp="value"
      />
    </div>
    <div v-if="false" class="form-group col-lg-3 col-md-4">
      <label>Groupe</label>
      <Multiselect
        v-model="form.planning_id"
        :options="
          async function(query) {
            return await getPlannings(query);
          }
        "
        placeholder="--Saisir le nom du groupe--"
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
    <div class="form-group col-md">
      <label class="text-white">_</label>
      <label class="d-block border rounded px-2 pt-2 pb-1">
        <input
          type="checkbox"
          v-model="form.renewed_subscription_saved"
          class="ml-2"
        />
        Souscription effectué
      </label>
    </div>
    <div class="form-group col-md">
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
        season_id: null,
        num_trimester: null,
        activity_id: null,
        pass_id: null,
        planning_id: null,
        renewed_subscription_saved: null
      },
      disableSelectPlanning: false,
      loadPlanning: false,
      select: {
        renewal_status: []
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
              if (init) {
                this.getTrimesters(true);
              }
            });
        } else {
          this.select.seasons = [];
        }
      }, 100);
    },
    getTrimesters(init = false) {
      setTimeout(() => {
        if (this.form.season_id) {
          this.loadTrimesters = true;
          axios
            .get(
              route("api.seasons.trimesters", {
                season: this.form.season_id,
                group: true
              })
            )
            .then(response => {
              this.select.trimesters = response.data.map(a => {
                return {
                  value: a.num_trimester,
                  label:
                    "T " +
                    a.num_trimester +
                    " (" +
                    this.dateFrMin(a.date_start) +
                    " - " +
                    this.dateFrMin(a.date_end) +
                    ")",
                  ...a
                };
              });
              this.loadTrimesters = false;

              if (init) {
                setTimeout(() => {
                  if (this.q_.filterBy && this.q_.filterBy.num_trimester) {
                    this.form.num_trimester = this.q_.filterBy.num_trimester;
                  }
                }, 100);
              }
            });
        } else {
          this.select.trimesters = [];
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
          account_type: this.account_type,
          filterBy: this.filterEmpty(this.form)
        },
        preserveScroll: true
      };

      params.page ? delete params.page : 0;
      this.$emitter.emit("PageLoading");
      this.$inertia.visit(this.href, params);
    },
    resetFilters() {
      this.form.establishment_id = this.form.season_id = this.form.num_trimester = this.form.activity_id = this.form.renewal_status = this.form.renewed_subscription_saved = null;

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
    if (this.q_.filterBy && this.q_.filterBy.activity_id) {
      this.form.activity_id = this.q_.filterBy.activity_id;
    }

    if (this.q_.filterBy && this.q_.filterBy.planning_id) {
      this.$refs.selectPlanning.refreshOptions(() => {
        this.$refs.selectPlanning.select(this.form.planning_id);
      });
    }

    if (this.q_.filterBy && this.q_.filterBy.pass_id) {
      this.form.pass_id = this.q_.filterBy.pass_id;
    }

    if (
      this.q_.filterBy &&
      this.q_.filterBy.renewed_subscription_saved === "true"
    ) {
      this.form.renewed_subscription_saved = true;
    }

    axios.get(route("api.renewals.status")).then(response => {
      this.select.renewal_status = response.data.filter(
        status => status.value !== "not_informed"
      );

      setTimeout(() => {
        if (this.q_.filterBy && this.q_.filterBy.renewal_status) {
          this.form.renewal_status = this.q_.filterBy.renewal_status;
        }
      }, 100);
    });

    this.getSeasons(true);
    this.getActivities();
    this.getPlannings();
  }
};
</script>

<style scoped></style>
