<template>
  <inertia-data-table :searchable="true" :pagination="TableData">
    <template #header>
      <table-header width="20" name="id">Code</table-header>
      <table-header width="50" name="created_at">Création</table-header>
      <table-header width="20"></table-header>
      <table-header name="first_name">Prènom et Nom</table-header>
      <template v-if="account_type != 'prospect' && account_type != 'attente'">
        <table-header>Centre</table-header>
        <table-header>Activités</table-header>
      </template>
      <template v-if="account_type == 'prospect' || account_type == 'attente'">
        <table-header>Activités souhaités</table-header>
      </template>
      <table-header width="150">Statut</table-header>
      <table-header class="text-right" width="127px">Actions</table-header>
    </template>
    <template #content>
      <tr v-for="(item, index) in TableData.data" :key="item.id">
        <td>{{ item.id }}</td>
        <td>{{ dateHFr(item.created_at) }}</td>
        <td>
          <profil-photo :user="item" :width="30" class="mr-2" />
        </td>
        <td
          class="pointer"
          @click="
            this.$inertia.visit(
              route('account.index', { user_id: item.user_id })
            )
          "
        >
          {{ item.full_name }}
        </td>
        <template
          v-if="account_type != 'prospect' && account_type != 'attente'"
        >
          <td>
            <template v-if="item.user_establishments.length > 0">
              <template
                v-for="establishment in item.user_establishments"
                :key="establishment.id"
              >
                <p class="establishment mb-0 ml-1">
                  - <span class="text-uppercase">{{ establishment.name }}</span>
                </p>
              </template>
            </template>
          </td>
          <td>
            <template v-if="item.user_activities.length > 0">
              <p
                class="activity mb-0 ml-1"
                v-for="activity in item.user_activities"
                :key="activity.id"
              >
                -
                <span class="text-uppercase">{{ activity.name }}</span>
                <inertia-link
                  :href="
                    route('customers.index') +
                      '?' +
                      serializeUrl({
                        'filterBy[planning_id]': activity.planning_id,
                        'filterBy[establishment_id]': activity.establishment_id
                      })
                  "
                  >{{ activity.group_name }}</inertia-link
                >
              </p>
            </template>
          </td>
        </template>
        <template
          v-if="account_type == 'prospect' || account_type == 'attente'"
        >
          <td>
            <template v-if="item.user_activities.length > 0">
              <p
                class="activity mb-0 ml-1"
                v-for="activity in item.user_activities"
                :key="activity.id"
              >
                -
                <span class="text-uppercase">{{ activity.name }}</span>
                {{ activity.group_name }}
              </p>
            </template>
          </td>
        </template>

        <td>{{ item.status_fr }}</td>

        <td class="column-actions text-center">
          <btn-show :href="route('account.index', { user_id: item.user_id })" />
          <btn-edit :href="route('account.edit', { user_id: item.user_id })" />
          <div class="text-center">
            <button
              v-if="item.activated == '0'"
              type="button"
              class="btn btn-info btn-action btn-sm text-white"
              title="Mettre en liste d'attente"
              @click.prevent="toAttente(item.user_id, index)"
            >
              <i class="fa fa-list"></i>
            </button>
          </div>
        </td>
      </tr>
    </template>
  </inertia-data-table>
</template>

<script>
import profilPhoto from "@/Pages/Components/profilPhoto.vue";
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";

export default {
  components: {
    profilPhoto,
    InertiaDataTable,
    TableHeader
  },
  props: ["TableData", "account_type", "pagination"],
  methods: {
    toAttente(id, index) {
      if (confirm("Voulez vous ajouter ceci à la liste d'attente?")) {
        axios
          .post(route("api.user.attente"), {
            id: id
          })
          .then(response => {
            if (response) {
              this.TableData.data[index].activated = 2;
              alert("Modification prise en compte");
            }
          });
      }
    }
  }
};
</script>


