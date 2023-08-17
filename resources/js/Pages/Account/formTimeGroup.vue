<template>
  <div class="px-10">
    <div class="form-group">
      <label>Jour</label>

      <Multiselect
        v-model="form.day"
        placeholder="--jour--"
        :options="select.days"
        @change="getEstablishmentPlanningTime"
        class="text-capitalize"
        required
      />

      <small class="validation-error" v-if="errors.day">{{ errors.day }}</small>
    </div>
    <div class="form-group" v-if="times.length != 0">
      <label>Horaire</label>
      <div class="row">
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
              @click="$emit('update:planningId', $event.target.value)"
            />
            <label :for="i_time"
              >{{ time.time_start }} - {{ time.time_end }}</label
            >
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["days", "establishment_id"],
  data() {
    return {
      form: {
        day: "",
        time: ""
      },
      times: [],
      select: Object
    };
  },
  beforeMount() {
    this.select.days = this.days.map(a => {
      return { value: a.en, label: a.fr };
    });
  },
  watch: {
    establishment_id: function(newVal, oldVal) {
      this.getEstablishmentPlanningTime();
    }
  },
  methods: {
    getEstablishmentPlanningTime() {
      setTimeout(() => {
        if (this.form.day != "" && this.establishment_id != null) {
          axios
            .get(route("plannings.times"), {
              params: {
                day: this.form.day,
                establishment_id: this.establishment_id
              }
            })
            .then(response => {
              this.times = response.data.times;
            });
          this.$emit("update:day", this.form.day);
        }
      }, 100);
    }
  }
};
</script>


