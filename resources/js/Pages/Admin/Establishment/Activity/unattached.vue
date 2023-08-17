<template>
  <div class="pt-10">
    <hr />
    <h2 class="text-2xl">Activités non associées</h2>
    <button
      class="btn btn-outline-success btn-sm mb-1 mr-2"
      v-for="activity in this.activitiesNotAttached"
      :key="activity.id"
      @click="attachActicity(activity.id)"
    >
      <i class="fa fa-link"></i> {{ activity.name }}
    </button>
  </div>
</template>

<script>
export default {
  props: ["activitiesNotAttached", "establishment_id"],
  methods: {
    attachActicity(activity_id) {
      var routeParameters = {
        establishment: this.establishment_id,
        activity_id: activity_id
      };

      this.$inertia.post(
        route("establishments.activities.attach", routeParameters),
        {
          onBefore: () => confirm("Ajouter l'activité au centre?"),
          preserveScroll: true
        }
      );
    }
  }
};
</script>


