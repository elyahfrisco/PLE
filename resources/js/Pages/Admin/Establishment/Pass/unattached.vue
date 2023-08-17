<template>
  <hr />
  <h2 class="h4">Passes non associées</h2>
  <div>
    <button
      class="btn btn-outline-success btn-sm mb-1 mr-2"
      v-for="pass in this.passesNotAttached"
      :key="pass.id"
      @click="attachPass(pass.id)"
    >
      <i class="fa fa-link"></i> {{ pass.name }}
    </button>
  </div>
</template>

<script>
export default {
  props: ["passesNotAttached", "establishment_id"],
  methods: {
    attachPass(pass_id) {
      var routeParameters = {
        establishment: this.establishment_id,
        pass_id: pass_id
      };

      this.$inertia.post(
        route("establishments.passes.attach", routeParameters),
        {
          onBefore: () => confirm("Ajouter Pass au centre?"),
          preserveScroll: true
        }
      );
    }
  }
};
</script>


