<template>
  <form action="" class="text-black">
    <div class="form-group">
      <label>N° Semestre</label>

      <input type="number" class="form-control" v-model="form.num_trimester" />

      <small class="validation-error" v-if="errors.num_trimester">{{
        errors.num_trimester
      }}</small>
    </div>
    <div class="form-group">
      <label>Jour</label>
      <Multiselect
        v-model="form.day"
        placeholder="--jour--"
        :options="select.days"
        :searchable="true"
        class="text-capitalize"
        required
      />
      <small class="validation-error" v-if="errors.day">{{ errors.day }}</small>
    </div>
    <div class="form-group">
      <label>Date debut</label>
      <datepicker
        :lower-limit="minDate"
        :upper-limit="maxDate"
        :disabledDates="disabledDates"
        :language="fr"
        v-model="form.date_start"
        class="form-control"
      />

      <small class="validation-error" v-if="errors.date_start">{{
        errors.date_start
      }}</small>
    </div>
    <div class="form-group">
      <label>Date fin</label>

      <datepicker
        :lower-limit="minDate"
        :upper-limit="maxDate"
        :disabledDates="disabledDates"
        :language="fr"
        v-model="form.date_end"
        class="form-control"
      />

      <small class="validation-error" v-if="errors.date_end">{{
        errors.date_end
      }}</small>
    </div>

    <!-- <div class="form-group">
      <label>Nombre de semaine</label>
      <input
        type="number"
        class="form-control"
        placeholder=""
        v-model="form.week_count"
      />
      <small class="validation-error" v-if="errors.week_count">{{
        errors.week_count
      }}</small>
    </div> -->

    <button class="btn btn-success" v-if="form.id" @click.prevent="update">
      Metre à jour
    </button>
    <button v-else class="btn btn-success" @click.prevent="store">
      Enregistrer
    </button>
    <br />
  </form>
</template>

<script>
export default {
  props: ["trimesterData", "numTrimester", "seasonId"],
  data() {
    return {
      form: {
        num_trimester: null,
        week_count: null,
        date_start: null,
        date_end: null,
        day: null
      },
      season_id: null,
      select: Object
    };
  },
  beforeMount() {
    if (this.trimesterData) {
      this.form = { ...this.trimesterData };
      this.form.date_start = this.dateAng(this.form.date_start);
      this.form.date_end = this.dateAng(this.form.date_end);
    } else if (this.seasonId) {
      this.season_id = this.seasonId;
    }

    var days = this.$page.props.days;
    this.select.days = days.map(a => {
      return { value: a.en, label: a.fr };
    });
  },
  watch: {
    trimesterData: {
      deep: true,
      handler: function(val, oldform) {
        this.form = cp(this.trimesterData);
        this.form.day = this.dayAng(this.form.date_start);
        this.form.date_start = new Date(this.form.date_start);
        this.form.date_end = new Date(this.form.date_end);
      }
    }
  },
  methods: {
    update() {
      this.$inertia.put(
        route("establishments.seasons.trimesters.update", this.form.id),
        {
          id: this.form.id,
          num_trimester: this.form.num_trimester,
          date_start: this.dateAng(this.form.date_start),
          date_end: this.dateAng(this.form.date_end),
          week_count: this.form.week_count,
          season_id: this.form.season_id
        },
        {
          onSuccess: () => {
            // $("#editTrimester").modal("hide");
            this.iReload(this);
          }
        }
      );
    },
    store() {
      this.$inertia.post(
        route("establishments.seasons.trimesters.store", this.season_id),
        {
          num_trimester: this.form.num_trimester,
          date_start: this.dateAng(this.form.date_start),
          date_end: this.dateAng(this.form.date_end),
          week_count: this.form.week_count,
          season_id: this.season_id
        },
        {
          onSuccess: () => {
            this.iReload(this);
          }
        }
      );
    }
  },
  computed: {
    dayName() {
      if (this.form.day) {
        var days = this.$page.props.days;
        this.dayOfWeek = days.findIndex(x => x.en == this.form.day);
        return this.days[this.dayOfWeek].fr.toUpperCase();
      }
    },
    minDate() {
      return new Date(this.$page.props.season.date_start);
    },
    maxDate() {
      return new Date(this.$page.props.season.date_end);
    },
    dayName() {},
    disabledDates() {
      if (this.form.day) {
        var days = this.$page.props.days;
        var index = days.findIndex(x => x.en == this.form.day);
        var disabledDaysWeek = [1, 2, 3, 4, 5, 6, 0];
        disabledDaysWeek.splice(index, 1);
        return {
          dates: this.getDayDate(this.minDate, this.maxDate, disabledDaysWeek)
        };
      }
    }
  }
};
</script>


