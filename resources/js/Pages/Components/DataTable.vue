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
        <tr v-for="item in TableData" :key="item.id">
          <td>{{ item.id }}</td>
          <td class="text-capitalize">{{ item.name }}</td>
          <td>
            <b-dropdown btn="outline-secondary float-right py-0" sm="true">
              <template #title> Action </template>
              <template #content>
                <a href="#" class="dropdown-item" @click="showDetail(item)">
                  Détail
                </a>
                <jet-dropdown-link :href="route('activities.edit', item.id)">
                  Modifier
                </jet-dropdown-link>
                <a
                  href="#"
                  class="dropdown-item"
                  @click.prevent="deleteActivity(item.id)"
                >
                  Supprimer
                </a>
              </template>
            </b-dropdown>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <activity-show :activity="activeActivity" />
</template>

<script>
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";

export default {
  components: {
    BDropdown,
    JetDropdownLink,
    ActivityShow
  },
  props: {
    TableData: {
      type: Array,
      require: true
    },
    id: {
      type: String,
      require: true
    },
    labels: {
      type: Array,
      require: true
    }
  },
  data() {
    return {
      rows: [],
      page: 1,
      per_page: 50,
      activeActivity: Object
    };
  },
  mounted() {
    $("#" + this.id).DataTable();
  },
  methods: {
    deleteActivity(activity_id) {
      this.$inertia.delete(route("activities.destroy", activity_id), {
        onBefore: () => confirm("Supprimer activité?"),
        preserveScroll: true
      });
    },
    showDetail(activity_) {
      this.activeActivity = activity_;
      $("#activityShow").modal();
    }
  }
};
</script>


