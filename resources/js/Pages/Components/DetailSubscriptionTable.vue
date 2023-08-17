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
          <th>Presence</th>
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
                      subscription.activities.length > 1
                  "
                >
                  <input
                    type="checkbox"
                    :id="'select_activity_session_' + activity_session.id"
                    v-model="form.selected_subscription_sessions_id"
                    :value="activity_session.id"
                  />
                </template>
                <label :for="'select_activity_session_' + activity_session.id"
                  >{{ activity_session.day_name }}
                  {{ dateFr(activity_session.date) }}</label
                >
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
          <td class="text-uppercase">{{ activity_session.activity.name }}</td>
          <td>{{ activity_session.session_status_txt_fr }}</td>
          <td>{{ activity_session.presence_status_txt_fr }}</td>
          <td class="column-actions">
            <template
              v-if="
                activity_session.presence_status_txt === null ||
                  [
                    'prevent_before_6',
                    'prevent_out_of_time',
                    'absent'
                  ].includes(activity_session.presence_status_txt)
              "
            >
              <btn-cancel-prevent-absence
                v-if="activity_session.absence_prevention_id"
                class="btn-action btn-sm"
                :absence_prevention_id="activity_session.absence_prevention_id"
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
      :selectedSeance="selectedSeance"
      @modal-closing="modalClosing()"
    />
  </template>

  <div v-else>
    <Calendar
      v-if="modeCalendarActive"
      :calSync="true"
      @set_new_selected_sessions_id="setSessions"
      @cancel_selecte_sessions="
        showMode = !showMode;
        modeCalendarActive = !modeCalendarActive;
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
        view: 'year'
      }"
    ></Calendar>
  </div>
</template>

<script>
import Calendar from "@/Pages/Components/Calendar.vue";
import BtnPreventAbsence from "@/Pages/Components/Planning/BtnPreventAbsence.vue";
import BtnCancelPreventAbsence from "@/Pages/Components/Planning/BtnCancelPreventAbsence.vue";
import modalPreventAbsence from "@/Pages/Components/modalPreventAbsence.vue";

export default {
  props: ["subscription"],
  components: {
    Calendar,
    BtnPreventAbsence,
    BtnCancelPreventAbsence,
    modalPreventAbsence
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
      selectedSeance: null
    };
  },
  methods: {
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
      setTimeout(() => {
        this.updateSubscription();
      }, 100);
      this.toggleModeChoiceNewSessionForSubscription();
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
    }
  },
  computed: {
    can_edit_subscription() {
      return !["customer", "coach"].includes(this.auth_user.role_name);
    }
  }
};
</script>
