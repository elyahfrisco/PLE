<template>
  <inertia-data-table :pagination="TableData" :searchable="true">
    <template #header>
      <table-header name="created_at">Date de souscription</table-header>
      <table-header name="first_name">Client</table-header>
      <table-header name="establishment_name">Centre</table-header>
      <table-header name="season_year_start">Saison</table-header>
      <table-header>PASS</table-header>
      <table-header name="subscription_type">Type</table-header>
      <table-header>Activités</table-header>
      <table-header name="start_at">Date de Debut/Fin</table-header>
      <table-header>Statut</table-header>
      <table-header>Renouvelement</table-header>
      <table-header class="text-right">Actions</table-header>
    </template>
    <template #content>
      <tr v-for="item in TableData.data" :key="item.id">
        <td>{{ dateHFr(item.created_at) }}</td>
        <td class="text-capitalize">
          <inertia-link
            as="span"
            class="mr-1 pointer"
            :href="route('account.index', { user_id: item.customer.id })"
            >{{ item.customer.full_name }}</inertia-link
          >
          <inertia-link
            :href="route('subscriptions.create', { user_id: item.user_id })"
            class="btn btn-outline-success btn-sm py-0 px-1"
            :title="'Plus de souscription pour ' + item.customer.full_name"
            data-toggle="tooltip"
            ><i class="fa fa-plus"></i
          ></inertia-link>
        </td>
        <td>{{ item.establishment.name }}</td>
        <td>{{ item.season.year_start }} - {{ item.season.year_end }}</td>
        <td>
          {{ item.pass.name }}
          {{
            item.pass.PassCategory == "trimester"
              ? " T " + item.num_trimester
              : ""
          }}
          <span
            v-if="item.pass.PassCategory == 'other'"
            class="badge badge-secondary d-block pointer"
            @click.prevent="showDetailSubscription(item)"
          >
            {{ item.activities.length }} / {{ item.number_of_sessions }} séances
            réservées
          </span>
        </td>
        <td>{{ item.subscription_type == "child" ? "Enfant" : "Adult" }}</td>
        <td>
          <template v-if="!item.pass.pass_trimester && !item.pass.one_session">
            <strong
              class="mr-2"
              v-for="activity in item.activities"
              :key="activity.id"
            >
              <template v-if="activity.activity != undefined">
                {{ activity.activity.name }}
                <a
                  target="_blank"
                  :href="
                    route('establishments.plannings.sessions.participants', {
                      establishment: activity.establishment_id,
                      activity_session: activity.activity_session_id
                    })
                  "
                  data-toggle="tooltip"
                  title="Liste des participants"
                >
                  ({{ activity.group_name }})
                </a>
              </template>
              <br
            /></strong>
          </template>
          <template v-else>
            <strong v-if="item.activities[0] != undefined"
              >{{ item.activities[0].activity.name }}
              <a
                target="_blank"
                :href="
                  route('establishments.plannings.sessions.participants', {
                    establishment: item.activities[0].establishment_id,
                    activity_session: item.activities[0].activity_session_id
                  })
                "
                data-toggle="tooltip"
                title="Liste des participants"
              >
                ({{ item.activities[0].group_name }})
              </a>
            </strong>
          </template>
        </td>
        <td>
          {{ dateFr(item.date.start) }}
          <template v-if="item.date.start != item.date.end">
            au {{ dateFr(item.date.end) }}</template
          >
        </td>
        <td>
          <status-badge :status="item.status" />
          <br />
          <span class="badge badge-success" v-if="item.payment">réglé</span>
          <span class="badge badge-danger" v-else>non réglé</span>
        </td>
        <td class="text-center">
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
            href="#"
            class="btn btn-info"
            data-toggle="tooltip"
            title="Détail de la souscription"
            @click.prevent="() => showDetailSubscription(item)"
          >
            <i class="fa fa-eye"></i>
          </button>
          <button
            href="#"
            class="btn btn-info"
            data-toggle="tooltip"
            title="Détail de la facture"
            @click.prevent="() => showDetailInvoice(item)"
          >
            <i class="fa fa-money-check-alt"></i>
          </button>
          <modal-invoice
            v-if="activeBillDetailSubscriptionIdDisplayed == item.id"
            :bill_id="item.bill_id"
            :subscription_id="item.id"
          />
          <template v-if="!item.payment_id">
            <button
              class="btn btn-success"
              title="Payer la facture"
              @click="showModalAddPayment(item.bill_id)"
            >
              <i class="fa fa-money-check-alt"></i>
            </button>
            <modal-form-payment
              :payment_methods="payment_methods"
              :bill_id="item.bill_id"
              @payment-saved="hideModalAddPayment(item.bill_id)"
            />
          </template>
          <button
            href="#"
            class="btn btn-danger"
            data-toggle="tooltip"
            title="Annuler la souscription"
            @click.prevent="deleteSubscription(item)"
          >
            <i class="fa fa-trash"></i>
          </button>
        </td>
      </tr>
    </template>
  </inertia-data-table>
  <jet-modal id="editTrimester" title="Modifier trimestre">
    <form-subscription-parameter :subscriptionData="subscriptionData" />
  </jet-modal>
</template>

<script>
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import formSubscriptionParameter from "./Form/Index.vue";
import ModalDetailSubscription from "@/Pages/Components/Subscription/ModalDetailSubscription.vue";
import ModalInvoice from "@/Pages/Components/ModalInvoice.vue";
import BtnRenewal from "@/Pages/Components/Renewal/BtnRenewal.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import ModalFormPayment from "@/Pages/Invoice/Unpaid/ModalFormPayment.vue";

export default {
  components: {
    BDropdown,
    JetDropdownLink,
    formSubscriptionParameter,
    ModalDetailSubscription,
    ModalInvoice,
    BtnRenewal,
    TableHeader,
    InertiaDataTable,
    ModalFormPayment
  },
  props: ["TableData"],
  data() {
    return {
      labels: [],
      activeSubscriptionDetailDisplayed: Object,
      activeBillDetailSubscriptionIdDisplayed: null,
      subscriptionData: {},
      activePaymentModalShowId: null,
      payment_methods: []
    };
  },
  beforeMount() {
    this.getPaymentMethods();
  },
  methods: {
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
    },
    showModalAddPayment(bill_id) {
      $("#add-payment" + bill_id).modal();
    },
    hideModalAddPayment(bill_id) {
      $("#add-payment" + bill_id).modal("hide");
      $(".modal-open").removeClass("modal-open");
      $(".modal-backdrop").remove();
    },
    deleteSubscription(data) {
      let message = data.number_absence_prevention
        ? " Cette souscription a " +
          data.number_absence_prevention +
          " absence" +
          (data.number_absence_prevention > 1 ? "s" : "") +
          " prévenus"
        : "";
      this.$inertia.delete(route("subscriptions.destroy", data.id), {
        onBefore: () => confirm("Supprimer la souscription ? " + message)
      });
    },
    getPaymentMethods() {
      axios.get(route("api.payments.methods")).then(response => {
        if (response.data) {
          this.payment_methods = this.toSelect(response.data, "name");
        }
      });
    }
  }
};
</script>


