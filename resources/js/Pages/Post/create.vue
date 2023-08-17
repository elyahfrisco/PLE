<template>
  <app-layout collapse="true">
    <template #pageTitle>Créer un article</template>
    <form-post />
  </app-layout>
</template>

<script>
import FormPost from "./form.vue";
export default {
  components: {
    FormPost
  }
};
</script>
