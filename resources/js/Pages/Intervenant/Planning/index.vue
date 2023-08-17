<template>
  <app-layout>
    <h1>Planning de centre {{ establishment.name }}</h1>
    <div class="planning-container">
      <div class="planning-center--filters">
        <Filter
          @refresh-view="refreshView"
          :seasons="seasons"
          :activities="activities"
          v-if="seasons && activities"
        />
      </div>
      <div class="planning-center--content">
        <Planning :establishment="establishment" :filters="filtersValue" />
      </div>
    </div>
  </app-layout>
</template>

<script>
import Planning from "@/Pages/Components/Planning/EstablishmentCalendar.vue";
import Filter from "@/Pages/Components/Planning/Filters/filter.vue";

export default {
  props: ["establishment"],
  components: {
    Planning,
    Filter
  },
  data: () => ({
    seasons: null,
    activities: null,
    filtersValue: null
  }),
  beforeMount() {
    axios
      .get(
        route("api.establishments.seasons", {
          establishment: this.establishment.id
        })
      )
      .then(response => {
        this.seasons = cpp(response.data);
      });

    axios
      .get(
        route("api.establishments.activities", {
          establishment: this.establishment.id
        })
      )
      .then(response => {
        this.activities = cpp(response.data);
      });
  },
  methods: {
    refreshView: function(v) {
      this.filtersValue = v;
    }
  }
};
</script>


