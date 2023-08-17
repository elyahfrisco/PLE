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
      <table-header>Activité</table-header>
      <table-header>Status</table-header>
      <table-header class="text-right" width="127px">Actions</table-header>
    </template>
    <template #content>
      <template v-for="item in TableData">
        <tr>
          <td>{{ dateHFr(item.updated_at) }}</td>
          <td>
            <inertia-link
              as="span"
              class="mr-1 pointer"
              :href="
                route('account.index', {
                  user_id: item.customer_id
                })
              "
              >{{ item.name }}</inertia-link
            >
          </td>
          <td>
            {{ item.establishment }}
          </td>
          <td>
            <template v-if="item.activities.length > 0">
              <p class="activity mb-0 ml-1" v-for="activity in item.activities">
                -
                <span class="text-uppercase">{{ activity[0] }}</span>
                {{ activity[1] }}
              </p>
            </template>
          </td>
          <td>
            <span class="badge mr-1 text-left badge-success">
              <span
                v-if="item.renewal_status == 'continue_change_time_else_stop'"
                >Continuer et changer horaire sinon STOP</span
              >
              <span v-else>Continuer et changer horaire</span>
            </span>
          </td>
          <td class="column-actions"></td>
        </tr>
      </template>
    </template>
  </inertia-data-table>
</template>

<script>
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";
export default {
  components: {
    InertiaDataTable,
    TableHeader
  },
  props: ["TableData"],
  methods: {}
};
</script>


