<template>
  <app-layout collapse="true">
    <template v-if="$page.props.renewal_id" #pageTitle
      >Nouvelle souscription pour le renouvellement</template
    >
    <template v-else #pageTitle>Nouvelle souscription</template>
    <form-subscription
      :establishments="establishments"
      :customers="customers"
    />
  </app-layout>
</template>

<script>
import FormSubscription from "./Form/Index.vue";
export default {
  props: ["establishments", "customers"],
  components: {
    FormSubscription
  }
};
</script>
