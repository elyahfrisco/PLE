<template>
  <button
    type="button"
    class="btn btn-planning cancel-prevented-absence border-white"
    data-toggle="tooltip"
    data-placement="top"
    title="Annuler l'absence prévenu"
    @click.prevent="cancelPresence()"
  >
    <i class="fa fa-user-alt-slash"></i>
  </button>
</template>

<script>
export default {
  props: ["absence_prevention_id"],
  methods: {
    cancelPresence() {
      confirm("Confirmer l'annulation de l'absence prévenu ?")
        ? axios
            .delete(
              route("absences.destroy", {
                absence: this.absence_prevention_id
              })
            )
            .then(response => {
              toastr.success(response.data);
              this.$emit("absence-canceled");
            })
        : null;
    }
  }
};
</script>

<style scoped>
.cancel-prevented-absence {
  background-color: #f54730;
}
</style>
