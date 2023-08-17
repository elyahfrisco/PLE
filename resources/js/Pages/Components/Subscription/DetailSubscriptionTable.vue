<template>
  <template v-if="showMode">
    <div class="form-row">
      <btn-edit
        v-if="
          subscription.pass.PassCategory == 'other' &&
            subscription.activities.length < subscription.number_of_sessions
        "
        type="button"
        @click="toggleModeChoiceNewSessionForSubscription()"
        >Réserver les séances restantes</btn-edit
      >
      <template v-if="$can('edit_subscription')">
        <btn-edit
          v-if="subscription.activities.length > 1"
          type="button"
          :disabled="!form.selected_subscription_sessions_id.length"
          @click="toggleModeChoiceNewSessionForSubscription()"
          >Modifier les sélectionnés</btn-edit
        >
        <btn-delete
          v-if="subscription.activities.length > 1"
          @click="deleteSubscriptionActivities()"
          :disabled="!form.selected_subscription_sessions_id.length"
          >Supprimer les sélectionnés</btn-delete
        >
      </template>
    </div>
    <table class="table">
      <thead>
        <tr>
          <th>Date</th>
          <th>Heure</th>
          <th>Activité</th>
          <th>Status</th>
          <th>Motif de l’absence</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="activity_session in subscription.activities"
          :key="activity_session.id"
          class="subscriptions__activity"
          :class="
            activity_session.session_status_txt +
              ' ' +
              activity_session.presence_status_txt
          "
        >
          <td class="text-uppercase">
            <div v-if="$can('edit_subscription')" class="form-group mr-1">
              <div class="icheck-primary icheck-sm">
                <template
                  v-if="
                    activity_session.presence_status_txt === null &&
                      subscription.activities.length > 1 &&
                      !activity_session.presence.is_recuperation
                  "
                >
                  <input
                    type="checkbox"
                    :id="'select_activity_session_' + activity_session.id"
                    v-model="form.selected_subscription_sessions_id"
                    :value="activity_session.id"
                  />
                </template>
                <label :for="'select_activity_session_' + activity_session.id">
                  {{ activity_session.day_name }}
                </label>

                <a
                  target="_blank"
                  :href="
                    route('establishments.plannings.sessions.participants', {
                      establishment: activity_session.establishment_id,
                      activity_session: activity_session.activity_session_id
                    })
                  "
                  data-toggle="tooltip"
                  title="Liste des participants"
                >
                  {{ dateFr(activity_session.date) }}
                </a>
              </div>
            </div>
            <label v-else
              >{{ activity_session.day_name }}
              {{ dateFr(activity_session.date) }}</label
            >
          </td>
          <td>
            {{ activity_session.schedule.start }} à
            {{ activity_session.schedule.end }}
          </td>
          <td class="text-uppercase">
            {{ activity_session.activity.name }}
          </td>
          <td>
            <div class="d-block">
              <span
                v-if="activity_session.is_first"
                class="badge border-white badge-info"
              >
                Première séance
              </span>
            </div>
            <div class="d-block">
              <span
                v-if="activity_session.presence.is_recuperation"
                class="badge border-white badge-info"
              >
                Séance de récuperation
              </span>
            </div>

            {{ activity_session.session_status_txt_fr }}
          </td>
          <td>
            {{
              activity_session.absence_prevention != null
                ? activity_session.absence_prevention.motif
                : activity_session.presence_status_txt_fr
            }}
          </td>
          <td class="column-actions">
            <template
              v-if="
                activity_session.presence_status_txt === null ||
                  ([
                    'prevent_before_6',
                    'prevent_out_of_time',
                    'absent'
                  ].includes(activity_session.presence_status_txt) &&
                    activity_session.session_status_txt !==
                      'presence_for_recuperation_confirmed')
              "
            >
              <template
                v-if="
                  subscription.customer.status !== 'old_customer' &&
                    !subscription.customer.activated == 0
                "
              >
                <btn-cancel-prevent-absence
                  v-if="activity_session.absence_prevention_id"
                  class="btn-action btn-sm"
                  :absence_prevention_id="
                    activity_session.absence_prevention_id
                  "
                  @absence-canceled="
                    $emitter.emit(
                      'refresh-detail-subscription-' + subscription.id
                    )
                  "
                />

                <btn-prevent-absence
                  v-else-if="
                    activity_session.accomplished === null &&
                      activity_session.presence_status_txt === null
                  "
                  class="btn-action btn-sm"
                  @prevent_absence="showModalPreventAbsence(activity_session)"
                />

                <template
                  v-if="
                    activity_session?.absence_prevention?.queue
                      ?.recuperation_request
                  "
                >
                  <template
                    v-if="
                      !activity_session.absence_prevention.queue
                        .recuperation_request.presence_confirmed_at
                    "
                  >
                    <btn-edit-recuperation
                      v-if="$can('edit_recuperation_request')"
                      :queue_id="
                        activity_session.absence_prevention.queue
                          .recuperation_request.queue_id
                      "
                    />
                    <inertia-link
                      :href="
                        route('recuperation_requests.presence.confirm', {
                          id:
                            activity_session.absence_prevention.queue
                              .recuperation_request.id,
                          redirect_back: true
                        })
                      "
                      preserve-state
                      class="btn btn-success btn-action btn-sm"
                      data-toggle="tooltip"
                      title="Confirmer la présence"
                      :onSuccess="refresh_subscription_list"
                      ><i class="fa fa-check"></i
                    ></inertia-link>
                    <inertia-link
                      :href="
                        route('recuperation_requests.cancel', {
                          id:
                            activity_session.absence_prevention.queue
                              .recuperation_request.id,
                          redirect_back: true
                        })
                      "
                      preserve-state
                      class="btn btn-danger btn-action btn-sm"
                      data-toggle="tooltip"
                      title="Annuler la demande de récupération"
                      :onSuccess="refresh_subscription_list"
                      ><i class="fa fa-times"></i
                    ></inertia-link>
                  </template>
                </template>
                <btn-recuperation
                  v-else-if="
                    activity_session.absence_prevention?.queue &&
                      !activity_session.absence_prevention?.queue
                        ?.recuperation_request
                  "
                  :title="
                    auth_user.role_name != 'admin'
                      ? 'Donner de la récupération pour cette'
                      : 'Récupérer la séance'
                  "
                  class="btn-action"
                  @request_recuperation="
                    requestRecuperation({
                      ...activity_session.absence_prevention.queue,
                      can_catch_up_until: activity_session.can_catch_up_until,
                      renewal: subscription.renewal,
                      activity_session: activity_session
                    })
                  "
                />
              </template>

              <template v-if="$can('edit_subscription')">
                <btn-edit
                  type="button"
                  @click="editActivity(activity_session.id)"
                />
                <btn-delete
                  v-if="subscription.activities.length > 1"
                  type="button"
                  @click="deleteActivity(activity_session.id)"
                />
              </template>
            </template>
          </td>
        </tr>
      </tbody>
    </table>
    <modal-prevent-absence
      v-if="selectedUserPreventAbsence"
      :user="selectedUserPreventAbsence"
      :subscription_id="subscription.id"
      :selectedSeance="selectedSeance"
      @modal-closing="modalClosing()"
      @event1="refreshComment"
    />
  </template>

  <div v-else>
    <Calendar
      v-if="modeCalendarActive"
      :calSync="true"
      @set_new_selected_sessions_id="setSessions"
      @cancel_selecte_sessions="
        () => {
          showMode = !showMode;
          modeCalendarActive = !modeCalendarActive;
          form.selected_subscription_sessions_id = [];
        }
      "
      :filter="{
        user_id: subscription.user_id,
        establishment_id: subscription.establishment_id,
        selected_subscription_sessions_id_to_replace:
          form.selected_subscription_sessions_id,
        calendar_for: 'pass_' + subscription.pass.PassCategory,
        number_of_sessions_remains_to_be_chosen:
          subscription.pass.PassCategory === 'other'
            ? subscription.number_of_sessions - subscription.activities.length
            : null,
        pass_id:
          subscription.pass.PassCategory === 'other'
            ? subscription.pass.id
            : null,
        expire_date: subscription.expired_at,
        view: 'year'
      }"
    ></Calendar>
  </div>

  <modal-request-recuperation
    v-if="activeSessionForRequestRecuperation"
    :sessionInQueue="activeSessionForRequestRecuperation"
    :emit_after_save="true"
    @recuperation_request_saved="refresh_subscription_list"
  />
</template>

<script>
import Calendar from "@/Pages/Components/Calendar.vue";
import BtnPreventAbsence from "@/Pages/Components/Planning/BtnPreventAbsence.vue";
import BtnCancelPreventAbsence from "@/Pages/Components/Planning/BtnCancelPreventAbsence.vue";
import modalPreventAbsence from "@/Pages/Components/modalPreventAbsence.vue";
import BtnRecuperation from "@/Pages/Components/Planning/BtnRecuperation.vue";
import BtnEditRecuperation from "@/Pages/Components/Planning/BtnEditRecuperation.vue";
import ModalRequestRecuperation from "@/Pages/Components/ModalRequestRecuperation.vue";

export default {
  props: ["subscription"],
  components: {
    Calendar,
    BtnPreventAbsence,
    BtnCancelPreventAbsence,
    modalPreventAbsence,
    BtnRecuperation,
    BtnEditRecuperation,
    ModalRequestRecuperation
  },
  data() {
    return {
      form: {
        selected_subscription_sessions_id: [],
        new_selected_sessions_id: []
      },
      modeCalendarActive: false,
      showMode: "show",
      selectedUserPreventAbsence: null,
      selectedSeance: null,
      activeSessionForRequestRecuperation: null
    };
  },
  methods: {
    refreshComment() {
      this.$emit("event2");
    },
    toggleModeChoiceNewSessionForSubscription() {
      this.modeCalendarActive = !this.modeCalendarActive;
      this.showMode = !this.showMode;
    },
    deleteSubscriptionActivities() {
      let s = this.form.selected_subscription_sessions_id.length > 1 ? "s" : "";
      let message =
        "Supprimer les séance" +
        s +
        " selectionée" +
        s +
        " ? Le montant de la facture " +
        (this.subscription.payment_id
          ? "ne sera plus modifié"
          : "sera modifié");
      confirm(message)
        ? axios
            .delete(
              route("subscriptions.activities.delete", {
                subscription_activities_id: this.form
                  .selected_subscription_sessions_id,
                subscription: this.subscription.id
              })
            )
            .then(response => {
              this.form.selected_subscription_sessions_id = [];
              toastr.success(response.data);
              this.$emitter.emit(
                "refresh-detail-subscription-" + this.subscription.id
              );
            })
        : null;
    },

    setSessions(sessions) {
      this.form.new_selected_sessions_id = sessions;
      setTimeout(async () => {
        // if (this.form.selected_subscription_sessions_id.length == 1) {
        //   let verification =
        //     await this.verifyRecuperationRelatedInSelectedSession();

        //   if (verification.data.result && confirm(verification.data.message)) {
        //     // this.updateSubscription();
        //   } else {
        //     // this.updateSubscription();
        //   }
        // } else {
        //   // this.updateSubscription();
        // }
        this.updateSubscription();
      }, 100);
      this.toggleModeChoiceNewSessionForSubscription();
    },
    async verifyRecuperationRelatedInSelectedSession() {
      return await axios.post(
        route("api.subscription.verify_related_recuperation"),
        {
          subscription_activities_id: this.form
            .selected_subscription_sessions_id
        }
      );
    },
    updateSubscription() {
      let new_selected_sessions_id = [];

      for (let key of Object.keys(this.form.new_selected_sessions_id)) {
        new_selected_sessions_id.push(
          this.form.new_selected_sessions_id[key].id
        );
      }

      axios
        .put(route("subscriptions.update", this.subscription), {
          selected_subscription_sessions_id: this.form
            .selected_subscription_sessions_id,
          new_selected_sessions_id: new_selected_sessions_id,
          pass_type: this.subscription.pass.PassCategory
        })
        .then(response => {
          this.form.new_selected_sessions_id = [];
          this.form.selected_subscription_sessions_id = [];

          toastr.success(response.data);

          this.$emitter.emit(
            "refresh-detail-subscription-" + this.subscription.id
          );
        });
    },
    modalClosing() {
      setTimeout(() => {
        this.selectedUserPreventAbsence = null;
        this.selectedSeance = null;

        $("body").addClass("modal-open");
        this.$emitter.emit(
          "refresh-detail-subscription-" + this.subscription.id
        );
      }, 500);
    },
    editActivity(id) {
      this.form.selected_subscription_sessions_id = [id];
      this.toggleModeChoiceNewSessionForSubscription();
    },
    deleteActivity(id) {
      this.form.selected_subscription_sessions_id = [id];
      this.deleteSubscriptionActivities();
    },
    showModalPreventAbsence(session) {
      this.selectedSeance = { ...session };
      this.selectedSeance.id = this.selectedUserPreventAbsence =
        session.user_id;
      setTimeout(() => {
        $("#modal-prevent-absence").modal();
      }, 500);
    },
    requestRecuperation(data) {
      this.activeSessionForRequestRecuperation = null;
      setTimeout(() => {
        this.activeSessionForRequestRecuperation = data;
        console.log(data);
        setTimeout(() => {
          $("#modal-request-recuperation").modal();
        }, 100);
      }, 100);
    },
    refresh_subscription_list() {
      this.activeSessionForRequestRecuperation = null;
      this.$emitter.emit("refresh-detail-subscription-" + this.subscription.id);
    }
  },
  computed: {
    can_edit_subscription() {
      return !["customer", "coach"].includes(this.auth_user.role_name);
    }
  }
};
</script>
