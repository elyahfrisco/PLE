<template>
  <app-layout>
    <template #pageTitle>Ajouter une question</template>
    <form-question></form-question>
  </app-layout>
</template>

<script>
import formQuestion from "./formQuestion.vue";
export default {
  components: {
    formQuestion
  }
};
</script>


