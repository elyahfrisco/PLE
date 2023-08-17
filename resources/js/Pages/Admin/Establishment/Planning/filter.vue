<template>
  <form class="row">
    <div class="form-group mr-1">
      <label>Saison</label>
      <Multiselect
        v-model="form.season_id"
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
    <div class="form-group mr-1">
      <label>Date min</label>
      <!-- <input
        type="date"
        class="form-control"
        v-model="form.date_min"
        @change="refreshView"
      /> -->

      <datepicker v-model="form.date_min" class="form-control" />
    </div>
    <div class="form-group mr-1">
      <label>Date max</label>
      <datepicker
        v-model="form.date_max"
        :disabled="false"
        class="form-control"
      />
      <!-- <input
        type="date"
        class="form-control"
        v-model="form.date_max"
        @change="refreshView"
      /> -->
    </div>
  </form>
</template>

<script>
export default {
  props: ["seasons", "activities", "establishment_id"],
  methods: {
    refreshView() {
      setTimeout(() => {
        this.setUrl(
          route("establishments.plannings.create", {
            establishment: this.establishment_id,
            ...this.form
          })
        );

        this.$emit("refreshView", this.form);
      }, 100);
    },
    getTrimester() {
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
                    "T" +
                    a.num_trimester +
                    "(" +
                    this.dateFrMin(a.date_start) +
                    " - " +
                    this.dateFrMin(a.date_end) +
                    ")"
                };
              });
              this.refreshView();
            });
        } else {
          this.select.trimesters = [];
        }
      }, 100);
    }
  },
  data() {
    return {
      form: {
        season_id: null,
        num_trimester: "all",
        activity_id: "all",
        date_min: null,
        date_max: null
      },
      data: {
        seasons: Object,
        trimesters: Object,
        activities: Object
      },
      select: {}
    };
  },
  watch: {
    "form.date_min": {
      handler: function(val, oldVal) {
        this.refreshView();
      },
      deep: true
    },
    "form.date_max": {
      handler: function(val, oldVal) {
        this.refreshView();
      },
      deep: true
    }
  },
  beforeMount() {
    if (this.$page.props.season_id) {
      this.form.season_id = this.$page.props.season_id;
    } else if (this.seasons.length) {
      this.form.season_id = this.seasons[0].id;
    }
    this.data.activities = this.activities;

    this.select.seasons = this.toSelect(this.seasons, "id", "season");

    this.select.activities = this.activities.map(a => {
      return {
        value: a.id,
        label: a.name.toUpperCase()
      };
    });

    this.getTrimester();
  }
};
</script>


