<template>
  <div class="row">
    <div class="form-group col-md-2">
      <label>Activités</label>
      <Multiselect
        v-model="form.activity_id"
        placeholder="--activités--"
        :options="select.activities"
        :searchable="true"
      />
    </div>

    <div class="form-group col-md-2">
      <label>Pass</label>
      <Multiselect
        v-model="form.pass_id"
        placeholder="--pass--"
        :options="select.pass"
        :searchable="true"
      />
    </div>
    <div class="filter-date col-4">
      <label>Date de création</label>
      <div class="row">
        <div class="filter-date--start col-md-5">
          <datepicker
            :language="fr"
            v-model="form.minDate"
            class="form-control"
          />
        </div>
        <div
          class="
            filter-date--separator
            col-md-1
            py-1
            text-center
            align-self-center
          "
        >
          <span> à </span>
        </div>
        <div class="filter-date--end col-md-5">
          <datepicker
            :language="fr"
            v-model="form.maxDate"
            class="form-control"
          />
        </div>
      </div>
    </div>
    <div class="form-group col-lg-3 col-md-4">
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
    <div class="align-self-bottom">
      <label class="transparent">_</label>
      <button @click="resetFilter" class="btn btn-primary ml-2">
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
        planning_id: null,
        pass_id: null,
        minDate: null,
        maxDate: null,
        desc: true
      },
      select: {},
      loadPlanning: false,
      initForm: {
        establishment_id: null,
        activity_id: null,
        planning_id: null,
        pass_id: null,
        minDate: null,
        maxDate: null,
        desc: true
      }
    };
  },
  methods: {
    getSeasons() {
      setTimeout(() => {
        axios
          .get(
            route("api.establishments.seasons", {
              establishment: this.form.establishment_id
            })
          )
          .then(response => {
            if (response.data != undefined) {
              this.select.seasons = this.toSelect(
                response.data,
                "id",
                "season"
              );
              this.select.seasons = [
                ...this.select.seasons,
                {
                  value: null,
                  label: "Toutes saisons"
                }
              ];
            }
          });
      }, 100);
    },
    getActivities() {
      axios
        .get(
          route("api.establishments.activities", {
            establishment: this.form.establishment_id
          }),
          {
            params: {
              season_id: this.form.season_id
            }
          }
        )
        .then(response => {
          if (response.data != undefined) {
            this.select.activities = this.toSelect(response.data);
            this.select.activities = [
              ...this.select.activities,
              {
                value: null,
                label: "Toutes activités"
              }
            ];
          }
        });
    },
    getPass() {
      axios
        .get(
          route("api.establishments.passes", {
            establishment: this.form.establishment_id
          }),
          {
            params: {
              season_id: this.form.season_id
            }
          }
        )
        .then(response => {
          if (response.data != undefined) {
            this.select.pass = this.toSelect(response.data);
            this.select.pass = [
              ...this.select.pass,
              {
                value: null,
                label: "Tous les passes"
              }
            ];
          }
        });
    },
    changeFilter() {
      this.$emitter.emit("PageLoading");
      this.$emitter.emit("refreshTable", this.form);
    },
    resetFilter() {
      this.form = { ...this.initForm };
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
    }
  },
  mounted: function() {
    if (this.$page.props.default_establishment_id) {
      this.form.establishment_id = this.$page.props.default_establishment_id;
    } else {
      this.form.establishment_id = this.$page.props.establishments_list[0].id;
    }

    this.getSeasons(true);
    this.getActivities();
    this.getPass();
  },
  watch: {
    form: {
      deep: true,
      handler() {
        setTimeout(() => {
          this.changeFilter();
        }, 100);
      }
    }
  }
};
</script>

<style scoped></style>
