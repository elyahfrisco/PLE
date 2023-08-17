<template>
  <div class="row mx-0">
    <div class="" v-if="pass_type == 'other' || pass_type == 'decouvert'">
      <p>
        Vous pouvez choisir {{ pass.number_sessions }} séances entre :
        <span
          class="mr-1"
          v-for="(activity, i) in pass.activities"
          :key="activity.id"
          ><u>{{ activity.name }}</u
          ><i v-if="pass.activities.length - 2 == i"> et </i
          ><i v-else-if="pass.activities.length - 1 != i">,</i></span
        >
        . Ou au moins 1 séance, et pour les restes vous pouvez prendre
        rendez-vous au fur et à mesure
      </p>
    </div>
  </div>
  <div class="row">
    <div class="col-lg-2 col-12 py-2" id="sessions_selected">
      <h5 class="font-weight-bold">Séances selectionnées</h5>
      <div class="row">
        <template v-for="(session, i) in selectedSessions" :key="i">
          <div
            class="col-md-12 col-sm-6 col-6"
            v-if="session != undefined"
            :id="i"
          >
            <template v-if="pass_type == 'trimester'">
              <label v-if="idSelectedSessions[0] == session.id"
                >Date de début</label
              >
              <label v-else>Date de fin</label>
            </template>

            <item-selected-session
              :session="session"
              :pass_type="pass_type"
              @deselectSession="deselectSession"
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
      <div
        class="row"
        v-if="pass_type == 'trimester' && this.realLengthSelectedSession == 0"
      >
        <div class="col-md-12">
          <div
            class="
              flat
              alert
              bg-info
              alert-info
              animate__animated animate__flash
            "
          >
            Sélectionnez la date de debut de votre séance
          </div>
        </div>
      </div>
      <div
        class="row"
        v-if="pass_type == 'trimester' && this.realLengthSelectedSession == 1"
      >
        <div class="col-md-12">
          <div
            class="
              flat
              alert
              bg-danger
              alert-danger
              animate__animated animate__pulse animate__infinite animate__slow
            "
          >
            Sélectionnez la date de fin de votre séance de
            <strong>{{ selectedSessions[idSelectedSessions[0]].title }}</strong>
          </div>
        </div>
      </div>
      <div class="row">
        <button
          class="btn btn-success btn-sm mx-auto"
          @click="setSubscriptionPassInfo"
          :disabled="statBtnSetSubscriptionInfo"
        >
          Sélectionner
        </button>
      </div>
    </div>
    <div class="col-lg-10 col-12">
      <vue-cal
        locale="fr"
        class="min-h-full vuecal--green-theme"
        :class="{
          'is-load': Object.keys(sessionLoad).length != 0
        }"
        click-to-navigate
        v-model:active-view.sync="activeView"
        today-button
        :disable-views="['years', 'year']"
        events-count-on-year-view
        :time-from="8 * 60"
        timeCellHeight="60"
        :events="activitiesSessions"
        :selected-date="selectedDate"
        @view-change="
          getSessions($event.startDate, $event.endDate, false, $event.view)
        "
        :on-event-click="getSessionInfo"
      >
        <template v-slot:event="{ event }">
          <SubscriptionSessionCalendarEvent
            :event="event"
            :selectedSessions="selectedSessions"
            :pass_type="pass_type"
          />
        </template>
        <template v-slot:no-event>Aucune activité</template>
      </vue-cal>
    </div>
  </div>
  <p v-html="style"></p>
</template>

<script>
import itemSelectedSession from "./itemSelectedSession.vue";
import SubscriptionSessionCalendarEvent from "./SubscriptionSessionCalendarEvent.vue";

export default {
  props: [
    "establishment",
    "season",
    "pass",
    "filter",
    "user_id",
    "cartIdSelectedSessions",
    "reduction"
  ],
  components: {
    itemSelectedSession,
    SubscriptionSessionCalendarEvent
  },
  watch: {
    filter: {
      deep: true,
      handler() {
        setTimeout(() => {
          if (!isNaN(this.filter.pass_id)) {
            this.pass_type = this.filter.pass_type;
            this.selectedSessions = {};
            this.only_planning_id = null;
            this.getSessions(
              this.$moment()
                .startOf("month")
                .format("YYYY-MM-DD"),
              this.$moment()
                .endOf("month")
                .format("YYYY-MM-DD"),
              true,
              "week"
            );
          }
        }, 200);
      }
    },
    season: {
      deep: true,
      handler() {
        if (this.season.id) {
          this.selectedDate = new Date(this.dateAng(this.season.date_start));
        }
      }
    }
  },
  data() {
    return {
      activitiesSessions: [],
      selectedDate: new Date(),
      selectedSessions: {},
      idSelectedSessions: [],
      pass_type: null,
      style: "",
      only_planning_id: null,
      activeView: "month",
      sessionLoad: {},
      lastRequestController: null
    };
  },
  computed: {
    realLengthSelectedSession() {
      var length = 0;
      this.idSelectedSessions = Object.keys(this.selectedSessions);
      var length = this.idSelectedSessions.length;

      // this.idSelectedSessions = [];
      // for (var a of this.selectedSessions) {
      //   if (a != undefined) {
      //     length += 1;
      //     this.idSelectedSessions.push(a.id);
      //     if (this.pass_type == "trimester") {
      //       if (length == 1) {
      //         if (this.selectedSessions[a.id] != undefined) {
      //           this.selectedSessions[a.id].title_date = "started_on";
      //         }
      //       } else if (length == 2) {
      //         this.selectedSessions[a.id].title_date = "finished_on";
      //       }
      //     }
      //   }
      // }

      return length;
    },
    statBtnSetSubscriptionInfo() {
      if (this.pass_type == "trimester" && this.realLengthSelectedSession > 0) {
        return false;
      } else if (
        this.pass_type == "one_session" &&
        this.realLengthSelectedSession > 0
      ) {
        return false;
      } else if (
        (this.pass_type == "other" || this.pass_type == "decouvert") &&
        this.realLengthSelectedSession >= 1
      ) {
        return false;
      }
      return true;
    }
  },
  methods: {
    async getSessions(start, end, isReady = false, view = null) {
      await this.cancelRequest();
      this.lastRequestController = new AbortController();

      if (this.filter.pass_id) {
        var not_session_id = this.cartIdSelectedSessions;
        this.pass_type = this.filter.pass_type;

        var params = {
          establishment_id: this.establishment.id,
          not_session_user_id: this.user_id,
          not_session_id: not_session_id,
          start: start,
          end: end,
          minDate: this.dateAng(new Date()),
          maxDate: this.dateAng(this.season.date_end),
          pass_id: this.filter.pass_id,
          with_price: true,
          view: view,
          pass_type: this.pass_type,
          season_id: this.filter.season_id,
          type_of_fees: this.filter.type_of_fees,
          reduction: this.reduction
        };

        if (this.filter.num_trimester) {
          params.num_trimester = this.filter.num_trimester;
        }

        if (this.only_planning_id) {
          params.only_planning_id = this.only_planning_id;
        }

        var loadKey = new Date().getUTCMilliseconds();
        this.sessionLoad[loadKey] = true;

        axios
          .get(route("api.plannings.sessions"), {
            params: params,
            signal: this.lastRequestController.signal
          })
          .then(response => {
            if (response.data.data != undefined) {
              this.activitiesSessions = cpp(response.data.data);
              this.initTooltipe();
            } else {
              this.activitiesSessions = [];
            }
            delete this.sessionLoad[loadKey];
          })
          .catch(() => delete this.sessionLoad[loadKey]);
      }
    },
    async cancelRequest() {
      if (this.lastRequestController) {
        await this.lastRequestController.abort();
      }
    },
    setSubscriptionPassInfo() {
      this.selectedSessions = oToA(this.selectedSessions);
      if (this.pass_type == "trimester") {
        var params = {
          establishment_id: this.establishment.id,
          min_id: this.selectedSessions[0].id,
          max_id: this.selectedSessions[1]
            ? this.selectedSessions[1].id
            : this.selectedSessions[0].id,
          activity_id: this.selectedSessions[0].activity_id,
          not_session_user_id: this.user_id,
          with_price: true,
          pass_type: this.pass_type,
          reduction: this.reduction
        };

        if (this.filter.num_trimester) {
          params.num_trimester = this.filter.num_trimester;
          params.season_id = this.filter.season_id;
        }

        axios
          .get(route("api.plannings.sessions"), {
            params: params
          })
          .then(response => {
            this.selectedSessions = cpp(response.data.data);
            this.$emit("setSubscriptionPassInfo", {
              sessions: this.selectedSessions,
              id_: this.filter.id_
            });
            $("#planningCalendar").modal("hide");
          });
      } else {
        this.$emit("setSubscriptionPassInfo", {
          sessions: this.selectedSessions,
          id_: this.filter.id_
        });
        $("#planningCalendar").modal("hide");
      }
    },
    getSessionInfo(event) {
      if (this.pass_type == "trimester") {
        if (this.hasSession(event)) {
          this.deselectSession(event.id);
        } else {
          if (this.realLengthSelectedSession < 2) {
            if (
              this.realLengthSelectedSession == 1 &&
              this.selectedSessions[this.idSelectedSessions[0]].activity_id !=
                event.activity_id
            ) {
              this.deselectSession(
                this.selectedSessions[this.idSelectedSessions[0]].id
              );
            }

            this.selectSession(event);

            if (this.realLengthSelectedSession == 1) {
              this.only_planning_id = this.selectedSessions[
                this.idSelectedSessions[0]
              ].form.planning_id;
              this.activeView = "year";
              this.infoCenter(
                "Sélectionnez la date de fin de votre séance de " +
                  this.selectedSessions[this.idSelectedSessions[0]].title
              );
            }
          } else if (this.realLengthSelectedSession == 2) {
            if (
              this.selectedSessions[this.idSelectedSessions[0]].activity_id !=
              event.activity_id
            ) {
              this.selectedSessions = [];
              this.selectSession(event);
            } else if (
              this.selectedSessions[this.idSelectedSessions[1]].activity_id ==
                event.activity_id &&
              event.id > this.selectedSessions[this.idSelectedSessions[1]].id
            ) {
              this.deselectSession(
                this.selectedSessions[this.idSelectedSessions[1]].id
              );
              this.selectSession(event);
            }
          }
        }
      } else if (this.pass_type == "one_session") {
        if (this.hasSession(event)) {
          this.deselectSession(event.id);
        } else {
          this.selectSession(event);
        }
      } else if (this.pass_type == "other" || this.pass_type == "decouvert") {
        if (this.hasSession(event)) {
          this.deselectSession(event.id);
        } else {
          if (this.realLengthSelectedSession < this.pass.number_sessions) {
            this.selectSession(event);
          } else {
            this.infoCenter(
              "Vous ne pouvez pas choisir plus de " +
                this.pass.number_sessions +
                " séances"
            );
          }
        }
      }
    },
    hasSession(session) {
      return this.selectedSessions[session.id] != undefined;
    },
    selectSession(session) {
      if (Object.keys(this.sessionLoad).length == 0) {
        this.selectedSessions[session.id] = cpp(session);
      }
    },
    deselectSession(id) {
      delete this.selectedSessions[id];
      setTimeout(() => {
        if (
          this.pass_type == "trimester" &&
          this.realLengthSelectedSession == 0 &&
          this.only_planning_id != null
        ) {
          this.only_planning_id = null;
        }
      }, 100);
    },

    realLengthSelectedSession_() {
      var length = 0;
      for (var a of this.selectedSessions) {
        if (a != undefined) {
          length += 1;
        }
      }
      return length;
    }
  },
  mounted() {
    axios.get(route("activities.style")).then(response => {
      this.style = "<style>" + response.data + "</style>";
    });
  }
};
</script>

<style soped lang="scss">
#sessions_selected {
  background-color: #ececec;
}

.timeInterval {
  font-size: 95%;
}

.vuecal__cell-date {
  padding: 10px;
}

.session-selected {
  position: absolute;
  opacity: 0.9;
  z-index: 5;
  right: 0;
}

.modal-xl {
  max-width: 95%;
}

.session-activity {
  & > .ps {
    min-width: 100%;
    position: relative;
  }
  .activity-price {
    position: absolute;
    top: -12px;
    left: 38.5%;
    min-height: 36px;
    min-width: 36px;
    padding: 12px 0px 4px 0px;
    border-radius: 50%;
    border: 1px solid;
    z-index: 6;
    font-size: 90%;
    line-height: 15px;
    font-weight: bold;
    letter-spacing: 1.5px;
    opacity: 0.6;
    transition: 0.2s;
    :hover {
      transition: 0.2s;
      opacity: 1;
    }
  }
}

@media (max-width: 576px) {
  .modal-xl {
    max-width: 100%;
  }

  .modal {
    padding: 0;
  }

  .modal-dialog {
    margin: 0 !important;
  }
}
</style>
