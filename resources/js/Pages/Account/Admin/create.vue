<template>
  <app-layout>
    <template #pageTitle>Créer un compte administrateur</template>
    <account-form />
  </app-layout>
</template>

<script>
import accountForm from "../form.vue";
export default {
  components: {
    accountForm
  }
};
</script>


