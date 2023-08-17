<template>
  <app-layout>
    <template #pageTitle>Modifier planning</template>
    <form-planning
      :planning="planning"
      :activities="activities"
      :days="days"
      :seasons="seasons"
      :establishment_id="establishment.id"
      :establishment="establishment"
      :edit="true"
    ></form-planning>
  </app-layout>
</template>

<script>
import formPlanning from "./form.vue";

export default {
  components: {
    formPlanning
  },
  props: ["seasons", "activities", "days", "planning", "establishment"]
};
</script>


