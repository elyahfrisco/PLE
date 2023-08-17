<template>
  <hr />
  <h2 class="h4">Passes non associées</h2>
  <div>
    <template v-for="pass in this.passesNotAttached" :key="pass.id">
      <button
        class="btn btn-outline-success btn-sm mb-1 mr-2"
        @click="attachPass(pass.id)"
      >
        <i class="fa fa-link"></i> {{ pass.name }}
      </button>
    </template>
  </div>
</template>

<script>
export default {
  props: ["passesNotAttached", "season_id"],
  methods: {
    attachPass(pass_id) {
      var routeParameters = {
        season_id: this.season_id,
        pass_id: pass_id
      };

      this.$inertia.post(
        route("establishments.seasons.passes.attach", routeParameters),
        {
          onBefore: () => confirm("Ajouter Pass au centre?"),
          preserveScroll: true
        }
      );
    }
  }
};
</script>


