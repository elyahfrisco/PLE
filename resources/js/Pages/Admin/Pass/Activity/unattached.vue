<template>
  <hr />
  <h2 class="h4">Activités non associées</h2>
  <div>
    <template v-for="activity in this.activitiesNotAttached" :key="activity.id">
      <button
        class="btn btn-outline-success btn-sm mb-1 mr-2"
        @click="attachActicity(activity.id)"
      >
        <i class="fa fa-link"></i> {{ activity.name }}
      </button>
    </template>
  </div>
</template>

<script>
export default {
  props: ["activitiesNotAttached", "pass_id"],
  methods: {
    attachActicity(activity_id) {
      var routeParameters = {
        pass_id: this.pass_id,
        activity_id: activity_id
      };

      this.$inertia.post(
        route("passes.activities.attach", routeParameters),
        {},
        {
          onSuccess: () => {
            this.iReload(this);
          }
        }
      );
    }
  }
};
</script>


