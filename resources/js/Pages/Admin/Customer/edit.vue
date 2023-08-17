<template>
  <app-layout>
    <template #pageTitle
      >Modifier {{ prospectForm ? "Prospect" : "Client" }} :
      {{ customer.first_name }} {{ customer.name.toUpperCase() }}</template
    >
    <form-customer :customer="customer" />
  </app-layout>
</template>

<script>
import formCustomer from "./form.vue";
export default {
  props: ["prospectForm", "customer"],
  components: {
    formCustomer
  }
};
</script>


