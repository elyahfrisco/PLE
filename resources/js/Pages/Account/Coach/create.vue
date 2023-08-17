<template>
  <app-layout>
    <template #pageTitle>Créer un compte enseignant</template>
    <account-form />
  </app-layout>
</template>

<script>
import accountForm from "../form.vue";
export default {
  props: ["prospectForm"],
  components: {
    accountForm
  }
};
</script>


