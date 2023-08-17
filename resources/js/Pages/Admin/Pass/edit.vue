<template>
  <app-layout>
    <template #pageTitle>Modifier le Pass</template>
    <form-pass :errors="this.errors" :pass="this.pass" :edit="true"></form-pass>
  </app-layout>
</template>

<script>
import formPass from "./form.vue";

export default {
  props: ["errors", "pass"],
  components: {
    formPass
  }
};
</script>


