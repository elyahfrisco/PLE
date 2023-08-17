<template>
  <jet-modal
    :id="'modal-create-customer-account'"
    maxWidth="xl"
    :fade="false"
    title="Créer un compte"
  >
    <form-customer />
  </jet-modal>
</template>

<script>
import formCustomer from "../../Account/form.vue";
export default {
  components: {
    formCustomer
  }
};
</script>


