<template>
  <app-layout>
    <template #pageTitle>Modifier la question</template>
    <form-question :question="this.question" :edit="true"></form-question>
  </app-layout>
</template>

<script>
import formQuestion from "./formQuestion.vue";

export default {
  props: ["question"],
  components: {
    formQuestion
  },
  data() {
    return {};
  }
};
</script>


