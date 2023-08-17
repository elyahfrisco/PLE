<template>
  <app-layout>
    <template #pageTitle>Ajouter une activité</template>
    <form-activity
      :establishments="this.establishments"
      :errors="errors"
    ></form-activity>
  </app-layout>
</template>

<script>
import formActivity from "./form";
export default {
  components: {
    formActivity
  },
  props: ["establishments", "errors"]
};
</script>


