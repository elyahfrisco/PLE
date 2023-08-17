<template>
  <app-layout>
    <template #pageTitle>Absences prévenues</template>
    <button @click="showModalPreventAbsence" class="btn btn-prevent-absence">
      Prévenir une absence
    </button>
    <div class="app-my-planning">
      <div class="planning-container">
        <div class="row">
          <div class="upcomming-seance col-lg-4 col-md-6">
            <h2>
              Prochaine séance
              {{ upcommingSeance ? upcommingSeance.elapseTime : "( aucune )" }}
            </h2>
            <div class="upcomming-seance--content">
              <PlanningCard
                v-if="upcommingSeance"
                :planning="upcommingSeance"
                @prevent_absence="showModalPreventAbsence(upcommingSeance)"
              />
            </div>
          </div>
          <div
            class="planning--filters-container col-lg-8 col-md-6 order-md-first"
          >
            <PlanningFilter v-on:filterchange="onFilterChange" />
            <hr />
            <button class="btn btn-primary mb-3" @click="switchViewMode()">
              <span v-if="viewMode == 'list'">
                <i class="fa fa-calendar mr-2"></i>
                Afficher le calendrier de mes sceance
              </span>
              <span v-else>
                <i class="fa fa-list mr-2"></i>
                Afficher la liste de mes sceance
              </span>
            </button>
          </div>
        </div>
        <div class="planning--list" v-if="viewMode == 'list'">
          <div class="row">
            <div
              class="col-sm-4 col-lg-3 card-container"
              v-for="(planning, index) in planningsData"
              :key="'planning--' + index"
            >
              <PlanningCard
                :planning="planning"
                @prevent_absence="showModalPreventAbsence(planning)"
                @request_recuperation="requestRecuperation(planning)"
              />
            </div>
          </div>
        </div>

        <div class="planning--calendar" v-else>
          <Calendar
            :activitiesSessions="planningsData"
            @prevent_absence="showModalPreventAbsence"
            @request_recuperation="requestRecuperation"
          />
        </div>

        <div class="planning--paginator-container" v-if="viewMode == 'list'">
          <PlannigPaginator :data="paginatorData" v-on:goto="goTo" />
        </div>

        <modal-request-recuperation
          v-if="activeSessionForRequestRecuperation"
          :sessionInQueue="activeSessionForRequestRecuperation"
        />
      </div>
    </div>
    <modal-prevent-absence :selectedSeance="selectedSeance" />
  </app-layout>
</template>

<script>
import PlanningCard from "@/Pages/Components/PlanningCard.vue";
import PlannigPaginator from "./Paginator.vue";
import PlanningFilter from "./Filter.vue";
import Calendar from "@/Pages/Components/Calendar.vue";
import modalPreventAbsence from "@/Pages/Components/modalPreventAbsence.vue";
import ModalRequestRecuperation from "@/Pages/Components/ModalRequestRecuperation.vue";

export default {
  props: ["plannings"],
  components: {
    PlanningCard,
    PlannigPaginator,
    PlanningFilter,
    Calendar,
    modalPreventAbsence,
    ModalRequestRecuperation
  },
  data: function() {
    return {
      planningsData: [],
      planningsParams: {
        user_id: null,
        absence_notified: true,
        per_page: 12,
        paged: true,
        page: 1,
        subscribed_pass: true,
        pass_id: null
      },
      upcommingSeance: false,
      paginatorData: null,
      viewMode: "list",
      selectedSeance: null,
      activeSessionForRequestRecuperation: null
    };
  },
  methods: {
    showModalPreventAbsence(planing = null) {
      this.selectedSeance = planing;
      $("#modal-prevent-absence").modal();
    },
    getUpcommingSeance() {
      let that = this;
      let params = { ...that.planningsParams };
      delete params.absence_notified;
      params.per_page = 1;
      params.minDate = new Date();
      params.without_prevention = true;

      axios
        .get(route("api.plannings.sessions"), {
          params
        })
        .then(response => {
          if (response.data.data != undefined) {
            that.upcommingSeance = response.data.data[0];
            this.initTooltipe();
          } else {
            that.planningsData = [];
          }
        });
    },
    getPlannings() {
      let that = this;
      axios
        .get(route("api.plannings.sessions"), {
          params: that.planningsParams
        })
        .then(response => {
          if (response.data.data != undefined) {
            that.planningsData = response.data.data;
          } else {
            that.planningsData = [];
          }
          that.paginatorData = response.data.meta;
          this.initTooltipe();
        });
    },
    onFilterChange(data) {
      this.planningsParams.activity_id = data.activity;
      this.planningsParams.minDate = data.minDate;
      this.planningsParams.maxDate = data.maxDate;
      this.planningsParams.pass_id = data.pass;
      this.getPlannings();
    },
    /*** pagination method */
    goTo(page) {
      this.planningsParams.page = page;
      this.getPlannings();
    },
    switchViewMode() {
      this.viewMode = this.viewMode == "list" ? "calendar" : "list";
    },

    requestRecuperation(data) {
      this.activeSessionForRequestRecuperation = data;
      setTimeout(() => {
        $("#modal-request-recuperation").modal();
      }, 100);
    }
  },
  mounted: function() {
    this.planningsParams.user_id = this.auth_user.id;
    this.getPlannings();
    this.getUpcommingSeance();
  }
};
</script>

<style lang="scss" scoped>
@import "@/Pages/Components/main.scss";

.btn-prevent-absence {
  background-color: $color-orange;
  color: white;
}

.app-my-planning {
  position: relative;
}

.planning--filters-container {
  min-height: 12rem;
}</style
>>
