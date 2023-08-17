<template>
  <form class="row">
    <div class="form-group mr-1">
      <label>Centre</label>
      <Multiselect
        v-model="form.establishment_id"
        placeholder="--centres--"
        :options="select.establishments"
        :searchable="true"
        :required="true"
      />
    </div>
    <div class="form-group mr-1">
      <label>Saison</label>
      <Multiselect
        v-model="form.season_id"
        placeholder="--saisons--"
        :options="select.seasons"
        :searchable="true"
        :required="true"
        :loading="loadSeason"
      />
    </div>
    <div class="form-group col-md">
      <label class="transparent d-block">_</label>
      <button @click.prevent="refreshView" class="btn btn-success mb-1">
        <i class="fa fa-filter mr-2"></i> Appliquer les filtres
      </button>
      <button @click.prevent="resetFilters" class="btn btn-primary ml-2">
        <i class="fa fa-trash mr-2"></i> Retirer les filtres
      </button>
    </div>
  </form>
</template>

<script>
export default {
  data() {
    return {
      form: {
        establishment_id: null,
        season_id: null,
        num_trimester: null
      },
      select: {},
      loadSeason: false,
      mounted: false
    };
  },
  // watch: {
  //   form: {
  //     deep: true,
  //     handler() {
  //       if (this.mounted) {
  //         this.refreshView();
  //       }
  //     },
  //   },
  // },
  beforeMount() {
    this.select.establishments = this.toSelect(
      this.$page.props.establishments_list
    );
    this.form.establishment_id = this.q_.establishment_id;
    this.form.season_id = this.q_.season_id;
    this.getSeason();
    this.mounted = true;
  },
  methods: {
    getSeason(init = false) {
      if (this.form.establishment_id) {
        setTimeout(() => {
          this.loadSeason = true;
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
        }, 100);
      }
    },
    getTrimester() {
      return 0;
      setTimeout(() => {
        if (this.form.season_id) {
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
                    ")"
                };
              });
            });
        } else {
          this.select.trimesters = [];
        }
      }, 100);
    },
    refreshView() {
      setTimeout(() => {
        var params = {
          ...route().params,
          establishment_id: this.form.establishment_id,
          season_id: this.form.season_id
        };

        if (this.form.establishment_id != this.q_.establishment_id) {
          params.season_id = null;
        }

        this.$inertia.visit(route("invoice.unpaid.index", params), {
          preserveScroll: true
        });
      }, 100);
    },
    resetFilters() {
      if (!this.form.establishment_id && !this.form.season_id) {
        return 0;
      }
      this.form.establishment_id = null;
      this.form.season_id = null;
      this.refreshView();
    }
  }
};
</script>


