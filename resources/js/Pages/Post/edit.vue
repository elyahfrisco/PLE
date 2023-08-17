<template>
  <app-layout collapse="true">
    <template #pageTitle>Modifier l'article</template>
    <form-post :post="post" />
  </app-layout>
</template>

<script>
import FormPost from "./form.vue";
export default {
  props: ["post"],
  components: {
    FormPost
  }
};
</script>
