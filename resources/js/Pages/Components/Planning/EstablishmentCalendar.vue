<template>
  <div class="row">
    <vue-cal
      locale="fr"
      class="min-h-full vuecal--green-theme"
      :class="{
        'is-load': Object.keys(sessionLoad).length != 0
      }"
      click-to-navigate
      :disable-views="['years', 'year']"
      v-model:active-view.sync="activeView"
      show-week-numbers
      today-button
      events-count-on-year-view
      :time-from="8 * 60"
      timeCellHeight="100"
      :events="activitiesSessions"
      :selected-date="selectedDate"
      @ready="getMountedEvents($event)"
      @view-change="
        getSession($event.startDate, $event.endDate, false, $event.view)
      "
      :on-event-click="showDetail"
    >
      <template v-slot:event="{ event }">
        <EstablishmentCalendarEvent :event="event" />
      </template>

      <template v-slot:no-event>Aucune activité</template>
    </vue-cal>
  </div>
  <p v-html="style"></p>
</template>

<script>
import EstablishmentCalendarEvent from "./EstablishmentCalendarEvent.vue";
export default {
  props: ["establishment", "filters"],
  components: { EstablishmentCalendarEvent },
  data() {
    return {
      activitiesSessions: [],
      selectedDate: new Date(),
      activeView: "week",
      style: "",
      lastDateStart: null,
      lastDateEnd: null,
      sessionLoad: {}
    };
  },
  methods: {
    getMountedEvents(event) {
      var init = false;
      if (this.q_.view) {
        this.activeView = this.q_.view;
        init = false;
      }
      if (this.q_.start) {
        this.selectedDate = this.dateAng(this.q_.start);
        init = false;
      }
      // this.getSession(event.startDate, event.endDate, init);
    },
    getSession(start, end, isReady = false, view = "week") {
      this.lastDateStart = start;
      this.lastDateEnd = end;
      if (start == null) {
        return null;
      } else {
        var params = {
          start: start,
          end: end,
          auth_user_id: this.auth_user.id,
          ignore_when_trimester_filter: true,
          ...this.filters
        };
        var dynamic_url_params = { ...params };
        dynamic_url_params.view = view;
        delete dynamic_url_params.auth_user_id;
        delete dynamic_url_params.ignore_when_trimester_filter;
        this.setUrl(
          route(route().current(), {
            ...route().params,
            _query: this.filterEmpty(dynamic_url_params)
          })
        );
        var loadKey = new Date().getUTCMilliseconds();
        this.sessionLoad[loadKey] = true;
        axios
          .get(
            route("api.plannings.sessions", {
              establishment_id: this.establishment.id
            }),
            {
              params: params
            }
          )
          .then(response => {
            if (isReady && response.data.data[0] == undefined) {
              axios
                .get(
                  route("api.plannings.sessions", {
                    establishment_id: this.establishment.id
                  })
                )
                .then(response => {
                  this.activitiesSessions = response.data.data;
                  if (this.activitiesSessions[0]) {
                    this.selectedDate = this.activitiesSessions[0].weekStart;
                  }
                  this.initTooltipe();
                  this.setClassOverlappedEventInCalendar();
                });
            } else {
              this.activitiesSessions = response.data.data;
              if (
                this.activitiesSessions[0] &&
                this.filters &&
                !isNaN(this.filters.num_trimester)
              ) {
                this.selectedDate = this.activitiesSessions[0].weekStart;
              }
              this.initTooltipe();
              this.setClassOverlappedEventInCalendar();
              delete this.sessionLoad[loadKey];
            }
          });
      }
    },
    showDetail(event) {
      console.log(event.id);
    },
    setAccomplished(id) {
      if (confirm("Confimer la modification?")) {
        this.$inertia
          .put(
            route("establishments.plannings.sessions.isaccompished", {
              establishment: this.establishment.id,
              session_id: id
            })
          )
          .then(() => {
            var index = this.activitiesSessions.findIndex(x => x.id == id);
            this.activitiesSessions[index].accomplished = 1;
          });
      }
    }
  },
  mounted() {
    axios.get(route("activities.style")).then(response => {
      this.style = "<style>" + response.data + "</style>";
    });
  },
  watch: {
    filters: {
      immediate: true,
      deep: true,
      handler: _.debounce(function() {
        this.getSession(this.lastDateStart, this.lastDateEnd);
      }, 500)
    }
  }
};
</script>

<style scoped>
.vuecal__menu,
.vuecal__cell-events-count {
  background-color: var(--base);
  color: white;
}

.vuecal__title-bar {
  background-color: var(--base-light) !important;
}

.session-activity {
  overflow-y: auto;
}
</style>

<style scoped>
.vuecal__body {
  min-height: 50vh;
}

.session-activity {
  padding: auto 50px;
  /* min-height: 60px !important; */
}
</style>
