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
          <td>{{ item.season ? item.season.season_txt : "" }}</td>
          <td>{{ dateFr(item.date_start) }}</td>
          <td>{{ dateFr(item.date_end) }}</td>
          <td>
            <small
              class="badge badge-info text-white py-1"
              v-if="item.status == 'future'"
              ><i class="far fa-spinner"></i> pas encore commencé</small
            >
            <small
              class="badge badge-success text-white py-1"
              v-else-if="item.status == 'progress'"
              ><i class="far fa-clock"></i> en-cours</small
            >
            <small class="badge badge-secondary py-1" v-else
              ><i class="fa fa-check"></i> terminé</small
            >
          </td>
          <td>
            <inertia-link
              :href="
                route('establishments.plannings.index', {
                  establishment: item.establishment_id,
                  season_id: item.id
                })
              "
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              >Planning général
            </inertia-link>
            <inertia-link
              :href="route('establishments.seasons.trimesters.index', item.id)"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
            >
              Trimesters
            </inertia-link>
            <inertia-link
              :href="route('closings.index', { season_id: item.id })"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
            >
              Jours de fermeture
            </inertia-link>
            <inertia-link
              :href="
                route('establishments.seasons.passes.index', {
                  establishment: item.establishment_id,
                  season_id: item.id
                })
              "
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
            >
              Passes
            </inertia-link>
            <inertia-link
              :href="
                route('establishments.seasons.activities.index', {
                  establishment: item.establishment_id,
                  season_id: item.id
                })
              "
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
            >
              Activités
            </inertia-link>
            <a
              href="#"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              @click.prevent="showModalParameterPriceRegistration(item.id)"
              >Frais d'inscription
            </a>
            <ModalRegistrationPrice :season="item" />
            <a
              href="#"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              @click.prevent="showModalParameterPriceManagement(item.id)"
            >
              Frais de gestion
            </a>
            <ModalManagementPrice :season="item" />
          </td>
          <td class="column-actions">
            <btn-edit :href="route('establishments.seasons.edit', item.id)" />
            <btn-delete @click.prevent="deleteSeason(item.id)" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <EstablishmentShow :establishment="activeShow" />
</template>

<script>
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import EstablishmentShow from "./show.vue";
import ModalRegistrationPrice from "./ModalRegistrationPrice.vue";
import ModalManagementPrice from "./ModalManagementPrice.vue";

export default {
  components: {
    BDropdown,
    JetDropdownLink,
    EstablishmentShow,
    ModalRegistrationPrice,
    ModalManagementPrice
  },
  props: ["TableData"],
  data() {
    return {
      labels: [],
      activeShow: Object
    };
  },
  beforeMount() {
    this.labels.push({ label: "Saison" });
    this.labels.push({ label: "Date debut" });
    this.labels.push({ label: "Date fin" });
    this.labels.push({ label: "Statut" });
    this.labels.push({ label: "Paramètres" });
  },
  mounted() {
    setTimeout(() => {
      if (this.q_.management_fee_season_id) {
        this.showModalParameterPriceManagement(
          this.q_.management_fee_season_id
        );
      }
    }, 1000);
  },
  methods: {
    deleteSeason(id) {
      this.$inertia.delete(route("establishments.seasons.destroy", id), {
        onBefore: () => confirm("Supprimer saison?")
      });
    },
    showModalParameterPriceRegistration(season_id) {
      $("#modal-parameter-registration-price-" + season_id).modal();
    },
    showModalParameterPriceManagement(season_id) {
      $("#modal-parameter-management-price-" + season_id).modal();
    }
  }
};
</script>


