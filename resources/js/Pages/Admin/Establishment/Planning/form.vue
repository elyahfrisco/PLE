<template>
  <form class="p-5">
    <div class="row">
      <div class="form-group col-md-6">
        <label>Saison</label>
        <Multiselect
          v-model="form.season_id"
          placeholder="--saison--"
          :options="select.seasons"
          @change="getActivitiesSeason"
          :canDeselect="false"
          :disabled="activitySessions_participants_count > 0"
          required
        />
        <small class="validation-error" v-if="errors.season_id">{{
          errors.season_id
        }}</small>
      </div>

      <!-- <div class="form-group col-md-6">
        <label>Trimestre</label>
        <Multiselect
          v-model="form.trimester_num"
          placeholder="--trimestre--"
          :options="select.trimesters"
          required
        />

        <small class="validation-error" v-if="errors.trimester_num">{{
          errors.trimester_num
        }}</small>
      </div> -->
      <div class="form-group col-md-6">
        <label>Jour</label>
        <Multiselect
          v-model="form.day"
          placeholder="--jour--"
          :options="select.days"
          :searchable="true"
          class="text-capitalize"
          :canDeselect="false"
          :disabled="activitySessions_participants_count > 0"
          required
        />
        <small class="validation-error" v-if="errors.day">{{
          errors.day
        }}</small>
      </div>

      <div class="form-group col-md-6">
        <label>Activité</label>

        <Multiselect
          v-model="form.activity_id"
          placeholder="--activité--"
          :options="select.activities"
          :searchable="true"
          class="text-uppercase"
          :loading="loadActivities"
          :canDeselect="false"
          :disabled="activitySessions_participants_count > 0"
          required
        />

        <small class="validation-error" v-if="errors.activity_id">{{
          errors.activity_id
        }}</small>
      </div>

      <div class="form-group col-md-3 col-sm-6">
        <label>Effectif max des participants</label>
        <input
          type="number"
          class="form-control"
          placeholder=""
          v-model="form.max_effective"
        />
        <small class="validation-error" v-if="errors.max_effective">{{
          errors.max_effective
        }}</small>
      </div>
      <div class="form-group col-md-3 col-sm-6">
        <label>Super pass</label>
        <input
          type="number"
          class="form-control"
          placeholder=""
          v-model="form.super_pass"
        />
        <small class="validation-error" v-if="errors.super_pass">{{
          errors.super_pass
        }}</small>
      </div>
    </div>
    <div class="form-group">
      <label>Date</label>
      <div class="row">
        <div class="col-md-6 form-group">
          <label>Date de debut</label>

          <datepicker
            :lower-limit="minDate"
            :upper-limit="maxDate"
            :disabledDates="disabledDates"
            :language="fr"
            v-model="form.start_at"
            class="form-control"
            :disabled="!form.day"
          />
          <span v-if="form.season_id"> min {{ dateFr(minDate) }} </span>
          <small class="validation-error d-block" v-if="errors.start_at">{{
            errors.start_at
          }}</small>
        </div>
        <div class="col-md-6 form-group">
          <label>Date de fin</label>
          <datepicker
            :lower-limit="minDate"
            :upper-limit="maxDate"
            :disabled-dates="disabledDates"
            v-model="form.end_at"
            class="form-control"
            :disabled="!form.day"
          />
          <span v-if="form.season_id"> max {{ dateFr(maxDate) }} </span>
          <small class="validation-error d-block" v-if="errors.end_at">{{
            errors.end_at
          }}</small>
        </div>
      </div>
    </div>
    <div class="form-group mb-0">
      <label>Horaire</label>
      <div class="row">
        <div class="col-md-6 form-group">
          <label>Heure de debut</label>
          <input
            type="time"
            class="form-control"
            placeholder=""
            v-model="form.time_start"
          />
          <small class="validation-error" v-if="errors.time_start">{{
            errors.time_start
          }}</small>
        </div>
        <div class="col-md-6 form-group">
          <label>Heure de fin</label>
          <input
            type="time"
            class="form-control"
            placeholder=""
            v-model="form.time_end"
          />
          <small class="validation-error" v-if="errors.time_end">{{
            errors.time_end
          }}</small>
        </div>
      </div>
    </div>

    <div class="form-group">
      <div class="icheck-primary icheck-sm d-inline my-3">
        <input
          type="checkbox"
          v-model="form.hide_to_customer"
          id="hide_to_customer"
        />
        <label for="hide_to_customer">Masquer au client</label>
      </div>
    </div>

    <div class="form-group">
      <div class="icheck-primary icheck-sm d-inline">
        <input
          type="checkbox"
          v-model="form.calculCalendar"
          id="calculCalendar"
        />
        <label for="calculCalendar" v-if="form.id"
          >Régénérer le calendrier des semaines</label
        >
        <label for="calculCalendar" v-else
          >Générer le calendrier des semaines</label
        >
      </div>
    </div>

    <div class="">
      Vous plannifiez seance de {{ activityName }}, chaque {{ dayName }} de
      {{ form.time_start }} à {{ form.time_end }}, dans le centre
      {{ this.establishment.name.toUpperCase() }} du
      {{ form.start_at ? dateFr(form.start_at) : "_" }} au
      {{ form.end_at ? dateFr(form.end_at) : "_" }}. Le nombre maximum de
      participants est de {{ this.form.max_effective }}
    </div>

    <button
      type="submit"
      @click.prevent="update"
      v-if="editMode"
      class="btn btn-success"
    >
      Enregistrer la Modification
    </button>
    <button
      type="submit"
      @click.prevent="submit"
      v-else
      class="btn btn-success"
    >
      Enregistrer
    </button>
  </form>
</template>

<script>
export default {
  components: {},
  props: [
    "planning",
    "activities",
    "seasons",
    "days",
    "establishment_id",
    "establishment",
    "edit",
    "activitySessions_participants_count"
  ],
  data() {
    return {
      form: {
        day: null,
        time_start: null,
        time_end: null,
        start_at: null,
        end_at: null,
        max_effective: null,
        super_pass: 0,
        number_activity_sessions: 0,
        trimester_num: 0,
        season_id: null,
        activity_id: null,
        calculCalendar: null,
        hide_to_customer: false
      },
      planningTrimester: true,
      editMode: false,
      select: Object,
      min: null,
      max: null,
      loadActivities: false
    };
  },
  watch: {
    "form.start_at": function() {
      setTimeout(() => {
        this.form.calculCalendar =
          this.planning &&
          this.dateAng(this.planning.start_at) !=
            this.dateAng(this.form.start_at);
      }, 200);
    },
    "form.end_at": function() {
      setTimeout(() => {
        this.form.calculCalendar =
          this.planning &&
          this.dateAng(this.planning.end_at) != this.dateAng(this.form.end_at);
      }, 200);
    }
  },
  beforeMount() {
    this.select.seasons = this.toSelect(this.seasons, "id", "season");

    this.select.trimesters = [
      { value: 1, label: "Trimestre 1" },
      { value: 2, label: "Trimestre 2" },
      { value: 3, label: "Trimestre 3" }
    ];
    this.select.days = this.days.map(a => {
      return { value: a.en, label: a.fr };
    });
    this.select.activities = this.activities.map(a => {
      return { value: a.id, label: a.name };
    });
    if (this.edit == true) {
      this.editMode = true;
      this.form = { ...this.planning };
      this.form.start_at = new Date(this.form.start_at);
      this.form.end_at = new Date(this.form.end_at);
      this.form.calculCalendar = this.form.organized ? false : true;
      this.form.hide_to_customer = this.form.hide_to_customer ? true : false;
      // delete this.form.activity;
    }
  },
  mounted() {
    if (this.edit == true) {
      this.getActivitiesSeason(true);
    }

    if (!this.form.id) {
      this.form.season_id = this.$page.props.req.season_id;
      this.form.activity_id = this.$page.props.req.activity_id;
    }
  },
  methods: {
    getActivitiesSeason(init = false) {
      setTimeout(() => {
        if (!init) {
          this.form.activity_id = null;
        }
        if (this.form.season_id) {
          this.loadActivities = true;
          axios
            .get(route("api.seasons.activities", this.form.season_id))
            .then(response => {
              this.select.activities = response.data.map(a => {
                return {
                  value: a.id,
                  id: a.id,
                  label: a.name,
                  name: a.name
                };
              });
              this.loadActivities = false;
            });
        } else {
          this.select.activities = {};
        }
      }, 100);
    },
    submit() {
      var dataPlanning = cp(this.form);

      dataPlanning.start_at = this.dateAng(dataPlanning.start_at);
      dataPlanning.end_at = this.dateAng(dataPlanning.end_at);

      this.$inertia.post(
        route("establishments.plannings.store", this.establishment_id),
        dataPlanning,
        {
          onSuccess: () => {
            this.form = {
              day: null,
              dayOfWeek: null,
              time_start: null,
              time_end: null,
              start_at: null,
              end_at: null,
              max_effective: null,
              super_pass: 0,
              number_activity_sessions: 0,
              trimester_num: 0,
              // season_id: null,
              activity_id: null
            };
          }
        }
      );
    },
    update() {
      var data = cp(this.form);
      data.start_at = this.dateAng(data.start_at);
      data.end_at = this.dateAng(data.end_at);
      this.$inertia.put(
        route("establishments.plannings.update", {
          establishment: this.establishment_id,
          planning: data.id
        }),
        data
      );
    }
  },
  computed: {
    activityName() {
      if (this.form.activity_id) {
        var r = this.select.activities[
          this.select.activities.findIndex(x => x.id == this.form.activity_id)
        ];
        return r ? r.name.toUpperCase() : "";
      }
    },
    dayName() {
      if (this.form.day) {
        this.dayOfWeek = this.days.findIndex(x => x.en == this.form.day);
        return this.days[this.dayOfWeek].fr.toUpperCase();
      }
    },
    minDate() {
      if (this.form.season_id) {
        this.min = this.seasons[
          this.seasons.findIndex(x => x.id == this.form.season_id)
        ].date_start;
        return new Date(this.min);
      }
    },
    maxDate() {
      if (this.form.season_id) {
        this.max = this.seasons[
          this.seasons.findIndex(x => x.id == this.form.season_id)
        ].date_end;
        return new Date(this.max);
      }
    },
    disabledDates() {
      if (this.form.day) {
        var index = this.days.findIndex(x => x.en == this.form.day);
        var disabledDaysWeek = [1, 2, 3, 4, 5, 6, 0];
        disabledDaysWeek.splice(index, 1);
        return {
          dates: this.getDayDate(this.minDate, this.maxDate, disabledDaysWeek)
        };
      }
      return {};
    }
  }
};
</script>

