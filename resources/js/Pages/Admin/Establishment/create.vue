<template>
  <app-layout>
    <template #pageTitle>Ajouter un centre</template>
    <form-establishment :errors="this.errors"></form-establishment>
  </app-layout>
</template>

<script>
import formEstablishment from "./form.vue";
export default {
  props: ["errors"],
  components: {
    formEstablishment
  }
};
</script>


