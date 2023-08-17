<template>
  <table class="table table-planning-general">
    <thead>
      <tr class="text-uppercase text-center">
        <th v-for="(day, i) in days" :key="i">
          <loadings class="text-info" :processing="loadPlannings" />
          <span class="px-1" :class="{ 'text-muted': loadPlannings }">{{
            day.fr
          }}</span>
        </th>
      </tr>
    </thead>
    <tbody>
      <tr v-if="plannings">
        <td v-for="(planning_day, i_p) in plannings" :key="i_p">
          <template v-for="(planning, i_s) in planning_day" :key="i_p + i_s">
            <item-planning :planning="planning" v-if="planning.activity" />
          </template>
        </td>
      </tr>
      <tr v-if="listEmpty">
        <td colspan="7" class="text-center">
          <p class="mb-1">Aucune planning à afficher</p>
          <inertia-link
            :href="
              route('establishments.plannings.create', {
                establishment: establishment_id,
                season_id: filter.season_id
              })
            "
            class="mr-2 btn btn-success rounded-0 btn-sm"
            ><i class="fa fa-plus"></i> Nouveau planning</inertia-link
          >
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import itemPlanning from "./item.vue";
export default {
  components: {
    itemPlanning
  },
  props: ["plannings", "days", "establishment_id", "loadPlannings", "filter"],
  data() {
    return {
      indexTable: 0
    };
  },
  computed: {
    listEmpty() {
      var length_ = 0;
      const plannings_ = Object.entries(_.cloneDeep(this.plannings));
      plannings_.map(a => {
        length_ += a[1].length;
      });
      return length_ == 0;
    }
  }
};
</script>

<style scoped>
th {
  padding: 0.5rem 0.25rem;
}
td {
  padding: 0.25rem;
}
</style>
