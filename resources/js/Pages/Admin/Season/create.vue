<template>
  <app-layout>
    <template #pageTitle>Ajouter une saison</template>
    <form-season
      :establishment="this.establishment"
      :errors="errors"
    ></form-season>
  </app-layout>
</template>

<script>
import formSeason from "./form.vue";
export default {
  components: {
    formSeason
  },
  props: ["establishment", "errors"]
};
</script>


