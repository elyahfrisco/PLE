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
          <td class="text-uppercase">
            {{ wish.establishment ? wish.establishment.name : "-" }}
          </td>
          <td class="text-uppercase">{{ wish.activity.name }}</td>
          <td>{{ wish.day ? dayfr(wish.day) : "-" }}</td>
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
    <form>
      <div class="row">
        <div class="form-group col-md-6">
          <label>Piscine souhaité</label>
          <Multiselect
            v-model="form.establishment_id"
            placeholder="--Piscine souhaitée--"
            :options="select.establishments"
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
            @change="getEstablishmentPlanningTime"
          />
          <small class="validation-error" v-if="errors.activity_id">{{
            errors.activity_id
          }}</small>
        </div>

        <div class="form-group col-md-6">
          <label
            >Date de debut souhaité
            <i
              class="fa fa-spinner pointer"
              @click="getEstablishmentPlanningTime"
            ></i
          ></label>
          <datepicker
            v-model="date_start"
            :lower-limit="minDate"
            :upper-limit="maxDate"
            :disabledDates="disabledDates"
            :disabled="!form.establishment_id || !form.day"
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
                  name="time_date"
                  type="checkbox"
                  v-model="form.times_vinany"
                  :value="time"
                  :id="i_time"
                />
                <label :for="i_time">
                  <span v-if="form.day == null">{{
                    dayfr(time.day, true)
                  }}</span>
                  {{ time.time_start }} - {{ time.time_end }}</label
                >
                <input
                  type="hidden"
                  class="form-control"
                  v-model="form.time_starts[i_time]"
                />
                <input
                  type="hidden"
                  class="form-control"
                  v-model="form.time_ends[i_time]"
                />
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
        <!--<div class="form-group col-md-6">
          <label>Heure debut</label>
          <input
            type="time"
            class="form-control"

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

            v-model="form.time_end"
          />
          <small class="validation-error" v-if="errors.time_end">{{
            errors.time_end
          }}</small>
        </div>-->
      </div>

      <div class="row">
        <button
          v-on:click="addWish()"
          class="btn btn-success mx-auto"
          type="button"
        >
          Inserer
        </button>
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
        times_vinany: [],
        time_starts: [],
        time_ends: [],
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
    attribPlanning(planning, key) {
      /*if (!this.form.day) {
        this.form.date_start = null;
        this.getEstablishmentPlanningTime;
      }*/
      this.form.days[key] = planning.day;
      this.form.time_starts[key] = planning.time_start;
      this.form.time_ends[key] = planning.time_end;
      this.form.planning_ids[key] = planning.id;
    },
    addWish() {
      var activity = this.iValue(this.select.activities, this.form.activity_id);
      var establishment = this.iValue(
        this.select.establishments,
        this.form.establishment_id
      );
      if (this.form.times_vinany.length > 0) {
        this.form.times_vinany.forEach((value, index) => {
          this.wishes.push({
            time_start: value.time_start,
            time_end: value.time_end,
            activity_id: this.form.activity_id,
            activity: activity,
            establishment_id: this.form.establishment_id,
            establishment: establishment,
            date_start: this.form.date_start,
            day: value.day,
            planning_id: value.id,
            time: this.iValue(this.times, this.form.planning_id)
          });
        });
      } else {
        this.wishes.push({
          time_start: "",
          time_end: "",
          activity_id: this.form.activity_id,
          activity: this.iValue(this.select.activities, this.form.activity_id),
          establishment_id: this.form.establishment_id,
          establishment: this.iValue(
            this.select.establishments,
            this.form.establishment_id
          ),
          date_start: this.form.date_start,
          time: this.iValue(this.times, this.form.planning_id)
        });
      }
      // delete data.time_starts;
      // delete data.time_ends;

      for (var key in this.form) {
        this.form[key] = null;
      }
      this.form["time_starts"] = [];
      this.form["time_ends"] = [];
      this.times = [];
      console.log(this.wishes);
      this.$emit("update:wishes", this.wishes);
      $("#AddWish").modal("hide");
    },
    getEstablishmentPlanningTime() {
      this.form.times_vinany = [];
      $('input[name="time_date"]').each(function() {
        this.checked = false;
      });
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
        setTimeout(() => {
          this.getEstablishmentPlanningTime;
        }, 200);
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
