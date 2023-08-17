<template>
  <app-layout>
    <template #pageTitle
      >Planning général centre: {{ establishment.name }}</template
    >
    <div class="row">
      <inertia-link
        :href="route('establishments.plannings.create', establishment.id)"
        class="mr-2 btn btn-success rounded-0"
        ><i class="fa fa-plus"></i> Nouveau planning</inertia-link
      >
    </div>

    <filter-planning
      :activities="activities"
      :seasons="seasons"
      :establishment_id="establishment.id"
      @refreshView="refreshView"
    ></filter-planning>

    <div class="row">
      <a
        @click.prevent="organizeAll"
        class="ml-auto btn btn-sm mb-2 btn-outline-danger"
        href="#"
        v-if="isLocal()"
        >Organiser tout sur le calendrier</a
      >
      <planning-table
        :plannings="dataDailyActivities"
        :days="days"
        :establishment_id="establishment.id"
        :loadPlannings="loadPlannings"
        :filter="lastFilter"
      />
    </div>
  </app-layout>
</template>

<script>
import filterPlanning from "./filter.vue";
import PlanningTable from "./planningGeneralTable.vue";

export default {
  components: {
    filterPlanning,
    PlanningTable
  },
  props: ["seasons", "daily_activities", "establishment", "activities", "days"],
  data() {
    return {
      dataDailyActivities: Object,
      trimesters: [],
      loadPlannings: false,
      lastFilter: {}
    };
  },
  beforeMount() {
    this.dataDailyActivities = this.daily_activities;
  },
  methods: {
    refreshView(filters) {
      this.lastFilter = filters;
      this.loadPlannings = true;
      axios
        .get(
          route("establishments.plannings.filter", {
            establishment: this.establishment.id
          }),
          { params: filters }
        )
        .then(response => {
          this.dataDailyActivities = response.data;
          this.loadPlannings = false;
        });
    },
    organizeAll() {
      this.$inertia.get(
        route(
          "establishments.plannings.sessions.organize.all",
          this.establishment.id
        )
      );
    }
  }
};
</script>


