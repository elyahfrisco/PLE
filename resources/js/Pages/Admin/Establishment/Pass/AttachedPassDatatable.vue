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
              @click.prevent="detachPassEstablishment(item)"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              ><i class="fa fa-unlink"></i> Détacher</a
            >
          </td>
          <td class="column-actions">
            <btn-edit :href="route('passes.edit', item.id)" />
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
    detachPassEstablishment(data) {
      var routeParameters = {
        establishment: data.pivot.establishment_id,
        pass_id: data.id
      };

      this.$inertia.delete(
        route("establishments.passes.detach", routeParameters),
        {
          onBefore: () => confirm("Détacher le Pass au centre?"),
          preserveScroll: true
        }
      );
    }
  }
};
</script>


