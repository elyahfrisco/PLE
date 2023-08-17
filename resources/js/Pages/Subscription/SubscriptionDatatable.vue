<template>
  <inertia-data-table :pagination="TableData" :searchable="true">
    <template #header>
      <table-header name="created_at">Date de souscription</table-header>
      <table-header v-if="!is_facture" name="first_name">Client</table-header>
      <table-header name="establishment_name">Centre</table-header>
      <table-header name="season_year_start">Saison</table-header>
      <table-header>PASS</table-header>
      <table-header name="subscription_type">Type</table-header>
      <table-header>Activités</table-header>
      <table-header name="start_at">Date de Debut/Fin</table-header>
      <table-header name="start_at">Montant</table-header>
      <table-header>Statut</table-header>
      <table-header v-if="!is_facture">Renouvelement</table-header>
      <table-header class="text-right">Actions</table-header>
    </template>
    <template #content>
      <tr v-for="item in TableData.data" :key="item.id">
        <td>{{ dateHFr(item.created_at) }}</td>
        <td v-if="!is_facture" class="text-capitalize">
          {{ item.customer.first_name }} {{ item.customer.name.toUpperCase() }}
        </td>
        <td>{{ item.establishment.name }}</td>
        <td>{{ item.season.year_start }} - {{ item.season.year_end }}</td>
        <td>{{ item.pass.name }}</td>
        <td>{{ item.subscription_type == "child" ? "Enfant" : "Adult" }}</td>
        <td>
          <template v-if="item.pass.PassCategory == 'other'">
            <strong
              class="mr-2"
              v-for="activity in item.activities"
              :key="activity.id"
              >{{
                activity.activity != undefined
                  ? activity.activity.name + " (" + activity.group_name + ")"
                  : ""
              }}<br
            /></strong>
          </template>
          <strong>{{
            item.activities[0] != undefined
              ? item.activities[0].activity.name +
                " (" +
                item.activities[0].group_name +
                ")"
              : ""
          }}</strong>
        </td>
        <td>
          {{ dateFr(item.date.start) }}
          <template v-if="item.date.start != item.date.end">
            au {{ dateFr(item.date.end) }}</template
          >
        </td>
        <td>{{ item.amount }}€</td>
        <td>
          <status-badge :status="item.status" />
          <br />
          <span class="badge badge-success" v-if="item.payment">réglé</span>
          <span class="badge badge-danger" v-else>non réglé</span>
        </td>
        <td v-if="!is_facture">
          <span
            v-if="item.renewal"
            class="badge mr-1"
            :class="{
              'badge-danger': item.renewal.renewal_status === 'stop',
              'badge-success': item.renewal.renewal_status !== 'stop'
            }"
          >
            {{ item.renewal.status_fr }}
            <template v-if="item.renewal_subscription_id">
              <br />, souscription effectué
            </template>
          </span>
          <template v-if="!item.renewal_subscription_id">
            <btn-renewal
              :subscription="item"
              v-if="item.pass.PassCategory == 'trimester'"
            />
            <a
              v-if="
                $can('create_subscription_fro_renewal') &&
                  item.renewal &&
                  item.renewal.renewal_status !== 'stop' &&
                  item.renewal.renewal_status !== 'not_informed'
              "
              target="_blank"
              :href="
                route('subscriptions.create', {
                  user_id: item.user_id,
                  renewal_id: item.renewal.id,
                  season_id: item.renewal.season_id,
                  establishment_id: item.renewal.establishment_id,
                  num_trimester: item.renewal.num_trimester,
                  pass_id: item.pass_id,
                  pass_type: item.pass.PassCategory,
                  subscription_id: item.id
                })
              "
              class="btn btn-outline-success btn-sm py-0 px-1 ml-1"
              :title="
                'Nouvelle souscription pour le renouvellement de ' +
                  item.customer.full_name
              "
              data-toggle="tooltip"
              ><i class="fa fa-plus"></i
            ></a>
          </template>
        </td>
        <td class="column-actions">
          <modal-detail-subscription :subscription="item" />
          <button
            v-if="!is_facture"
            href="#"
            class="btn btn-info"
            data-toggle="tooltip"
            title="Détail de la souscription"
            @click.prevent="showDetailSubscription(item)"
          >
            <i class="fa fa-eye"></i>
          </button>
          <button
            href="#"
            class="btn btn-info"
            data-toggle="tooltip"
            title="Détail de la facture"
            @click.prevent="showDetailInvoice(item)"
          >
            <i class="fa fa-money-check-alt"></i>
          </button>
          <modal-invoice
            v-if="activeBillDetailSubscriptionIdDisplayed == item.id"
            :bill_id="item.bill_id"
            :subscription_id="item.id"
          />
          <button
          v-if="!is_facture"
            href="#"
            class="btn btn-danger"
            @click.prevent="deleteSubscription(item)"
          >
            <i class="fa fa-trash"></i>
          </button>
        </td>
      </tr>
    </template>
  </inertia-data-table>
</template>

<script>
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import ModalDetailSubscription from "@/Pages/Components/Subscription/ModalDetailSubscription.vue";
import ModalInvoice from "@/Pages/Components/ModalInvoice.vue";
import BtnRenewal from "@/Pages/Components/Renewal/BtnRenewal.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";

export default {
  components: {
    BDropdown,
    JetDropdownLink,
    ModalDetailSubscription,
    ModalInvoice,
    BtnRenewal,
    TableHeader,
    InertiaDataTable
  },
  props: ["TableData","is_facture"],
  data() {
    return {
      labels: [],
      activeShow: Object,
      subscriptionData: {},
      activeSubscriptionDetailDisplayed: Object,
      subscriptionData: {},
      activeBillDetailSubscriptionIdDisplayed: null
    };
  },
  beforeMount() {
    this.labels.push({ label: "Date" });
    this.labels.push({ label: "Client" });
    this.labels.push({ label: "Centre" });
    this.labels.push({ label: "Saison" });
    this.labels.push({ label: "PASS" });
    this.labels.push({ label: "Type" });
    this.labels.push({ label: "Activités" });
    this.labels.push({ label: "Date" });
    this.labels.push({ label: "Montant" });
    this.labels.push({ label: "Statut" });
  },
  methods: {
    editSubscription(data) {
      this.subscriptionData = data;
      $("#editTrimester").modal();
    },
    deleteSubscription(data) {
      this.$inertia.delete(route("subscriptions.destroy", data.id), {
        onBefore: () => confirm("Supprimer la souscription?")
      });
    },
    editSubscription(data) {
      this.subscriptionData = data;
      $("#editTrimester").modal();
    },
    showDetailSubscription(subscription) {
      this.activeSubscriptionDetailDisplayed = subscription;
      this.$emitter.emit("get-subscription-comments" + subscription.id);
      $("#modal-detail-subscription" + subscription.id).modal();
    },
    showDetailInvoice(subscription) {
      this.activeBillDetailSubscriptionIdDisplayed = subscription.id;
      setTimeout(() => {
        $("#modal-detail-invoice-" + subscription.bill_id).modal();
      }, 50);
    }
  }
};
</script>


