<template>
  <inertia-data-table :searchable="true" :pagination="TableData">
    <template #header>
      <table-header width="50" name="created_at">Création</table-header>
      <table-header width="20"></table-header>
      <table-header width="150" name="first_name">Prènom et Nom</table-header>
      <table-header width="150">Sexe</table-header>
      <table-header width="150" name="address">Adresse</table-header>
      <table-header class="text-right" width="127px">Actions</table-header>
    </template>
    <template #content>
      <tr v-for="item in TableData.data" :key="item.id">
        <td>{{ dateHFr(item.created_at) }}</td>
        <td>
          <profil-photo :user="item" :width="30" class="mr-2" />
        </td>
        <td
          class="pointer"
          @click="
            this.$inertia.visit(route('account.index', { user_id: item.id }))
          "
        >
          {{ item.first_name }}
          <span class="text-uppercase">{{ item.name }}</span>
        </td>
        <td>{{ item.gender == "female" ? "Femme" : "Homme" }}</td>
        <td>{{ item.address }}</td>
        <td>
          <b-dropdown btn="outline-secondary float-right py-0" sm="true">
            <template #title> Action </template>

            <template #content>
              <jet-dropdown-link
                :href="route('account.index', { user_id: item.id })"
              >
                <i class="fa fa-eye"></i> Voir
              </jet-dropdown-link>
              <jet-dropdown-link
                :href="route('account.edit', { user_id: item.id })"
              >
                <i class="fa fa-edit"></i> Modifier
              </jet-dropdown-link>
            </template>
          </b-dropdown>
        </td>
      </tr>
    </template>
  </inertia-data-table>
</template>

<script>
import profilPhoto from "@/Pages/Components/profilPhoto.vue";
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";

export default {
  components: {
    profilPhoto,
    BDropdown,
    JetDropdownLink,
    TableHeader,
    InertiaDataTable
  },
  props: ["TableData", "account_type"]
};
</script>


