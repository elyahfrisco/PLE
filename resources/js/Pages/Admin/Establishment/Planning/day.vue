<template>
  <div class="col-md-2">
    <item-header :day="this.day" />
    <template v-for="planning in plannings" :key="planning.id">
      <item-planning
        :planningData="tran(planning)"
        v-if="planning.activity != null"
      ></item-planning>
    </template>
  </div>
</template>

<script>
import itemPlanning from "./item.vue";
import itemHeader from "./itemHeader.vue";
export default {
  components: {
    itemPlanning,
    itemHeader
  },
  props: ["plannings", "day"],
  beforeMount() {},
  methods: {
    tran(data) {
      return { ...data };
    }
  }
};
</script>


