<template>
  <app-layout>
    <template #pageTitle>Messages</template>
    <contact-table :TableData="contacts" />
  </app-layout>
</template>

<script>
import ContactTable from "./ContactDatatable.vue";
export default {
  components: {
    ContactTable
  },
  props: ["contacts"]
};
</script>


