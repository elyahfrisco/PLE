<template>
  <app-layout collapse="true">
    <template #pageTitle>Créer une relance</template>
    <form-relaunch />
  </app-layout>
</template>

<script>
import FormRelaunch from "./form.vue";
export default {
  components: {
    FormRelaunch
  }
};
</script>
