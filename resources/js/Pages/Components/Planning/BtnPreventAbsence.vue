<template>
  <a
    type="button"
    class="btn btn-planning prevent-absence"
    data-toggle="tooltip"
    data-placement="top"
    title="Prevenir absence"
    @click.prevent="$emit('prevent_absence')"
  >
    <i class="fa fa-user-alt-slash"></i>
  </a>
</template>

<script>
export default {};
</script>

<style scoped>
.prevent-absence {
  background-color: #ec7523;
}
</style>
