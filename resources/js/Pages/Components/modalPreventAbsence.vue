<template>
  <jet-modal
    id="modal-prevent-absence"
    maxWidth="lg"
    :fade="false"
    :title="
      user
        ? user.full_name
          ? 'Prévenir l\'absence de ' + user.full_name
          : 'Prévenir l\'absence'
        : 'Prévenir une absence'
    "
    @modal-closing="$emit('modal-closing')"
  >
    <div class="row">
      <div class="col-sm-7">
        <div class="col-sm-4"></div>
        <div class="form-group row">
          <label for="seence" class="col-sm-3"> Seance : </label>
          <div class="col-sm-9">
            <button class="btn btn-primary w-100" @click="showModalCalendar">
              selectionner sur le calendrier des séance
            </button>
            <div class="mt-2 planning-selected-for-absence-prevention">
              <template v-if="selectedPlanning">
                <button
                  class="btn btn-deselect-planning"
                  @click.prevent="deselectPlanning"
                  :style="{
                    color: selectedPlanning.font_color
                  }"
                >
                  <i class="fa fa-times-circle"></i>
                </button>
                <PlanningCard :planning="selectedPlanning" />
              </template>
              <div v-else class="alert alert-info text-center">
                Selectionner une séance
              </div>
            </div>
          </div>
        </div>
        <div class="form-group row mt-4">
          <label for="seence" class="col-sm-3">
            Date et heure de notification :
          </label>
          <div class="col-sm-9 row pr-0">
            <div class="col-8">
              <datepicker
                :upper-limit="new Date()"
                v-model="absence_prevent_date"
                class="form-control"
                :disabled="auth_user.role_name != 'admin'"
              />
            </div>
            <input
              type="time"
              class="form-control col-4"
              placeholder=""
              v-model="absence_prevent_time"
              :disabled="auth_user.role_name != 'admin'"
            />
          </div>
        </div>
        <div class="form-group row mt-4">
          <label for="seence" class="col-sm-3"> Motif : </label>
          <div class="col-sm-9">
            <Multiselect
              v-model="absence.motif"
              placeholder="--Motif de l'absence--"
              :options="list_motif"
              required
            />
          </div>
        </div>
        <div class="form-group row">
          <label for="seence" class="col-sm-3"> Description : </label>
          <div class="col-sm-9">
            <textarea
              class="form-control"
              rows="3"
              v-model="absence.reason"
              minlength="20"
            ></textarea>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-9 offset-md-3 text-center" id="btn-prevenir">
            <button
              class="btn btn-prevent-absence"
              @click.prevent="sauvegarde"
              :disabled="
                absence.activity_session_id == null || absence.motif == ''
              "
            >
              Prévenir l'absence
            </button>
          </div>
        </div>
      </div>
      <div class="col-sm-5 planning--list planning-in-form">
        <perfect-scrollbar>
          <div
            v-if="loadPlanning"
            class="skeleton skeleton-line w-100"
            style="--lines: 1; --l-h: 100px"
          ></div>

          <div
            class="col-sm-12 card-container"
            v-for="(planning, index) in planningsData"
            :key="'planning--' + index"
          >
            <PlanningCard
              :planning="planning"
              @prevent_absence="setSeance(planning)"
            />
          </div>
        </perfect-scrollbar>
      </div>
    </div>
  </jet-modal>
  <jet-modal
    id="choice-session-on-calendar"
    maxWidth="xl"
    :fade="false"
    title="calendrier"
    @modal-closed="toggleModalPreventAbsence()"
  >
    <Calendar
      :activitiesSessions="planningsData"
      @prevent_absence="setSeance"
    ></Calendar>
  </jet-modal>
</template>

<script>
import Calendar from "@/Pages/Components/Calendar.vue";
import PlanningCard from "@/Pages/Components/PlanningCard.vue";

export default {
  components: {
    Calendar,
    PlanningCard
  },
  props: ["planningData", "selectedSeance", "user", "subscription_id"],
  data() {
    return {
      calendarFilter: {},
      list_motif: [
        "Raison de santé",
        "Cause professionnelle",
        "Vacance",
        "Raision personelle"
      ],
      absence: {
        motif: "",
        reason: "",
        date: null,
        activity_session_id: null
      },
      absence_prevent_date: new Date(),
      absence_prevent_time: this.$moment().format("HH:mm"),
      selectedPlanning: null,
      loadPlanning: false,
      planningsData: [],
      planningsParams: {
        without_prevention: true,
        user_id: null,
        minDate: this.$moment().format("YYYY-MM-DD")
      }
    };
  },
  mounted() {
    if (this.user) {
      this.planningsParams.user_id =
        this.user != null && !isNaN(this.user) ? this.user : this.user.id;
      if (this.selectedSeance) {
        this.selectedPlanning = null;
        var params = {};
        params.id = this.selectedSeance.activity_session_id
          ? this.selectedSeance.activity_session_id
          : this.selectedSeance.id;
        axios
          .get(route("api.plannings.sessions"), {
            params
          })
          .then(response => {
            if (response.data.data != undefined) {
              this.selectedPlanning = response.data.data[0];
              this.absence.activity_session_id = this.selectedPlanning.id;
            } else {
              this.selectedPlanning = null;
            }
          });
      }
    } else if (this.auth_user) {
      this.planningsParams.user_id = this.auth_user.id;
    }

    this.getPlannings();
  },
  watch: {
    selectedSeance(value) {
      if (value.id != undefined) {
        setTimeout(() => {
          this.absence.activity_session_id = value.id;
        }, 200);
      }
    },
    user(value) {
      setTimeout(() => {
        if (this.user) {
          if (this.user) {
            this.planningsParams.user_id = this.user.id;
          }
          this.getPlannings();
        }
      }, 200);
    }
  },

  methods: {
    setSeance: function(planning) {
      this.absence.activity_session_id = planning.id;
      this.selectedPlanning = planning;
      this.hideModalCalendar();
      setTimeout(() => {
        $(".planning-selected-for-absence-prevention").slideDown(200);
      }, 100);
    },
    deselectPlanning() {
      this.absence.activity_session_id = null;
      $(".planning-selected-for-absence-prevention>.planning-card").slideUp(
        200
      );
      setTimeout(() => {
        this.selectedPlanning = null;
      }, 250);
    },
    sauvegarde: function() {
      let absence_ = { ...this.absence };
      if (this.auth_user.role_name == "admin") {
        absence_.date =
          this.$moment(this.absence_prevent_date).format("YYYY-MM-DD") +
          " " +
          this.absence_prevent_time;
      } else {
        absence_.date = this.$moment().format("YYYY-MM-DD HH:mm");
      }

      this.$inertia.post(
        route("absences.store", {
          user_id:
            this.user != null && !isNaN(this.user) ? this.user : this.user?.id,
          subscription_id: this.subscription_id
        }),
        absence_,
        {
          onSuccess: () => {
            if (this.user != null && !isNaN(this.user)) {
              $("#modal-prevent-absence").modal("hide");
              this.$emit("modal-closing");
              this.$emit("event1");
            } else {
              this.iReload();
            }
          }
        }
      );
    },
    getPlannings() {
      let that = this;
      if (this.user) {
        that.planningsData = [];
      }
      this.loadPlanning = true;
      console.log(that.planningsParams);
      axios
        .get(route("api.plannings.sessions"), {
          params: that.planningsParams
        })
        .then(response => {
          if (response.data.data != undefined) {
            that.planningsData = response.data.data;
            // console.log(that.planningsData);
            if (this.user) {
              // this.selectedPlanning = this.selectedSeance;
              // this.absence.activity_session_id = this.selectedSeance.id;
            }
          } else {
            that.planningsData = [];
          }
          that.paginatorData = response.data.meta;
          this.loadPlanning = false;
        });
    },
    toggleModalPreventAbsence(toggle = "") {
      $("#modal-prevent-absence").modal();
    },
    showModalCalendar: function() {
      $("#choice-session-on-calendar").modal({
        keyboard: false,
        backdrop: "static"
      });
    },
    hideModalCalendar: function() {
      $("#choice-session-on-calendar").modal("hide");
    }
  }
};
</script>

<style lang="scss" scoped>
@import "@/Pages/Components/main.scss";

.btn-prevent-absence {
  background-color: $color-orange;
  color: white;
}

.btn-deselect-planning {
  position: absolute;
  right: 0;
  z-index: 10;
}

.planning-selected-for-absence-prevention {
  height: 140px;
  position: relative;
}

.ps {
  min-height: 245px;
  max-height: 60vh;
  height: 100%;
}
</style>
