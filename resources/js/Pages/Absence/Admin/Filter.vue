<template>
  <div class="row">
    <div class="form-group col-md-2">
      <label>Centres</label>
      <Multiselect
        v-model="form.establishment_id"
        placeholder="--centres--"
        :options="$page.props.establishments_list"
        :required="true"
        :canDeselect="false"
        label="name"
        valueProp="id"
        @change="getSeasons"
      />
    </div>
    <div class="form-group col-md-2">
      <label>Saison</label>
      <Multiselect
        v-model="form.season_id"
        placeholder="--saisons--"
        :options="select.seasons"
      />
    </div>
    <div class="form-group col-md-2">
      <label>Activités</label>
      <Multiselect
        v-model="form.activity_id"
        placeholder="--activités--"
        :options="select.activities"
      />
    </div>
    <div class="form-group col-md-2">
      <label>Pass</label>
      <Multiselect
        v-model="form.pass_id"
        placeholder="--pass--"
        :options="select.pass"
      />
    </div>
    <div class="form-group col-md-2">
      <label class="text-white">_</label>
      <label class="d-block border rounded px-2 pt-2 pb-1">
        <input type="checkbox" v-model="form.upcoming" class="mr-2" /> Les
        absences à venir
      </label>
    </div>
  </div>
  <div class="row">
    <div class="filter-date col-4 row">
      <div class="filter-date--start col-md-5">
        <datepicker
          :language="fr"
          v-model="form.minDate"
          class="form-control"
        />
      </div>
      <div class="filter-date--separator col-md-1 py-1 text-center">
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
    <div>
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
        pass_id: null,
        upcoming: true,
        minDate: null,
        maxDate: null,
        desc: true
      },
      select: {}
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
      this.selectedActivity = this.minDate = this.maxDate = null;
      this.changeFilter();
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
