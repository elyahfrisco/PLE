<template>
  <app-layout>
    <template #pageTitle
      >{{ q_.duplicat_user_id ? "Dupliquer Fiche" : "Ajouter" }}
      {{
        account_type == "prospect" && !q_.duplicat_user_id
          ? "Prospect"
          : "Client"
      }}
      <span v-if="$page.props.create_for_subscription"
        >pour la souscription</span
      >
    </template>
    <form-customer />
  </app-layout>
</template>

<script>
import formCustomer from "../form.vue";
export default {
  props: ["account_type"],
  components: {
    formCustomer
  }
};
</script>


