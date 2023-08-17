<template>
  <a
    type="button"
    class="btn btn-planning request-recuperation text-white"
    data-toggle="tooltip"
    data-placement="top"
    :title="title"
    @click.prevent="$emit('request_recuperation')"
  >
    <i class="fa fa-user-clock"></i>
  </a>
</template>

<script>
export default {
  props: {
    title: {
      default: "Récupérer la séance"
    },
    id_absence_prevention: {
      default: null
    }
  }
};
</script>

<style scoped>
.request-recuperation {
  background-color: #dc1c4b;
}
</style>
