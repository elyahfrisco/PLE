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
          <td class="text-capitalize">{{ item.name }}</td>
          <td>
            {{ item.number_sessions }}
            <span v-if="!!item.period_validity && !isNaN(item.period_validity)"
              >( {{ item.period_validity }} mois de validité )</span
            >
          </td>
          <!-- <td>{{ item.category?item.category.name:'non classé' }}</td> -->
          <td>
            <inertia-link
              :href="route('passes.activities', item.id)"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              >Activités</inertia-link
            >
          </td>
          <td class="column-actions">
            <btn-edit :href="route('passes.edit', item.id)" />
            <btn-delete @click.prevent="deletePass(item.id)" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <pass-show :pass="activeShow" />
</template>

<script>
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import PassShow from "./show.vue";

export default {
  components: {
    BDropdown,
    JetDropdownLink,
    PassShow
  },
  props: ["TableData"],
  data() {
    return {
      labels: [],
      activeShow: Object
    };
  },
  beforeMount() {
    this.labels.push({ label: "Nom" });
    this.labels.push({ label: "Nombre des séances" });
    this.labels.push({ label: "Parametres", width: "100" });
  },
  methods: {
    deletePass() {
      this.$inertia.delete(route("passes.destroy", this.pass.id), {
        onBefore: () => confirm("Supprimer Pass?")
      });
    },
    showDetail(data) {
      this.activeShow = data;
      $("#establishmentShow").modal();
    }
  }
};
</script>


