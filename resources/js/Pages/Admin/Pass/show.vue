<template>
  <jet-modal id="passShow" title="Détail centre">
    <p class="text-capitalize"><strong>Nom: </strong>{{ pass.name }}</p>
  </jet-modal>
</template>

<script>
import JetModal from "@/Jetstream/Modal";
export default {
  props: ["pass"],
  components: {
    JetModal
  }
};
</script>


