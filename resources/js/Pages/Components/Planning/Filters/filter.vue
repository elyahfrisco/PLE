<template>
  <form
    v-if="auth_user.role_name != 'prospect' && auth_user.role_name != 'customer'"
   class="row">
    <div class="form-group mr-1">
      <label>Saison</label>
      <Multiselect
        v-model="form.season_id"
        id="select_season"
        placeholder="--seasons--"
        :options="select.seasons"
        :searchable="true"
        :required="true"
        :canDeselect="false"
        @change="getTrimester"
      />
    </div>
    <div class="form-group mr-1">
      <label>Trimestre</label>
      <Multiselect
        v-model="form.num_trimester"
        placeholder="--Trimestres--"
        :options="select.trimesters"
        :searchable="true"
        @change="refreshView"
      />
    </div>
    <div class="form-group mr-1">
      <label>Activité</label>
      <Multiselect
        v-model="form.activity_id"
        placeholder="--Activités--"
        :options="select.activities"
        :searchable="true"
        @change="refreshView"
      />
    </div>
    <div
      class="form-group mr-1 col-lg-2 col-md-4 col-6"
    >
      <label>Nombre des participant</label>
      <div class="">
        <div class="row">
          <div class="col-sm-6 pb-1">
            <input
              type="number"
              class="form-control text-center"
              placeholder="min..."
              v-model="form.participant_min"
              @keyup="refreshView"
              @change="refreshView"
            />
          </div>
          <div class="col-sm-6">
            <input
              type="number"
              class="form-control text-center"
              placeholder="max..."
              v-model="form.participant_max"
              @keyup="refreshView"
              @change="refreshView"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="form-group col-lg-2 col-md col-md-6 col">
      <label>Groupe</label>
      <Multiselect
        v-model="form.planning_id"
        :options="
          async function(query) {
            return await getPlannings(query);
          }
        "
        @deselect="refreshView"
        @select="refreshView"
        @clear="refreshView"
        placeholder="--Saisir le nom du groupe--"
        :filterResults="false"
        :resolveOnLoad="false"
        :minChars="2"
        :searchable="true"
        :loading="loadPlanning"
        :clearOnSelect="false"
        delay="500"
        trackBy="group_name"
        label="group_name"
        valueProp="id"
        ref="selectPlanning"
      />
    </div>
    <div class="form-group col-md">
      <label class="transparent d-block">_</label>
      <button @click.prevent="resetFilters" class="btn btn-primary ml-2">
        <i class="fa fa-trash"></i>
      </button>
    </div>
  </form>
</template>

<script>
export default {
  props: ["establishment", "seasons", "activities","showFilter"],
  methods: {
    refreshView(e) {
      if (
        typeof e == "object" &&
        e.key &&
        isNaN(e.key) &&
        "ArrowDown" != e.key &&
        "Backspace" != e.key &&
        "ArrowUp" != e.key
      ) {
        return 0;
      }
      setTimeout(() => {
        if (
          this.select.trimesters &&
          this.lastFilter.num_trimester != this.form.num_trimester
        ) {
          const trimester = this.iValue(
            this.select.trimesters,
            this.form.num_trimester,
            "num_trimester"
          );
          if (trimester) {
            this.form.miDate = new Date(trimester.date_start);
            this.form.maDate = new Date(trimester.date_end);
          }
        }
        this.lastFilter = this.form;
        this.$emit("refreshView", this.form);
      }, 100);
    },
    getTrimester() {
      setTimeout(() => {
        this.refreshView();
        
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
                    ")",
                  ...a
                };
              });
            });
        } else {
          this.select.trimesters = [];
        }
      }, 100);
    },
    getPlannings(query = "") {
      if ((!this.mounted && this.$page.props.planning_id) || query.length) {
        this.loadPlanning = true;
        let params = !this.mounted
          ? {
              planning_id: this.$page.props.planning_id
            }
          : {
              q: query
            };
        params.establishment_id = this.establishment.id;
        return axios.get(route("api.plannings.list", params)).then(response => {
          this.select.plannings = response.data;
          this.loadPlanning = false;
          if (!this.mounted) {
            this.form.planning_id = +this.$page.props.planning_id;
            this.mounted = true;
          }
          return this.select.plannings;
        });
      }
      return [];
    },
    resetFilters() {
      this.form.season_id = null;
      this.form.num_trimester = "all";
      this.form.activity_id = "all";
      this.form.participant_min = null;
      this.form.participant_max = null;
    },
    setActiveSeason() {
      let in_progress_season = this.seasons.find(
        season => season.status === "in_progress"
      );
      if (in_progress_season) {
        this.form.season_id = in_progress_season.id;
      } else {
        let upcoming_season = this.seasons.find(
          season => season.status === "upcoming"
        );
        if (upcoming_season) {
          this.form.season_id = upcoming_season.id;
        } else {
          this.form.season_id = this.seasons[0].id;
        }
      }
    }
  },
  data() {
    return {
      form: {
        season_id: null,
        num_trimester: "all",
        selectedDate: null,
        activity_id: "all",
        date_min: null,
        date_max: null,
        participant_min: null,
        participant_max: null
      },
      data: {
        seasons: [],
        trimesters: [],
        activities: []
      },
      select: {},
      mounted: false,
      lastFilter: {},
      loadPlanning: false
    };
  },
  watch: {
    "form.date_min": {
      handler: function() {
        this.refreshView();
      },
      deep: true
    },
    "form.date_max": {
      handler: function() {
        this.refreshView();
      },
      deep: true
    }
  },
  mounted() {
    this.setActiveSeason();
    this.data.activities = this.activities;

    this.select.seasons = this.toSelect(this.seasons, "id", "season");

    this.select.activities = this.activities.map(a => {
      return {
        value: a.id,
        label: a.name.toUpperCase()
      };
    });

    this.getTrimester();
    this.mounted = true;
    if (
      this.$page.props.activity_id ||
      this.$page.props.num_trimester ||
      this.$page.props.season_id
    ) {
      if (this.$page.props.activity_id) {
        this.form.activity_id = this.$page.props.activity_id;
      }
      if (this.$page.props.num_trimester) {
        this.form.num_trimester = this.$page.props.num_trimester;
      }
      if (this.$page.props.season_id) {
        this.form.season_id = this.$page.props.season_id;
      }
      this.refreshView();
    }

    this.$emit("initFilter", this.form);
  }
};
</script>

<style scoped>
input {
  min-height: 41px;
}
</style>
