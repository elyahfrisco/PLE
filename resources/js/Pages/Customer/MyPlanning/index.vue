<template>
  <app-layout>
    <template #pageTitle>Mon planning</template>
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
                :show_elapsetime="true"
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
      </div>
      <modalPreventAbsence
        :selectedSeance="selectedSeance"
      ></modalPreventAbsence>
      <modal-request-recuperation
        v-if="activeSessionForRequestRecuperation"
        :sessionInQueue="activeSessionForRequestRecuperation"
      />
    </div>
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
      DataPlans: [],
      planningsData: [],
      planningsParams: {
        user_id: null,
        per_page: 12,
        paged: false,
        page: 1,
        subscribed_pass: true,
        pass_id: null,
      },
      upcommingSeance: false,
      upcommingSeanceDiff: "",
      paginatorData: null,
      viewMode: "list",
      selectedSeance: null,
      activeSessionForRequestRecuperation: null
    };
  },

  methods: {
    showModalPreventAbsence(planing) {
      this.selectedSeance = planing;
      $("#modal-prevent-absence").modal();
    },
    getUpcommingSeance() {
      let that = this;
      let params = { ...that.planningsParams };
      params.per_page = 1;
      params.minDate = new Date();
      axios
        .get(route("api.plannings.sessions"), {
          params
        })
        .then(response => {
          if (response.data.data != undefined) {
            that.upcommingSeance = response.data.data[0];
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
            that.planningsData = this.getDataFilter(response.data.data);
          } else {
            that.planningsData = [];
          }
          that.paginatorData = response.data.meta;
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
    },

    getDataFilter(datas) {
      return datas.filter(item => {
        return (new Date(item.date) > new Date());
      })
    }
  },

  mounted: function() {
    this.planningsParams.user_id = this.auth_user.id;
    this.getPlannings();
    this.getUpcommingSeance();
  }
}
</script>

<style lang="scss" scoped>

</style>

