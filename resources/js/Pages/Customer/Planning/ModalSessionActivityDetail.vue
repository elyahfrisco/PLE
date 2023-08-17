<template>
  <jet-modal
    id="session-activity-detail"
    maxWidth="md"
    title="Detail de l'activité"
  >
    <template v-if="session">
      {{ session }}
    </template>
  </jet-modal>
</template>

<script>
export default {
  props: ["session"]
};
</script>


