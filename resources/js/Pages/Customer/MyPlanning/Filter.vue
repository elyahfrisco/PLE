<template>
  <div class="filters-container">
    <div class="filter-tag-activity">
      <select v-model="selectedActivity" @change="changeFilter">
        <option
          v-for="(acticity, index) in activities"
          :key="'filter-activity-' + index"
          :value="acticity.id"
        >
          {{ acticity.name }}
        </option>
      </select>
    </div>
    <div class="filter-tag-pass" v-if="user_role != 'coach'">
      <select v-model="selectedPass" @change="changeFilter">
        <option
          v-for="(p, index) in pass"
          :key="'filter-pass-' + index"
          :value="p.id"
        >
          {{ p.name }}
        </option>
      </select>
    </div>
    <div class="filter-upcomming">
      <label>
        <input type="checkbox" v-model="upcomming" class="mr-2" /> Mes séances à
        venir
      </label>
    </div>
  </div>
  <div class="filters-container">
    <div class="filter-date">
      <div class="filter-date--start">
        <input type="date" v-model="minDate" @change="changeFilter" />
      </div>
      <div class="filter-date--separator">
        <span class="mx-2"> à </span>
      </div>
      <div class="filter-date--end">
        <input type="date" v-model="maxDate" @change="changeFilter" />
      </div>
    </div>
    <div>
      <button @click="resetFilter" class="btn btn-primary ml-4">
        <i class="fa fa-trash mr-2"></i> Retirer les filtres
      </button>
    </div>
  </div>
</template>

<script>
export default {
  name: "PlanningFilter",
  data: function() {
    return {
      activities: [],
      pass: [],
      selectedActivity: null,
      selectedPass: null,
      minDate: null,
      maxDate: null,
      upcomming: true,
      // user_role: this.auth_user.role_name
      user_role: null
    };
  },
  methods: {
    getActivities() {
      let that = this;
      axios
        .get(route("api.user.activities"), {
          params: {
            user_id: this.auth_user.id
          }
        })
        .then(response => {
          if (response.data != undefined) {
            that.activities = response.data;

            that.activities = [
              ...that.activities,
              {
                id: null,
                name: "Toutes activités"
              }
            ];
          }
        });
    },
    getPass() {
      let that = this;
      axios
        .get(route("api.user.passes"), {
          params: {
            user_id: this.auth_user.id
          }
        })
        .then(response => {
          if (response.data != undefined) {
            that.pass = response.data;
            that.pass = [
              ...that.pass,
              {
                id: null,
                name: "Tous les passes"
              }
            ];
          }
        });
    },
    changeFilter() {
      this.$emit("filterchange", {
        activity: this.selectedActivity,
        minDate: this.minDate,
        maxDate: this.maxDate,
        pass: this.selectedPass
      });
    },
    resetFilter() {
      this.selectedActivity = this.minDate = this.maxDate = null;
      this.changeFilter();
    }
  },
  mounted: function() {
    this.user_role = this.auth_user.role_name;
    this.getActivities();
    this.getPass();
  },
  watch: {
    upcomming(val) {
      if (val) {
        this.minDate = this.$moment().format("YYYY-MM-DD HH:mm");
        this.maxDate = null;
      } else {
        this.minDate = null;
      }
      this.changeFilter();
    }
  }
};
</script>

<style scoped>
.filters-container {
  display: flex;
  padding-top: 1rem;
  align-items: center;
}
.filter-tag-activity select,
.filter-tag-pass select {
  background: #20c997;
  color: white;
  padding: 5px 0.5rem;
  border-radius: 5px;
  margin-right: 1rem;
  border: none;
  font-weight: 600;
  text-transform: capitalize;
}
.filter-date {
  display: flex;
  align-items: center;
  justify-content: space-between;
}
.filter-date input {
  border: solid 1px rgb(173 171 171 / 54%);
  padding: 2px 0.5rem;
  border-radius: 5px;
}
.filter-upcomming label {
  display: flex;
  align-items: center;
  justify-content: center;
  margin: 0 1rem;
  padding: 4px 12px;
  border: solid 1px #d3d5d7;
  border-radius: 5px;
  cursor: pointer;
}
@media (max-width: 1325px) {
  .filters-container {
    flex-direction: column;
    align-items: start;
  }
  .filters-container > div {
    margin-top: 1rem;
    margin-top: 10px;
  }
}
</style>
