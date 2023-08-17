<template>
  <app-layout>
    <template #pageTitle
      >Modifier : {{ user.first_name }} {{ user.name.toUpperCase() }}</template
    >
    <form-customer :user="user" />
  </app-layout>
</template>

<script>
import formCustomer from "./form.vue";
export default {
  props: ["prospectForm", "user"],
  components: {
    formCustomer
  }
};
</script>


