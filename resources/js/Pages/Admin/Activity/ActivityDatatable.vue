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
          <th style="width: 100px !important" class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in TableData" :key="item.id">
          <!-- <td>{{ item.id }}</td> -->
          <td class="text-capitalize">{{ item.name }}</td>
          <td>{{ item.care ? "soin" : "" }}</td>
          <td>{{ item.for_kid ? "OUI" : "" }}</td>
          <td>
            <span
              v-if="item.activities_for_recuperation"
              class="text-uppercase"
            >
              {{
                item.activities_for_recuperation.reduce(
                  (acc, activity) => acc + activity.name + ", ",
                  ""
                )
              }}
            </span>
          </td>
          <td class="column-actions">
            <btn-show @click.prevent="showDetail(item)" type="button" />
            <btn-edit :href="route('activities.edit', item.id)" />
            <btn-delete @click.prevent="deleteActivity(item.id)" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <activity-show :activity="activeShow" />
</template>

<script>
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import ActivityShow from "./show.vue";

export default {
  components: {
    BDropdown,
    JetDropdownLink,
    ActivityShow,
  },
  props: ["TableData"],
  data() {
    return {
      labels: [],
      activeShow: Object,
    };
  },
  beforeMount() {
    // this.labels.push({ label: "ID", width: "20" });
    this.labels.push({ label: "Nom" });
    this.labels.push({ label: "Type", width: "50" });
    this.labels.push({ label: "Pour Enfant", width: "50" });
    this.labels.push({ label: "Activité(s) de récupération", width: "200" });
  },
  methods: {
    deleteActivity(id) {
      this.$inertia.delete(route("activities.destroy", id), {
        onBefore: () => confirm("Supprimer activité?"),
        preserveScroll: true,
      });
    },
    showDetail(data) {
      this.activeShow = data;
      $("#activityShow").modal();
    },
  },
};
</script>


