<template>
  <app-layout>
    <template #pageTitle>Bugs signalées</template>
    <BugTable :TableData="bugs" />
  </app-layout>
</template>

<script>
import BugTable from "./BugDatatable.vue";
export default {
  components: {
    BugTable
  },
  props: ["bugs"]
};
</script>


