<template>
  <inertia-data-table
    :searchable="true"
    id="table_relaunchs"
    :pagination="TableData"
    :ajaxMode="true"
  >
    <template #header>
      <table-header>Date création</table-header>
      <table-header>Objet</table-header>
      <table-header>Contenu</table-header>
      <table-header>Destinataire</table-header>
      <table-header>Date d'envoi</table-header>
      <table-header>status</table-header>
      <table-header>Type</table-header>
      <th style="width: 127px" class="text-right">Actions</th>
    </template>

    <template #content>
      <template v-if="TableData.data">
        <tr v-for="item in TableData.data" :key="item.id">
          <td>{{ dateHFr(item.created_at) }}</td>
          <td>{{ item.subject }}</td>
          <td>
            <perfect-scrollbar>
              <div class="w-100" v-html="item.content"></div>
            </perfect-scrollbar>
          </td>
          <td>
            <inertia-link
              as="span"
              class="mr-1 pointer"
              :href="route('account.index', { user_id: item.user?.id })"
              >{{ item.user?.full_name }}</inertia-link
            >
          </td>
          <td>
            {{ dateHFr(item.date_relaunch) }} <br />
            {{ item.elapseTime }}
          </td>
          <td>
            <span v-if="item.executed" class="badge badge-success">envoyé</span>
            <span v-else class="badge badge-secondary">future</span>
          </td>
          <td>{{ item.relaunch_type }}</td>
          <td class="column-actions">
            <template v-if="!item.timeSpent">
              <inertia-link
                class="btn btn-outline-success btn-sm btn-action"
                data-toggle="tooltip"
                data-placement="top"
                title="Envoyer la relance maintenant"
                method="POST"
                preserve-scroll
                :href="
                  route('api.relaunchs.send_now', {
                    relaunch_id: item.id
                  })
                "
                :onBefore="
                  msgConfirm('Êtes-vous sûr d\'envoyer la relance maintenant ?')
                "
              >
                <i class="fa fa-paper-plane"></i>
              </inertia-link>
              <btn-delete @click="deleteRelaunch(item)" />
            </template>
          </td>
        </tr>
      </template>
      <tr v-else>
        <td colspan="8" class="text-center">Aucun relanche à afficher</td>
      </tr>
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
  data() {
    return {
      labels: [],
      activeShow: Object,
      subscriptionData: {}
    };
  },
  methods: {
    deleteRelaunch(data) {
      this.$inertia.delete(route("relaunchs.destroy", data.id), {
        onBefore: () => confirm("Supprimer la relance ?"),
        onSuccess: () => this.iReload()
      });
    }
  }
};
</script>

<style scoped>
.ps {
  max-height: 100px;
}
</style>
