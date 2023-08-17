<template>
  <app-layout>
    <template #pageTitle
      >{{ session.establishment.sigle }} / Participants à l'activité {{ session.activity.name.toUpperCase() }} du
      {{ dateDayFr(session.date) }} {{ dateFr(session.date) }} de
      {{ H(session.time_start) }} à {{ H(session.time_end) }}
    </template>
    <div class="row">
      <div v-if="session.presence_checked_at" class="col-md-8">
        <i class="fa fa-check"></i> Presence effectué :
        {{ session.elapseTimePresence }} (
        {{ dateHFr(session.presence_checked_at) }} )
      </div>
      <div class="badge badge-primary pt-2 ml-auto font-weight-bold">
        <i class="fa fa-users"></i>
        {{ session.max_effective - session.participants_pass_trimester_count }}R
      </div>
      <div class="badge badge-info pt-2 ml-1 font-weight-bold">
        {{ participants.length }} / {{ session.max_effective }}
      </div>
      <a
        class="btn btn-sm btn-warning ml-1"
        data-toggle="tooltip"
        title="Ajouter des participants"
        @click.prevent="AddParticipants(session)"
      >
        <i class="fa fa-user-tag"></i
      ></a>
      <a
        href="#"
        @click="allParticipantsIsPresent"
        method="post"
        class="btn btn-success btn-sm ml-1"
      >
        <i class="fa fa-user-check"></i> Tous les participants sont tous
        présents
      </a>
    </div>
    <table id="table_participants" class="display table datatable manual">
      <thead>
        <tr>
          <template v-for="label in labels" :key="label.label">
            <th :style="'width:' + (label.width ? label.width + 'px' : '')">
              {{ label.label }}
            </th>
          </template>
          <th style="width: 400px !important" class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <template v-for="item in participants" :key="item.id">
          <tr
            class="subscriptions__activity"
            :class="item.session_status_txt + ' ' + item.presence_status_txt"
          >
            <td>
              <span
                class="pointer"
                @click="
                  this.$inertia.visit(
                    route('account.index', {
                      user_id: item.id,
                    })
                  )
                "
                >{{ item.full_name }}</span
              >
            </td>
            <td>
              {{ item.pass_name }}
            </td>
            <td>
              {{ item.status }} -
              <template v-if="!item.presence_confirmed_at">{{
                item.session_status_txt_fr
              }}</template>
              <inertia-link
                v-else
                :href="
                  route('establishments.plannings.sessions.participants', {
                    establishment: session.establishment_id,
                    activity_session: item.activity_session_id_for_catch_up,
                  })
                "
                >{{ item.session_status_txt_fr }}
              </inertia-link>
            </td>
            <td>
              <template v-if="!item.absence_prevention_id">
                <btn-presence @refresh="onRefresh" :user_session="item" />
              </template>
            </td>
            <td>
              <div class="d-block">
                <span
                  v-if="item.is_first_session"
                  class="badge border-white badge-info"
                >
                  Première séance
                </span>
              </div>
              <span
                class="badge payment border-white"
                :class="[item.payed ? 'badge-success' : 'badge-danger']"
              >
                Reglée : {{ item.payed ? "oui" : "non" }}
              </span>

              <template v-if="item.pass_trimester">
                <div class="d-block mb-1">
                  <span
                    v-if="item.renewal_status"
                    class="badge renewal border-white"
                    :class="[
                      ['stop', 'not_informed', ''].includes(item.renewal_status)
                        ? 'badge-info'
                        : 'badge-success',
                    ]"
                  >
                    Renouvellement :
                    {{ item.renewal_status_fr }}
                    <span v-if="item.renewal_subscription_id">
                      | Souscription effectué
                    </span>

                    <span v-if="item.renewal_subscription_payed">
                      | Reglé
                    </span>

                    <span
                      v-if="!item.renewal_subscription_payed"
                      class="
                        text-danger
                        pointer
                        btn btn-sm
                        bg-white
                        font-weight-bold
                        ml-2
                      "
                      @click="
                        item.renewal_subscription_bill_id &&
                          showModalAddPayment(item.renewal_subscription_bill_id)
                      "
                    >
                      €
                    </span>
                    <modal-form-payment
                      v-if="
                        item.renewal_subscription_bill_id &&
                        activeModalAddPaymentShowId ==
                          item.renewal_subscription_bill_id
                      "
                      :payment_methods="payment_methods"
                      :bill_id="item.renewal_subscription_bill_id"
                      @payment-saved="iReload()"
                    />
                  </span>
                </div>
              </template>
            </td>
            <td class="column-actions">
              <span class="d-none"
                >{{ item.mail1 }} {{ item.mail2 }} {{ item.email }}
                {{ item.city }} {{ item.address }}</span
              >

              <template v-if="!item.payed">
                <button
                  class="btn btn-success border-white"
                  @click="showModalAddPayment(item.bill_id)"
                >
                  <i class="fa fa-money-check-alt"></i> Payer
                </button>
                <modal-form-payment
                  v-if="
                    item.bill_id && activeModalAddPaymentShowId == item.bill_id
                  "
                  :payment_methods="payment_methods"
                  :bill_id="item.bill_id"
                  @payment-saved="iReload()"
                />
              </template>

              <template
                v-if="item.pass_trimester && !item.renewal_subscription_id"
              >
                <btn-renewal
                  :subscription_id="item.subscription_id"
                  :subscription="item"
                  :status="item.renewal_status"
                />

                <inertia-link
                  v-if="
                    $can('create_subscription_fro_renewal') &&
                    item.renewal_status &&
                    item.renewal_status === 'continue'
                  "
                  :href="
                    route('subscriptions.create', {
                      user_id: item.id,
                      renewal_id: item.renewal_id,
                      season_id: item.renewal_season_id,
                      establishment_id: item.renewal_establishment_id,
                      num_trimester: item.renewal_num_trimester,
                      pass_id: item.pass_id,
                      pass_type: 'trimester',
                      subscription_id: item.subscription_id,
                    })
                  "
                  class="
                    btn btn-outline-success btn-action btn-sm
                    py-0
                    px-1
                    ml-1
                  "
                  :title="
                    'Nouvelle souscription pour le renouvellement de ' +
                    item.full_name
                  "
                  data-toggle="tooltip"
                  ><i class="fa fa-plus"></i
                ></inertia-link>
              </template>

              <template
                v-if="
                  item.recuperation_request_id && !item.presence_confirmed_at
                "
              >
                <inertia-link
                  :href="
                    route('recuperation_requests.presence.confirm', {
                      id: item.recuperation_request_id,
                      admin: true,
                    })
                  "
                  class="btn btn-success btn-action btn-sm"
                  data-toggle="tooltip"
                  title="Confirmer la présence"
                  :onSuccess="() => iReload()"
                  ><i class="fa fa-check"></i
                ></inertia-link>
                <inertia-link
                  :href="
                    route('recuperation_requests.cancel', {
                      id: item.recuperation_request_id,
                      admin: true,
                    })
                  "
                  class="btn btn-danger btn-action btn-sm"
                  data-toggle="tooltip"
                  title="Annuler la demande de récupération"
                  :onSuccess="() => iReload()"
                  ><i class="fa fa-times"></i
                ></inertia-link>
              </template>
              <template v-else-if="item.queued_at">
                <btn-recuperation
                  class="btn-action btn-sm mr-1"
                  @request_recuperation="requestRecuperation(item)"
                />
              </template>
              <template v-if="!item.presence_confirmed_at">
                <template v-if="item.absence_prevention_id">
                  <btn-cancel-prevent-absence
                    class="btn-action btn-sm"
                    :absence_prevention_id="item.absence_prevention_id"
                    @absence-canceled="iReload()"
                  />
                </template>
                <template v-else-if="item.accomplished === null">
                  <btn-prevent-absence
                    @prevent_absence="showModalPreventAbsence(item)"
                  />
                </template>
              </template>
            </td>
          </tr>
        </template>
      </tbody>
    </table>
    <modal-request-recuperation
      v-if="activeSessionForRequestRecuperation"
      :sessionInQueue="activeSessionForRequestRecuperation"
    />

    <modal-prevent-absence
      v-if="selectedUserPreventAbsence"
      :user="selectedUserPreventAbsence"
      :selectedSeance="session"
    />

    <modal-add-participants
      :session="activeSessionForParticipantsAdd"
      :preserveState="true"
      @participantsAdded="refreshSession"
    />
  </app-layout>
</template>

<script>
import BtnPresence from "./btnPresence.vue";
import BtnPreventAbsence from "@/Pages/Components/Planning/BtnPreventAbsence.vue";
import BtnCancelPreventAbsence from "@/Pages/Components/Planning/BtnCancelPreventAbsence.vue";
import modalPreventAbsence from "@/Pages/Components/modalPreventAbsence.vue";
import BtnRecuperation from "@/Pages/Components/Planning/BtnRecuperation.vue";
import ModalRequestRecuperation from "@/Pages/Components/ModalRequestRecuperation.vue";
import ModalAddParticipants from "@/Pages/Components/Planning/ModalAddParticipants.vue";
import BtnRenewal from "@/Pages/Components/Renewal/BtnRenewal.vue";
import ModalFormPayment from "@/Pages/Invoice/Unpaid/ModalFormPayment.vue";

export default {
  props: ["session", "participants"],
  components: {
    BtnPresence,
    modalPreventAbsence,
    BtnPreventAbsence,
    BtnCancelPreventAbsence,
    BtnRecuperation,
    ModalRequestRecuperation,
    ModalAddParticipants,
    BtnRenewal,
    ModalFormPayment,
  },
  data() {
    return {
      labels: [],
      selectedUserPreventAbsence: null,
      activeSessionForRequestRecuperation: null,
      activeSessionForParticipantsAdd: {},
      activeModalAddPaymentShowId: null,
      payment_methods: [],
    };
  },
  beforeMount() {
    this.labels.push({ label: "Client" });
    this.labels.push({ label: "Pass" });
    this.labels.push({ label: "Statut de presence" });
    this.labels.push({ label: "Presence", width: "200" });
    this.labels.push({ label: "Satut", width: "150" });
  },
  mounted() {
    $("#table_participants").DataTable({
      paging: false,
    });
  },
  methods: {
    allParticipantsIsPresent() {
      this.$inertia.post(
        route("session.participants.presence.all", {
          activity_session_id: this.session.id,
        }),
        {},
        {
          onBefore: () => {
            return confirm("Tous les mondes sont tous présents ?");
          },
          onSuccess: () => {
            this.iReload();
          },
        }
      );
    },
    showModalAddPayment(bill_id) {
      this.activeModalAddPaymentShowId = bill_id;
      setTimeout(() => {
        $("#add-payment" + bill_id).modal();
      }, 300);
    },
    showModalPreventAbsence(user) {
      this.selectedUserPreventAbsence = user;
      setTimeout(() => {
        $("#modal-prevent-absence").modal();
      }, 500);
    },
    onRefresh() {
      this.iReloadPartial("participants", "session");
    },
    requestRecuperation(data) {
      this.activeSessionForRequestRecuperation = data;
      setTimeout(() => {
        $("#modal-request-recuperation").modal();
      }, 100);
    },
    AddParticipants(session) {
      axios
        .get(
          route("api.establishments.plannings.sessions", {
            establishment: session.establishment_id,
          }),
          {
            params: {
              session_id: session.id,
            },
          }
        )
        .then((response) => {
          this.activeSessionForParticipantsAdd = response.data[0];
          $("#add-participants").modal();
        });
    },
    refreshSession() {
      this.reload();
    },
    getPaymentMethods() {
      axios.get(route("api.payments.methods")).then((response) => {
        if (response.data) {
          this.payment_methods = this.toSelect(response.data, "name");
        }
      });
    },
  },
};
</script>
