<template>
  <app-layout>
    <template #pageTitle>Modifier activité</template>
    <form-activity :activity="this.activity" :errors="errors"></form-activity>
  </app-layout>
</template>

<script>
import formActivity from "./form";
export default {
  components: {
    formActivity
  },
  props: ["activity", "errors"]
};
</script>


