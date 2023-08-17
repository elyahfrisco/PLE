<template>
  <app-layout>
    <template #pageTitle>Ajouter un Pass</template>
    <form-pass :errors="this.errors"></form-pass>
  </app-layout>
</template>

<script>
import formPass from "./form.vue";

export default {
  props: ["errors"],
  components: {
    formPass
  }
};
</script>


