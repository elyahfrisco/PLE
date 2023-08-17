<template>
  <inertia-data-table :pagination="users">
    <template #header>
      <table-header width="150">Date d'ajout</table-header>
      <table-header width="20"></table-header>
      <table-header width="150">Nom</table-header>
      <table-header>Sexe</table-header>
      <table-header>Email</table-header>
      <table-header>Telephone</table-header>
      <table-header>Statut</table-header>
      <table-header class="text-right">Actions</table-header>
    </template>
    <template #content>
      <tr v-for="item in users.data" :key="item.id">
        <td>{{ dateHFr(item.created_at) }}</td>
        <td>
          <profil-photo :user="item" :width="20" class="mr-1" />
        </td>
        <td
          class="pointer"
          @click="
            this.$inertia.visit(route('account.index', { user_id: item.id }))
          "
        >
          {{ item.full_name }}
        </td>
        <td v-if="!item.is_child">
          {{ item.gender == "female" ? "Femme" : "Homme" }}
        </td>
        <td v-else>{{ item.gender == "female" ? "Fille" : "Garçon" }}</td>
        <td>
          <a v-if="realMail(item.email)" :href="'mailto:' + item.email"
            >{{ item.email }},
          </a>
          <a v-if="realMail(item.mail1)" :href="'mailto:' + item.mail1"
            >{{ item.mail1 }},
          </a>
          <a v-if="realMail(item.mail2)" :href="'mailto:' + item.mail2">{{
            item.mail2
          }}</a>
        </td>
        <td>
          <span class="d-block" v-for="phone in item.phones" :key="phone.id"
            ><a :href="'tel:' + phone.phone">{{
              phone.phone.split(/(\d{2})/).join(" ")
            }}</a></span
          >
        </td>
        <td>
          <template v-if="item.activated">
            <template v-if="item.status === 'old_customer'">
              Ancient client
            </template>
            <template v-else-if="item.status_fr === 'waiting_customer'">
              List d'attente
            </template>
            <template v-else>
              Client
            </template>
          </template>
          <template v-else>
            prospect
          </template>
        </td>
        <td class="column-actions">
          <btn-show :href="route('account.index', { user_id: item.id })" />
          <btn-edit :href="route('account.edit', { user_id: item.id })" />
        </td>
      </tr>
    </template>
  </inertia-data-table>
</template>

<script>
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import profilPhoto from "@/Pages/Components/profilPhoto.vue";
export default {
  props: ["users"],
  components: {
    TableHeader,
    InertiaDataTable,
    profilPhoto
  }
};
</script>


