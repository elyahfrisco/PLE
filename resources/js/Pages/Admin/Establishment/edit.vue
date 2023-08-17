<template>
  <app-layout>
    <template #pageTitle>Modifier le centre</template>
    <form-establishment
      :errors="this.errors"
      :establishment="this.establishment"
      :edit="true"
    ></form-establishment>
  </app-layout>
</template>

<script>
import formEstablishment from "./form.vue";

export default {
  props: ["errors", "establishment"],
  components: {
    formEstablishment
  },
  data() {
    return {};
  }
};
</script>


