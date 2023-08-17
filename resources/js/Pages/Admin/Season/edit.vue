<template>
  <app-layout>
    <template #pageTitle>Modifier la saison</template>
    <form-season :season="this.season" :errors="errors"></form-season>
  </app-layout>
</template>

<script>
import formSeason from "./form.vue";
export default {
  components: {
    formSeason
  },
  props: ["season", "errors"]
};
</script>


