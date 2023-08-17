<template>
  <inertia-data-table
    :searchable="true"
    id="absences_table"
    :pagination="TableData"
  >
    <template #header>
      <table-header name="updated_at">Mise à jour</table-header>
      <table-header name="first_name">Prènom et Nom</table-header>
      <table-header name="establishment_name">Centre</table-header>
      <table-header name="season_year_start">Saison</table-header>
      <table-header name="num_trimester">Trimestre</table-header>
      <table-header>Activité</table-header>
      <table-header>Status</table-header>
      <table-header class="text-right" width="127px">Actions</table-header>
    </template>
    <template #content>
      <template v-for="item in TableData" :key="item.id">
        <tr v-if="item.subscription">
          <td>{{ dateHFr(item.updated_at) }}</td>
          <td>
            <inertia-link
              as="span"
              class="mr-1 pointer"
              :href="
                route('account.index', {
                  user_id: item.subscription.customer.id
                })
              "
              >{{ item.subscription.customer.full_name }}</inertia-link
            >
          </td>
          <td>
            {{ item.establishment.name }}
          </td>
          <td>{{ item.season ? item.season.season_txt : "" }}</td>
          <td>{{ item.num_trimester }}</td>
          <td>{{ item.activity?.name }}</td>
          <td>
            <span
              class="badge mr-1 text-left"
              :class="{
                'badge-danger': item.renewal_status === 'stop',
                'badge-success': item.renewal_status !== 'stop'
              }"
              >{{ item.status_fr }}
              <template v-if="item.subscription.renewal_subscription_id">
                <br />, souscription effectué
              </template>
            </span>
          </td>
          <td class="column-actions">
            <template v-if="!item.subscription.renewal_subscription_id">
              <btn-renewal
                :subscription="{ ...item.subscription, renewal: item }"
              />
              <inertia-link
                v-if="
                  $can('create_subscription_fro_renewal') &&
                    item.renewal_status === 'continue'
                "
                :href="
                  route('subscriptions.create', {
                    user_id: item.subscription.user_id,
                    renewal_id: item.id,
                    season_id: item.season_id,
                    establishment_id: item.establishment_id,
                    num_trimester: item.num_trimester,
                    pass_id: item.subscription.pass_id,
                    pass_type: 'trimester',
                    subscription_id: item.subscription.id
                  })
                "
                class="btn btn-outline-success btn-action btn-sm py-0 px-1 ml-1"
                :title="
                  'Nouvelle souscription pour le renouvellement de ' +
                    item.subscription.customer.full_name
                "
                data-toggle="tooltip"
                ><i class="fa fa-plus"></i
              ></inertia-link>
              <button
                href="#"
                class="btn btn-danger btn-sm btn-action"
                data-toggle="tooltip"
                title="Annuler le renouvellement"
                @click.prevent="deleteRenewal(item)"
              >
                <i class="fa fa-trash"></i>
              </button>
            </template>
          </td>
        </tr>
      </template>
    </template>
  </inertia-data-table>
</template>

<script>
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";
import profilPhoto from "@/Pages/Components/profilPhoto.vue";
import BtnRenewal from "@/Pages/Components/Renewal/BtnRenewal.vue";
export default {
  components: {
    profilPhoto,
    InertiaDataTable,
    TableHeader,
    BtnRenewal
  },
  props: ["TableData"],
  methods: {
    deleteRenewal(data) {
      this.$inertia.delete(route("renewals.destroy", data.id), {
        onBefore: () => confirm("Supprimer le renouvellement ?"),
        preserveScroll: true
      });
    }
  }
};
</script>


