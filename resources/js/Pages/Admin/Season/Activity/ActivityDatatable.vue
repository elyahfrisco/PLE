<template>
  <div class="table-responsive">
    <table id="table_id" class="display table datatable">
      <thead>
        <tr>
          <th
            v-for="label in labels"
            :key="label"
            :style="'width:' + (label.width ? label.width + 'px' : '')"
          >
            {{ label.label }}
          </th>
          <th style="width: 127px" class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in TableData" :key="item.id" :id="'activity' + item.id">
          <td class="text-capitalize">{{ item.name }}</td>
          <td>
            <inertia-link
              :href="
                route('establishments.seasons.activities.prices.index', {
                  establishment: season.establishment_id,
                  season_id: season.id,
                  activity_id: item.id
                })
              "
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              ><i class="fa fa-dollar-sign"></i> Voir/Modifier les
              prix</inertia-link
            >
          </td>
          <td class="column-actions">
            <btn-edit :href="route('activities.edit', item.id)" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";

export default {
  components: {
    BDropdown,
    JetDropdownLink
  },
  props: ["TableData", "season"],
  data() {
    return {
      labels: []
    };
  },
  beforeMount() {
    this.labels.push({ label: "Nom" });
    this.labels.push({ label: "Paramètres", width: "180" });
  },
  methods: {}
};
</script>


