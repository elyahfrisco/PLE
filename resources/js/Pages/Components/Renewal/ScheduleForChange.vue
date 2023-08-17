<template>
  <div class="form-group col-md-6">
    <label>Jour</label>
    <Multiselect
      v-model="form.day"
      placeholder="--jour--"
      class="text-capitalize"
      :options="select.days"
      required
      @change="getPlanning"
    />
  </div>

  <div class="form-group w-100">
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
              required
              @click="attribPlanning(time)"
            />
            <label :for="i_time">
              <span v-if="form.day == null">{{ dayfr(time.day, true) }}</span>
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
</template>

<script>
export default {
  props: ["establishment_id", "activity_id", "num_trimester", "season_id"],
  data() {
    return {
      form: {
        day: null,
        time: null
      },
      select: {},
      times: []
    };
  },
  mounted() {
    this.select.days = this.toSelect(this.days(), "en", "fr");
  },
  methods: {
    getPlanning() {
      setTimeout(() => {
        this.$emit("update:day", this.form.day);
        axios
          .get(route("api.plannings"), {
            params: {
              day: this.form.day,
              establishment_id: this.establishment_id,
              activity_id: this.activity_id,
              num_trimester: this.num_trimester,
              season_id: this.season_id
            }
          })
          .then(response => {
            this.loadingTimes = false;
            this.times = response.data;
          });
      }, 100);
    },
    attribPlanning(planning) {
      this.form.day = planning.day;
      this.form.time_start = planning.time_start;
      this.form.time_end = planning.time_end;
      this.form.planning_id = planning.id;

      this.$emit("update:day", this.form.day);
      this.$emit("update:time_start", this.form.time_start);
      this.$emit("update:time_end", this.form.time_end);
      this.$emit("update:planning_id", this.form.planning_id);
    }
  }
};
</script>


