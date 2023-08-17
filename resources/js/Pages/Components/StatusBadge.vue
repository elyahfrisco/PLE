<template>
  <span class="badge badge-success" v-if="status == 'in_progress'">
    En cours
  </span>
  <span class="badge badge-secondary" v-else-if="status == 'futur'">
    Future
  </span>
  <span class="badge badge-secondary" v-else-if="status == 'finished'">
    Terminé
  </span>
</template>

<script>
export default {
  props: ["status"]
};
</script>


