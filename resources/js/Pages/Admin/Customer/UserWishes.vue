<template>
  <table class="table" v-if="wishes.length">
    <thead>
      <tr>
        <th>Centre</th>
        <th>Activité</th>
        <th>Jour</th>
        <th>Horaire</th>
        <th>Planning</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <template v-for="(wish, i) in wishes" :key="i">
        <tr
          :class="{
            deleted: wish.deleted
          }"
        >
          <td>{{ wish.establishment_id }}</td>
          <td>{{ wish.activity_id }}</td>
          <td>{{ dayfr(wish.day) }}</td>
          <td>{{ wish.time_start }} - {{ wish.time_end }}</td>
          <td>{{ wish.planning_id }}</td>
          <td>
            <button
              @click.prevent="deleteWish(i)"
              class="btn py-0"
              v-if="!wish.deleted"
            >
              <i class="fa fa-trash"></i>
            </button>
            <button @click.prevent="restoreWish(i)" class="btn py-0" v-else>
              <i class="fa fa-undo-alt"></i>
            </button>
          </td>
        </tr>
      </template>
    </tbody>
  </table>
  <p class="mb-1" v-else>Aucune activité souhaité</p>
  <jet-modal id="AddWish" maxWidth="lg" title="Ajouter Activité souhaité">
    <form @submit.prevent="addWish">
      <div class="row">
        <div class="form-group col-md-6">
          <label>Piscine souhaité</label>
          <Multiselect
            v-model="form.establishment_id"
            placeholder="--Piscine souhaitée--"
            :options="select.establishments"
            required
            @change="getEstablishmentPlanningTime"
          />
          <small class="validation-error" v-if="errors.establishment_id">{{
            errors.establishment_id
          }}</small>
        </div>
        <div class="form-group col-md-6">
          <label>Activité</label>
          <Multiselect
            v-model="form.activity_id"
            class="text-upper"
            placeholder="--Activité souhaitée--"
            :options="select.activities"
            required
            @change="getEstablishmentPlanningTime"
          />
          <small class="validation-error" v-if="errors.activity_id">{{
            errors.activity_id
          }}</small>
        </div>
        <div class="form-group col-md-6">
          <label>Jour</label>
          <Multiselect
            v-model="form.day"
            placeholder="--jour--"
            :options="select.days"
            required
            @change="getEstablishmentPlanningTime"
          />
          <small class="validation-error" v-if="errors.activity_id">{{
            errors.activity_id
          }}</small>
        </div>

        <div class="form-group col-md-6">
          <label
            >Date de debut souhaité
            <i class="fa fa-spinner" @click="getEstablishmentPlanningTime"></i
          ></label>
          <datepicker
            v-model="date_start"
            :lower-limit="minDate"
            :upper-limit="maxDate"
            :disabledDates="disabledDates"
            v-on:update:date_start="getEstablishmentPlanningTime()"
            class="form-control"
          />

          <small class="validation-error" v-if="errors.date_start">{{
            errors.date_start
          }}</small>
        </div>
      </div>

      <div class="form-group">
        <label>Horaire(s) disponible</label>
        <div class="row">
          <template v-if="times.length != 0">
            <div
              class="col-md-3 mb-2"
              v-for="(time, i_time) in times"
              :key="time.id"
            >
              <div class="icheck-info d-inline">
                <input
                  type="radio"
                  :value="time.id"
                  :id="i_time"
                  v-model="form.time"
                  @click="attribPlanning(time)"
                />
                <label :for="i_time">
                  <span v-if="form.day == null">{{
                    dayfr(time.day, true)
                  }}</span>
                  {{ time.time_start }} - {{ time.time_end }}</label
                >
              </div>
            </div>
          </template>
          <template v-else>
            <div class="col mb-2">
              <span
                v-if="loadingTimes"
                class="spinner-border spinner-info spinner-border-sm"
                role="status"
                aria-hidden="true"
              ></span>
              Les heures disponible s'affichent ici
            </div>
          </template>
        </div>
      </div>

      <div class="row">
        <div class="form-group col-md-6">
          <label>Heure debut</label>
          <input
            type="time"
            class="form-control"
            required
            v-model="form.time_start"
          />
          <small class="validation-error" v-if="errors.time_start">{{
            errors.time_start
          }}</small>
        </div>
        <div class="form-group col-md-6">
          <label>Heure fin</label>
          <input
            type="time"
            class="form-control"
            required
            v-model="form.time_end"
          />
          <small class="validation-error" v-if="errors.time_end">{{
            errors.time_end
          }}</small>
        </div>
      </div>

      <div class="row">
        <button class="btn btn-success mx-auto">Inserer</button>
      </div>
    </form>
  </jet-modal>
</template>

<script>
export default {
  props: ["wishesData"],
  data() {
    return {
      form: {
        time_start: null,
        time_end: null,
        date_start: null,
        planning_id: null,
        activity_id: null,
        establishment_id: null,
        user_id: null
      },
      date_start: null,
      wishes: [],
      select: {},
      establishments: this.$page.props.establishments,
      activities: this.$page.props.activities,
      days: this.$page.props.days,
      times: [],
      loadingTimes: false,
      loadingDay: false,
      loadingAxctivity: false,
      minDate: new Date(),
      maxDate: new Date(
        this.$moment()
          .add("1", "y")
          .format("YYYY-MM-DD")
      )
    };
  },
  beforeMount() {
    this.select.establishments = this.toSelect(this.establishments);
    this.select.days = this.toSelect(this.days, "en", "fr");
    this.select.activities = this.toSelect(this.activities);
    if (this.wishesData) {
      this.wishes = this.wishesData;
    }
  },
  methods: {
    deleteWish(i) {
      this.wishes[i].deleted = true;
      this.$emit("update:wishes", this.wishes);
    },
    restoreWish(i) {
      this.wishes[i].deleted = false;
      this.$emit("update:wishes", this.wishes);
    },
    attribPlanning(planning) {
      this.form.day = planning.day;
      this.form.time_start = planning.time_start;
      this.form.time_end = planning.time_end;
      this.form.planning_id = planning.id;
    },
    addWish() {
      this.wishes.push(cp(this.form));
      for (var key in this.form) {
        this.form[key] = null;
      }
      this.$emit("update:wishes", this.wishes);
      $("#AddWish").modal("hide");
    },
    getEstablishmentPlanningTime() {
      setTimeout(() => {
        if (this.form.establishment_id && this.form.activity_id) {
          this.loadingTimes = true;
          axios
            .get(route("api.plannings"), {
              params: {
                day: this.form.day,
                establishment_id: this.form.establishment_id,
                activity_id: this.form.activity_id,
                date_start: this.date_start
                  ? this.dateAng(this.date_start)
                  : null,
                startOrEndGreatNow: true
              }
            })
            .then(response => {
              this.loadingTimes = false;
              this.times = response.data;
            });
        } else {
          this.times = [];
        }
      }, 100);
    }
  },
  computed: {
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
  },
  watch: {
    date_start: {
      deep: true,
      function(val, oldVal) {
        this.getEstablishmentPlanningTime;
      }
    }
  }
};
</script>

<style scoped>
.table th,
.table td {
  padding: 2px;
}
.deleted {
  background-color: #ffbab8;
}
</style>
