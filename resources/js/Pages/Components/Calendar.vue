<template>
  <div
    class="py-2"
    v-if="filter.selected_subscription_sessions_id_to_replace?.length"
  >
    <button
      class="btn btn-success"
      :disabled="
        !(
          length_selected_seance ==
          filter.selected_subscription_sessions_id_to_replace?.length
        )
      "
      @click="$emit('set_new_selected_sessions_id', selectedSessions)"
    >
      Remplacer
      {{
        filter.selected_subscription_sessions_id_to_replace?.length > 1
          ? "les"
          : "la"
      }}
      {{ filter.selected_subscription_sessions_id_to_replace?.length }}
      seance{{
        filter.selected_subscription_sessions_id_to_replace?.length > 1
          ? "s"
          : ""
      }}
    </button>
    <button
      class="btn btn-danger ml-1"
      @click="$emit('cancel_selecte_sessions')"
    >
      Annuler la selection
    </button>
  </div>

  <div v-else-if="filter.calendar_for === 'pass_other'" class="py-2">
    <p>
      {{ length_selected_seance }} /
      {{ filter.number_of_sessions_remains_to_be_chosen }} séances selectionnées
    </p>

    <div class="row" style="min-height: 121px">
      <template
        v-for="selected_session_key in Object.keys(selectedSessions)"
        :key="selected_session_key"
      >
        <div class="col-md-4 col-sm-6 col-6" :id="selected_session_key">
          <item-selected-session
            :session="selectedSessions[selected_session_key]"
            @deselectSession="id => selectSession(selectedSessions[id])"
          />
          <template
            v-if="
              pass_type == 'trimester' &&
                session.title_date == 'started_on' &&
                realLengthSelectedSession_() == 1
            "
          >
          </template>
        </div>
      </template>
    </div>

    <button
      class="btn btn-success"
      :disabled="!length_selected_seance"
      @click="$emit('set_new_selected_sessions_id', selectedSessions)"
    >
      <i class="fa fa-disk"></i> Reserver
      {{ plural("le", length_selected_seance, "la") }}
      {{ length_selected_seance }}
      {{ plural("séance", length_selected_seance) }}
      {{ plural("selectionné", length_selected_seance) }}
    </button>

    <button
      class="btn btn-danger ml-1"
      @click="$emit('cancel_selecte_sessions')"
    >
      Annuler la selection
    </button>

    <div class="form-group">
      <label for="seence"> Centre : </label>
      <div>
        <Multiselect
          v-model="form.establishment_id"
          placeholder="--Centre--"
          :options="$page.props.establishments_list"
          label="name"
          trackBy="name"
          valueProp="id"
          :canDeselect="false"
          required
        />
      </div>
    </div>
  </div>
  <vue-cal
    v-if="filter.establishment_id || activitiesSessions"
    locale="fr"
    class="min-h-full vuecal--green-theme"
    click-to-navigate
    :disable-views="disableViews"
    active-view="year"
    show-week-numbers
    :today-button="today_button"
    :hide-view-selector="hide_view_selector"
    :hide-title-bar="hide_title_bar"
    events-count-on-year-view
    :disable-days="disableDays"
    :selected-date="selectedDate"
    :time-from="8 * 60"
    timeCellHeight="80"
    @ready="getMountedEvents($event)"
    :events="activitiesSessionsData"
    @view-change="
      calSync
        ? getSessions($event.startDate, $event.endDate, false, $event.view)
        : this.initTooltipe()
    "
    :on-event-click="selectSession"
  >
    <template v-slot:event="{ event }">
      <button
        class="btn-success btn btn-sm session-selected rounded-0"
        v-if="selectedSessions[event.id] != undefined"
      >
        <i class="fa fa-check"></i>
        selectionnée
      </button>
      <div
        class="w-100 planning-card--container h-100"
        :style="gatCardStyle(event)"
        :class="{
          'event-muted':
            filter.selected_subscription_sessions_id_to_replace &&
            length_selected_seance ==
              filter.selected_subscription_sessions_id_to_replace?.length &&
            selectedSessions[event.id] == undefined
        }"
      >
        <div
          v-if="
            !['customer', 'prospect'].includes(auth_user.role_name) &&
              event.absence_count
          "
          class="btn btn-danger absence_count"
          v-html="event.absence_count"
        />
        <div
          class="vuecal__event-title font-wieght-bold"
          v-html="event.activity"
        />
        <small class="vuecal__event-time">
          <span>{{ event.timestart }} - {{ event.timeend }}</span>
          <br />
        </small>
        <div
          v-if="
            filter.selected_subscription_sessions_id_to_replace == undefined &&
              filter.calendar_for !== 'renewal'
          "
          class="card-actions d-flex justify-content-around"
        >
          <template v-if="!hide_view_selector">
            <btn-activity-info />
            <btn-recuperation
              v-if="filter.activity_session_id_to_catch_up"
              @request_recuperation="$emit('request_recuperation', event)"
            />
            <template v-else>
              <btn-prevent-absence
                @prevent_absence="$emit('prevent_absence', event)"
              />
              <btn-present v-if="event.is_accomplished" />
              <btn-recuperation
                v-if="
                  event.presence.absence_prevention &&
                    event.presence.absence_prevention.queue &&
                    !event.presence.absence_prevention.queue
                      .recuperation_request
                "
                @request_recuperation="$emit('request_recuperation', event)"
              />
            </template>
          </template>
          <div v-else-if="auth_user.role_name == 'admin'">
            <div
              v-if="event.absence_count"
              class="btn btn-danger absence_count"
              v-html="event.absence_count"
            />
            <inertia-link
              :href="
                route('establishments.plannings.sessions.participants', {
                  establishment: event.establishment_id,
                  activity_session: event.id
                })
              "
              class="btn btn-sm btn-primary py-0 text-white"
              :class="{
                'no-participant': event.participants_count == 0,
                'should-make-presence':
                  event.TimeSpent &&
                  !event.presence_checked_at &&
                  event.participants_count > 0
              }"
              data-html="true"
              data-toggle="tooltip"
              :title="
                'Liste des participants ' +
                  event.participants_count +
                  '/' +
                  event.max_effective +
                  (event.TimeSpent &&
                  !event.presence_checked_at &&
                  event.participants_count > 0
                    ? '<br> <u>Vous devez effectué la presence</u>'
                    : '')
              "
            >
              <i class="fa fa-user-friends"></i>
              {{ event.max_effective - event.participants_pass_trimester_count }}R
            </inertia-link>

            <template v-if="!event.TimeSpent">
              <a
                class="btn btn-sm btn-warning py-0 ml-1"
                data-toggle="tooltip"
                title="Ajouter des participants"
                @click.prevent="AddParticipants(event)"
                ><i class="fa fa-user-tag"></i
              ></a>
              <a
                href="#"
                class="btn btn-sm btn-danger py-0 text-white ml-1"
                data-toggle="tooltip"
                title="Supprimer la séance"
                @click.prevent="deleteActivitySession(event.id)"
                ><i class="fa fa-trash"></i
              ></a>
            </template>
            <modal-add-participants
              :session="activeSessionForParticipantsAdd"
              @participantsAdded="refreshSession"
            />
          </div>
        </div>
      </div>
    </template>

    <template v-slot:no-event>Aucune activité</template>
  </vue-cal>
</template>
<script>
import BtnPreventAbsence from "@/Pages/Components/Planning/BtnPreventAbsence.vue";
import BtnPresent from "@/Pages/Components/Planning/BtnPresent.vue";
import BtnRecuperation from "@/Pages/Components/Planning/BtnRecuperation.vue";
import BtnActivityInfo from "@/Pages/Components/Planning/BtnActivityInfo.vue";
import ModalAddParticipants from "@/Pages/Components/Planning/ModalAddParticipants.vue";
import itemSelectedSession from "@/Pages/Admin/Subscription/Form/itemSelectedSession.vue";

export default {
  name: "PlanningCalendar",
  components: {
    BtnPreventAbsence,
    BtnPresent,
    BtnRecuperation,
    BtnActivityInfo,
    ModalAddParticipants,
    itemSelectedSession
  },
  props: {
    activitiesSessions: {
      type: Array,
      default: null
    },
    initSelectedSessions: {
      type: Array,
      default: null
    },
    calSync: {
      default: false
    },
    filter: {
      default: {}
    },
    disableViews: {
      default: ["years", "year", "day"]
    },
    selected_date: {
      default: null
    },
    today_button: {
      default: true
    },
    hide_view_selector: {
      default: false
    },
    hide_title_bar: {
      default: false
    },
    show_passed_date: {
      default: false
    },
    selected_subscription_sessions_id: {
      default: []
    },
    pass_type: {
      default: null
    }
  },
  data: function() {
    return {
      activitiesSessionsData: [],
      selectedDate: this.selected_date ? this.selected_date : new Date(),
      lastDateStart: null,
      lastDateEnd: null,
      disableDays: [],
      last_date_start: null,
      last_date_end: null,
      activeSessionForParticipantsAdd: {},
      selectedSessions: {},
      form: {
        establishment_id: null
      },
      last_attrib_renewal_session: null,
      lastRequestController: null
    };
  },
  watch: {
    activitiesSessions: {
      deep: true,
      handler() {
        this.activitiesSessionsData = this.activitiesSessions;
        this.initTooltipe();
      }
    },
    initSelectedSessions: {
      deep: true,
      handler() {
        if (
          this.initSelectedSessions?.length ||
          (!this.initSelectedSessions?.length &&
            Object.keys(this.selectedSessions).length)
        ) {
          let selectedSessions_ = {};
          for (let session of this.initSelectedSessions) {
            selectedSessions_[session.id] = session;
          }
          this.selectedSessions = selectedSessions_;
          this.initTooltipe();
        }
      }
    },
    "filter.establishment_id": {
      deep: true,
      handler() {
        if (
          (this.last_date_end && this.hide_view_selector) ||
          (this.filter.calendar_for === "renewal" && this.last_date_end)
        ) {
          setTimeout(() => {
            this.getSessions(
              this.last_date_start,
              this.last_date_end,
              true,
              "day"
            );
          }, 100);
        }
      }
    },
    "form.establishment_id": {
      deep: true,
      handler() {
        setTimeout(() => {
          if (this.last_date_end) {
            this.getSessions(
              this.last_date_start,
              this.last_date_end,
              true,
              "day"
            );
          }
        }, 100);
      }
    }
  },
  beforeMount() {
    if (this.calSync !== true) {
      this.activitiesSessionsData = this.activitiesSessions;
      this.initTooltipe();
    } else if (this.filter.calendar_for === "pass_other") {
      this.form.establishment_id = this.filter.establishment_id;
    }

    if (this.initSelectedSessions?.length) {
      for (let session of this.initSelectedSessions) {
        this.selectedSessions[session.id] = session;
      }
    }
  },
  computed: {
    length_selected_seance() {
      return Object.keys(this.selectedSessions).length;
    }
  },
  methods: {
    getMountedEvents(event) {
      if (this.calSync) {
        var view = this.filter.view ? this.filter.view : null;
        this.getSessions(
          event.last_date_start,
          event.last_date_end,
          true,
          view
        );
      }
    },
    gatCardStyle(card) {
      return {
        "background-color": card.bgcolor ? card.bgcolor : "#2BAC96",
        color: card.font_color ? card.font_color : "#fff"
      };
    },
    async getSessions(start, end, isReady = false, view = null) {
      await this.cancelRequest();
      this.lastRequestController = new AbortController();

      if (
        this.initSelectedSessions &&
        this.last_attrib_renewal_session &&
        this.last_attrib_renewal_session.diff(this.$moment(), "seconds") < 1
      ) {
        this.last_attrib_renewal_session = null;
        return;
      }

      if (this.filter) {
        var params = {
          establishment_id: this.form.establishment_id
            ? this.form.establishment_id
            : this.filter.establishment_id,
          not_session_user_id: this.filter.user_id,
          start: this.dateAng(start),
          end: this.dateAng(end),
          minDate: this.show_passed_date ? null : this.dateAng(new Date()),
          maxDate: this.filter.max_date
            ? this.dateAng(this.filter.max_date)
            : null,
          pass_id: this.filter.pass_id,
          expire_date: this.filter.expire_date,
          view: view,
          season_id: this.filter.season_id,
          selected_subscription_sessions_id_to_replace: this.filter
            .selected_subscription_sessions_id_to_replace
        };

        if (
          isReady &&
          (this.filter?.selected_subscription_sessions_id_to_replace?.length ||
            this.filter.calendar_for === "pass_other")
        ) {
          params.end = this.$moment()
            .add(3, "months")
            .format("YYYY-MM-DD");
        }

        if (this.filter.calendar_for == "renewal") {
          params.num_trimester = this.filter.num_trimester;
          if (isReady) {
            params.end = this.$moment()
              .add(3, "months")
              .format("YYYY-MM-DD");
          }
        }

        this.last_date_start = params.start;
        this.last_date_end = params.end;

        if (this.filter.activity_session_id_to_catch_up) {
          params.activity_session_id_to_catch_up = this.filter.activity_session_id_to_catch_up;
          params.not_session_user_id = this.filter.user_id;
        }

        if (this.filter.selected_subscription_sessions_id_to_replace?.length) {
          params.not_session_user_id = this.filter.user_id;
        }
        
        axios
          .get(route("api.plannings.sessions"), {
            params: params,
            signal: this.lastRequestController.signal
          })
          .then(response => {
            if (response.data.data != undefined) {
              this.activitiesSessionsData = cpp(response.data.data);
              this.initTooltipe();
            } else {
              this.activitiesSessionsData = [];
            }
          });
      }
    },
    async cancelRequest() {
      if (this.lastRequestController) {
        await this.lastRequestController.abort();
      }
    },
    AddParticipants(session) {
      this.activeSessionForParticipantsAdd = session;
      $("#add-participants").modal();
    },
    deleteActivitySession(id, index) {
      this.$inertia.delete(
        route("establishments.plannings.sessions.destroy", {
          establishment: this.filter.establishment_id,
          session: id
        }),
        {
          onBefore: () => confirm("Supprimer la séance?")
        }
      );
    },
    selectSession(session) {
      if (this.selectedSessions[session.id]) {
        delete this.selectedSessions[session.id];
      } else if (
        this.filter.selected_subscription_sessions_id_to_replace?.length >
          Object.keys(this.selectedSessions).length ||
        (this.filter.calendar_for === "renewal" &&
          Object.keys(this.selectedSessions).length <
            this.$page.props.max_session_for_renewal) ||
        (this.filter.calendar_for === "pass_other" &&
          Object.keys(this.selectedSessions).length <
            this.filter.number_of_sessions_remains_to_be_chosen)
      ) {
        this.selectedSessions[session.id] = session;
      } else if (
        this.selected_subscription_sessions_id?.length ||
        this.filter.number_of_sessions_remains_to_be_chosen
      ) {
        toastr.info(
          "Vous ne pouvez pas sélectionner plus de " +
            (this.filter.number_of_sessions_remains_to_be_chosen
              ? this.filter.number_of_sessions_remains_to_be_chosen
              : this.filter.selected_subscription_sessions_id_to_replace
                  ?.length) +
            " seances"
        );
      }

      if (this.filter.calendar_for === "renewal") {
        let session_ = this.selectedSessions;
        this.$emit("attrib-plannings", session_);
        this.last_attrib_renewal_session = this.$moment();
      }
    }
  }
};
</script>
<style lang="scss" scoped>
.btn-planning {
  color: white;
  border-radius: 50px;
  height: 32px;
  width: 32px;
  font-size: 6px;
  vertical-align: middle;
  padding: 8px 4px;
  text-align: center;
  border: 1px solid white;
}

.vuecal__body {
  min-height: 50vh;
}

.session-activity {
  .no-participant,
  .no-coach {
    &:not(:hover) {
      opacity: 0.5;
    }
  }
  .should-make-presence {
    border: rgb(241, 50, 24) 1px solid;
  }
  .absence_count {
    height: 25px;
    width: 25px;
    position: absolute;
    right: 5px;
    top: 5px;
    font-weight: 500;
    opacity: 0.9 !important;
    padding: 0px !important;
    text-align: center;
    font-size: 12px;
    &:hover {
      opacity: 1 !important;
      cursor: inherit;
    }
  }
  .seson-selected {
    position: absolute;
    opacity: 0.9;
    z-index: 10;
    right: 0;
    bottom: 0;
  }
}
</style>
