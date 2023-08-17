<template>
  <div class="card flat planning-card" :style="style">
    <div class="card-body">
      <div class="row card-body--content">
        <div class="col-sm-4 card-body--content-left">
          <div class="planning-card--time">
            <div class="plannign-card--date">
              <i class="icon fa fa-calendar"></i>
              <p class="dd">{{ dateObject.day }}</p>
              <p class="m">{{ dateObject.month }}</p>
              <p class="yyyy">{{ dateObject.year }}</p>
            </div>
            <div class="planning-card--hour card-body--content-right">
              <i class="icon fa fa-clock"></i>
              <p class="planning-card--hour-start">
                {{ planningObject.timestart }}
              </p>
              <p class="planning-card--hour-separator">à</p>
              <p class="planning-card--hour-start">
                {{ planningObject.timeend }}
              </p>
            </div>
          </div>
        </div>
        <div class="col-sm-8">
          <div class="planning-card--content">
            <div
              class="elapse-time-container text-center"
              v-if="
                planningObject.presence &&
                  planningObject.presence.absence_prevention !== null
              "
            >
              <p class="elapse-time mb-0">
                absence prévenu :
                {{
                  planningObject.presence.absence_prevention
                    .ElapseTimeBeforeStart
                }}
                <br />{{ planningObject.presence.absence_prevention.motif }} :
                {{ planningObject.presence.absence_prevention.reason }}
              </p>
            </div>
            <div
              class="elapse-time-container text-center"
              v-else-if="show_elapsetime === true"
            >
              <p class="elapse-time mb-0">{{ planningObject.elapseTime }}</p>
            </div>

            <div class="activity-name-container">
              <div class="">
                <p class="activity-name">{{ planningObject.activity }}</p>
                <p
                  class="activity-participant-reste text-center mb-1"
                  v-if="planningObject.remaining_place != undefined"
                >
                  Place :
                  <span
                    v-if="auth_user.role_name !== 'admin'"
                    class="btn btn-sm btn-primary py-0 text-white"
                  >
                    <i class="fa fa-user-friends mr-1"></i
                    >{{ planningObject.remaining_place }}</span
                  >
                  <a
                    v-else
                    :href="
                      route('establishments.plannings.sessions.participants', {
                        establishment: planningObject.establishment_id,
                        activity_session: planningObject.id
                      })
                    "
                    target="_blank"
                    class="btn btn-sm btn-primary py-0 text-white"
                    data-html="true"
                    data-toggle="tooltip"
                    :title="
                      'Participants ' +
                        planningObject.participants_count +
                        '/' +
                        planningObject.max_effective
                    "
                  >
                    <i class="fa fa-user-friends"></i>
                    {{ planningObject.remaining_place }}
                  </a>
                </p>
              </div>
            </div>
            <div class="d-flex justify-content-around">
              <!-- <btn-activity-info /> -->
              <template
                v-if="
                  planningObject.presence &&
                    planningObject.presence.can_prevent_absence === true &&
                    !planningObject.presence?.absence_prevention
                "
              >
                <btn-prevent-absence
                  @prevent_absence="$emit('prevent_absence', planningObject)"
                />
              </template>
              <template v-else>
                <template v-if="auth_user.role_name != 'coach'">
                  <btn-present v-if="planningObject.is_accomplished" />
                  <template
                    v-else-if="
                      planningObject.presence &&
                        planningObject.presence.absence_prevention !== null &&
                        planningObject.presence.absence_prevention.queue
                    "
                  >
                    <btn-recuperation
                      v-if="
                        !planningObject.presence.absence_prevention.queue
                          .recuperation_request
                      "
                      @request_recuperation="
                        $emit('request_recuperation', planningObject)
                      "
                    />
                  </template>
                </template>
              </template>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import BtnPreventAbsence from "@/Pages/Components/Planning/BtnPreventAbsence.vue";
import BtnPresent from "@/Pages/Components/Planning/BtnPresent.vue";
import BtnRecuperation from "@/Pages/Components/Planning/BtnRecuperation.vue";
import BtnActivityInfo from "@/Pages/Components/Planning/BtnActivityInfo.vue";

export default {
  components: {
    BtnPreventAbsence,
    BtnPresent,
    BtnRecuperation,
    BtnActivityInfo
  },

  props: ["planning", "show_elapsetime"],
  data() {
    return {
      style: Object,
      dateObject: false,
      planningObject: null
    };
  },
  watch: {
    planning(value) {
      let date = this.$moment(value.date);

      this.dateObject = {
        day: date.format("DD"),
        month: date.format("MMM"),
        year: date.format("YYYY")
      };

      this.planningObject = value;

      this.style = {
        "background-color": this.planning.bgcolor
          ? this.planning.bgcolor
          : "#2BAC96",
        color: this.planning.font_color ? this.planning.font_color : "#fff"
      };
    }
  },

  beforeMount() {
    this.planningObject = this.planning;
    this.style = {
      "background-color": this.planning.bgcolor
        ? this.planning.bgcolor
        : "#2BAC96",
      color: this.planning.font_color ? this.planning.font_color : "#fff"
    };

    let date = this.$moment(this.planningObject.date);
    this.dateObject = {
      day: date.format("DD"),
      month: date.format("MMM"),
      year: date.format("YYYY")
    };
  }
};
</script>

<style scoped>
.btn-planning {
  color: white;
  border-radius: 50px;
  height: 45px;
  width: 45px;
  font-size: 18px;
  vertical-align: middle;
  padding: 6px 8px;
  text-align: center;
  border: 2px solid white;
}
i {
  margin: 20% 0;
}

.planning-card {
  overflow: hidden;
}
.planning-card--time {
  display: flex;
  text-align: center;
  align-items: center;
  justify-content: space-between;
}
.planning-card--content {
  display: flex;
  flex-direction: column;
  min-height: 100%;
  justify-content: space-between;
}
.activity-name-container {
  display: flex;
  justify-content: center;
  align-items: center;
  flex: 1;
}

.planning-card--time p {
  margin: 0;
}
.planning-card--time p.dd {
  font-weight: bold;
  font-size: 1rem;
}
.planning-card--time .icon {
  font-size: 1.5rem;
}

.card-body {
  padding: 0 !important;
}
.card-body--content-left {
  background: #00000042;
  padding: 1.5rem;
}

.planning-card--content {
  padding: 1rem 0.5rem;
}

.elapse-time {
  font-size: 11px;
}
</style>
