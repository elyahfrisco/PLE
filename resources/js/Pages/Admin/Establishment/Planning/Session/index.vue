<template>
  <app-layout>
    <template #pageTitle
      >Planning
      <template v-if="!establishment.relaxation_center"> centre </template>
      {{ establishment.name }}
    </template>
    <div class="row mb-2">
      <inertia-link
        :href="
          route('establishments.plannings.create', {
            establishment: establishment.id,
            season_id: filtersValue.season_id
          })
        "
        class="mr-2 btn btn-success rounded-0"
        ><i class="fa fa-plus"></i> Nouveau planning</inertia-link
      >
    </div>

    <Filter
      @refresh-view="refreshView"
      @init-filter="
        e => {
          filtersValue = e;
        }
      "
      :seasons="seasons"
      :activities="activities"
      :establishment="establishment"
      v-if="seasons && activities"
    />

    <div class="row">
      <vue-cal
        ref="session_calendar"
        locale="fr"
        class="min-h-full vuecal--green-theme"
        :class="{
          'is-load': Object.keys(sessionLoad).length != 0
        }"
        click-to-navigate
        v-model:active-view.sync="activeView"
        :disable-views="['years', 'year']"
        show-week-numbers
        today-button
        events-count-on-year-view
        :time-from="8 * 60"
        timeCellHeight="100"
        :events="activitiesSessions"
        v-model:selected-date="selectedDate"
        :disable-days="disableDays"
        @ready="getMountedEvents($event)"
        @view-change="
          getSession($event.startDate, $event.endDate, false, $event.view)
        "
      >
        <template v-slot:event="{ event }" class="ps">
          <PlanningEvent
            @refreshSession="refreshSession"
            :event="event"
            :toHistory="toHistory"
          />
        </template>
        <template v-slot:no-event>Aucune activité</template>
      </vue-cal>
    </div>
    <div v-html="style"></div>
  </app-layout>
</template>

<script>
import Filter from "@/Pages/Components/Planning/Filters/filter.vue";
import PlanningEvent from "./PlanningEvent.vue";

export default {
  props: ["establishment"],
  components: {
    Filter,
    PlanningEvent
  },
  data() {
    return {
      activitiesSessions: [],
      selectedDate: new Date(),
      activeView: "week",
      style: "",
      lastDateStart: null,
      lastDateEnd: null,
      disableDays: [],
      lastView: null,
      filtersValue: {},
      sessionLoad: {},
      toHistory: null,
      lastRequestController: null
    };
  },
  mounted() {
    this.getEstablishmentSeasons();
    this.getEstablishmentActivities();
    this.getActivitiesStyles();
    this.getDisableDays();

    this.toHistory = window.location.href;

    this.verifyCalendar();

    this.$emitter.on("cancelGetPlanningSessions", this.cancelRequest);
  },
  methods: {
    getMountedEvents(event) {
      var init = true;

      this.lastDateStart = event.startDate;
      this.lastDateEnd = event.endDate;

      if (this.q_.view) {
        this.activeView = this.q_.view;
        this.lastView = this.q_.view;
        init = false;
      }
      if (this.q_.start) {
        this.lastDateStart = this.q_.start;
        if (this.q_.end) {
          this.lastDateEnd = this.q_.end;
        }
        this.selectedDate = this.dateAng(this.q_.start);
        init = false;
      }

      // setTimeout(() => {
      //   this.getSession(this.lastDateStart, this.lastDateEnd);
      // }, 200);
    },
    refreshSession() {
      this.getSession(
        this.lastDateStart,
        this.lastDateEnd,
        false,
        this.lastView
      );
    },
    async getSession(start, end, isReady = false, view = null) {
      await this.cancelRequest();
      this.lastRequestController = new AbortController();

      if (start) {
        this.lastDateStart = start;
        this.lastDateEnd = end;
        this.lastView = view;
      }

      var params = {
        start: this.lastDateStart,
        end: this.lastDateEnd,
        view: view,
        with_coachs: true
      };

      if (this.filtersValue) {
        /** ignore request if season_id is empty */
        if (!this.filtersValue.season_id) return;

        params = { ...params, ...this.filtersValue };
      }

      var url_ = route(route().current(), {
        ...route().params,
        _query: this.filterEmpty(params)
      });

      this.setUrl(url_);

      this.toHistory = url_;

      params.without_subscription_activity = true;

      var loadKey = new Date().getUTCMilliseconds();
      this.sessionLoad[loadKey] = true;

      axios
        .get(
          route("api.establishments.plannings.sessions", {
            establishment: this.establishment.id
          }),
          {
            params: params,
            signal: this.lastRequestController.signal
          }
        )
        .then(response => {
          this.activitiesSessions = response.data;
          if (isReady && this.activitiesSessions[0] == undefined) {
            axios
              .get(
                route("api.establishments.plannings.sessions", {
                  establishment: this.establishment.id,
                  view: "week",
                  with_coachs: true,
                  ...this.filtersValue
                })
              )
              .then(response => {
                this.activitiesSessions = cpp(response.data);
                if (this.activitiesSessions[0]) {
                  this.selectedDate = this.activitiesSessions[0].weekStart;
                }
                this.initTooltipe();
                this.setClassOverlappedEventInCalendar();
              });
          } else {
            this.initTooltipe();
            this.setClassOverlappedEventInCalendar();
          }
          delete this.sessionLoad[loadKey];
        })
        .catch(() => delete this.sessionLoad[loadKey]);
    },
    async cancelRequest() {
      if (this.lastRequestController) {
        await this.lastRequestController.abort();
      }
    },
    getDisableDays() {
      axios
        .get(
          route("api.plannings.dates.disabled", {
            establishment_id: this.establishment.id
          })
        )
        .then(response => {
          this.DisableDays = response.data;
        });
    },
    setAccomplished(id) {
      this.$inertia.put(
        route("establishments.plannings.sessions.isaccompished", {
          establishment: this.establishment.id,
          session_id: id
        }),
        {},
        {
          onBefore: () => confirm("Confimer la modification?"),
          onSuccess: () => {
            var index = this.activitiesSessions.findIndex(x => x.id == id);
            this.activitiesSessions[index].accomplished = 1;
          }
        }
      );
    },
    refreshView: _.debounce(function(v) {
      if (!isNaN(v.num_trimester)) {
        this.filtersValue = v;

        if (v.miDate) this.selectedDate = v.miDate;
        if (
          this.activeView == "year" ||
          !(
            this.filtersValue.participant_min === null &&
            isNaN(this.filtersValue.participant_min) &&
            this.filtersValue.participant_max === null &&
            isNaN(this.filtersValue.participant_max)
          )
        ) {
          setTimeout(() => {
            this.getSession(
              this.lastDateStart,
              this.lastDateEnd,
              this.lastView
            );
          }, 200);
        } else {
          this.activeView = "year";
        }
      } else {
        this.filtersValue = v;
        this.getSession(this.lastDateStart, this.lastDateEnd, this.lastView);
      }
    }, 500),
    getEstablishmentSeasons() {
      axios
        .get(
          route("api.establishments.seasons", {
            establishment: this.establishment.id
          })
        )
        .then(response => {
          this.seasons = cpp(response.data);
        });
    },
    getEstablishmentActivities() {
      axios
        .get(
          route("api.establishments.activities", {
            establishment: this.establishment.id
          })
        )
        .then(response => {
          this.activities = cpp(response.data);
        });
    },
    getActivitiesStyles() {
      axios.get(route("activities.style")).then(response => {
        this.style = "<style >" + response.data + "</style>";
      });
    },
    verifyCalendar() {
      setTimeout(() => {
        if ($("#select_season")[0] == undefined) {
          if (!Object.keys(this.sessionLoad).length)
            this.simulateCalendarClick();
        }
      }, 4000);
    }
  }
};
</script>

<style scoped lang="scss">
.vuecal__body {
  min-height: 50vh;
}

.session-activity {
  padding: auto 50px;
  /* min-height: 60px !important; */
}
</style>
