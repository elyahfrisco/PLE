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
            <a
              href="#"
              @click.prevent="detachActivityEstablishment(item)"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              ><i class="fa fa-unlink"></i> Détacher</a
            >
          </td>
          <td>
            <b-dropdown btn="outline-secondary float-right py-0" sm="true">
              <template #title> Action </template>
              <template #content>
                <jet-dropdown-link :href="route('activities.edit', item.id)">
                  <i class="fa fa-edit"></i> Modifier
                </jet-dropdown-link>
              </template>
            </b-dropdown>
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
  props: ["TableData"],
  data() {
    return {
      labels: []
    };
  },
  beforeMount() {
    this.labels.push({ label: "Nom" });
    this.labels.push({ label: "Paramètres", width: "100" });
  },
  methods: {
    detachActivityEstablishment(data) {
      var routeParameters = {
        establishment: data.pivot.establishment_id,
        activity_id: data.id
      };

      this.$inertia.delete(
        route("establishments.activities.detach", routeParameters),
        {
          onBefore: () => confirm("Détacher l'activité au centre?"),
          preserveScroll: true
        }
      );
    }
  }
};
</script>


