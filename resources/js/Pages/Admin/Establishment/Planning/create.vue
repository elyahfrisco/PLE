<template>
  <app-layout>
    <template #pageTitle
      >Ajout planning pour centre {{ establishment.name }}</template
    >
    <form-planning
      :activities="activities"
      :seasons="seasons"
      :days="days"
      :establishment="establishment"
      :establishment_id="establishment.id"
    ></form-planning>
  </app-layout>
</template>

<script>
import formPlanning from "./form.vue";

export default {
  components: {
    formPlanning
  },
  props: ["activities", "days", "seasons", "establishment"]
};
</script>


