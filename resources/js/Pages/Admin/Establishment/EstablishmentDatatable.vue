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
          <td>{{ item.email }}</td>
          <td>{{ item.phone }}</td>
          <td class="text-uppercase">
            {{ item.relaxation_center ? "oui" : "non" }}
          </td>
          <td>
            <inertia-link
              :href="route('establishments.seasons.index', item.id)"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              >Saisons</inertia-link
            >
            <inertia-link
              :href="route('establishments.passes.index', item.id)"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              >Passes</inertia-link
            >
            <inertia-link
              :href="route('establishments.plannings.index', item.id)"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              >Planning général</inertia-link
            >
            <inertia-link
              :href="route('closings.index', { establishment_id: item.id })"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              >Jours de fermeture</inertia-link
            >
            <!-- <inertia-link
              :href="route('establishments.activities.index', item.id)"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              >Activités ( pour le site )</inertia-link
            > -->
          </td>
          <td class="column-actions">
            <btn-show @click.prevent="showDetail(item)" type="button" />
            <btn-edit :href="route('establishments.edit', item.id)" />
            <btn-delete @click.prevent="deleteEstablishment(item.id)" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <establishment-show :establishment="activeShow" />
</template>

<script>
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import EstablishmentShow from "./show.vue";

export default {
  components: {
    BDropdown,
    JetDropdownLink,
    EstablishmentShow
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
    this.labels.push({ label: "Email" });
    this.labels.push({ label: "Téléphone" });
    this.labels.push({ label: "Centre de soin", width: "100" });
    this.labels.push({ label: "Paramètres", width: "150" });
  },
  methods: {
    deleteEstablishment(id) {
      this.$inertia.delete(route("establishments.destroy", id), {
        onBefore: () => confirm("Supprimer etablisement?")
      });
    },
    showDetail(data) {
      this.activeShow = data;
      $("#establishmentShow").modal();
    }
  }
};
</script>


