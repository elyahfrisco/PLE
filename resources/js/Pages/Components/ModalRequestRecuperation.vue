<template>
  <jet-modal
    :id="
      queue_id
        ? 'modal-edit-request-recuperation-' + queue_id
        : 'modal-request-recuperation'
    "
    maxWidth="xl"
    :fade="false"
    :title="
      (auth_user.role_name != 'admin' ? 'Demander' : 'Donner') +
        ' une récupération'
    "
  >
    <template v-if="!calendarActive">
      <div class="form-group">
        <label for="seence"> Description : </label>
        <div>
          <textarea
            class="form-control"
            rows="3"
            v-model="form.content"
            minlength="20"
          ></textarea>
        </div>
      </div>
      <div class="row mb-3">
        <div class="col-md-6">
          <div class="form-group">
            <label for="seence"> Centre de récupération : </label>
            <div>
              <Multiselect
                v-model="form.establishment_id"
                placeholder="--Centre de récupération--"
                :options="select.establishments"
                label="name"
                trackBy="name"
                valueProp="id"
                required
              />
            </div>
          </div>
          <button
            class="btn btn-primary w-100"
            :class="{ disabled: !form.establishment_id }"
            :disabled="!form.establishment_id"
            @click="showModalCalendar"
          >
            <span v-if="selectedSession">Resélectionner</span>
            <span v-else>Selectionner</span>
            la séance de récupération
          </button>
        </div>
        <div class="col-md-6">
          <template v-if="selectedSession">
            <button
              v-if="defaultSelectedSession != selectedSession"
              class="btn btn-deselect-planning"
              @click.prevent="deselectPlanning"
              :style="{
                color: selectedSession.font_color
              }"
            >
              <i class="fa fa-times-circle"></i>
            </button>
            <PlanningCard :planning="selectedSession" />
          </template>
        </div>
      </div>
      <div class="row">
        <button
          class="btn btn-send-request-recuperation text-white mx-auto"
          @click.prevent="submitRequest"
          :disabled="
            loadSaving ||
              !selectedSession ||
              (defaultSelectedSession == selectedSession &&
                form.original_content == form.content) ||
              (defaultSelectedSession == selectedSession &&
                form.original_establishment_id != form.establishment_id)
          "
        >
          {{ form.id ? "Modifier" : "Soumettre" }} la demande de récupération
        </button>
      </div>
    </template>
    <template v-else>
      <Calendar
        :filter="form"
        :calSync="true"
        @request_recuperation="setSession"
      ></Calendar>
    </template>
  </jet-modal>
</template>
<script>
import Calendar from "@/Pages/Components/Calendar.vue";
import PlanningCard from "@/Pages/Components/PlanningCard.vue";
import axios from "axios";
export default {
  props: ["sessionInQueue", "queue_id", "emit_after_save"],
  components: { Calendar, PlanningCard },
  data() {
    return {
      selectedSession: null,
      form: {
        activity_session_id_to_catch_up: null,
        queue_id: null,
        user_id: null,

        activity_session_id_for_catch_up: null,
        content: null,

        establishment_id: null,
        max_date: null
      },
      select: {},
      defaultSelectedSession: null,
      calendarActive: false,
      loadSaving: false
    };
  },
  watch: {
    sessionInQueue: {
      deep: true,
      handler() {
        this.setActiveQueue();
      }
    }
  },
  mounted() {
    if (this.queue_id) {
      this.getRequestRecuperation();
    } else {
      this.setActiveQueue();
    }
    this.select.establishments = this.$page.props.establishments_list;
  },
  methods: {
    setActiveQueue() {
      setTimeout(() => {
        if (this.sessionInQueue) {
          if (this.sessionInQueue.presence) {
            this.form.activity_session_id_to_catch_up = this.sessionInQueue.id;

            this.form.queue_id = this.sessionInQueue.presence.absence_prevention
              .queue_id
              ? this.sessionInQueue.presence.absence_prevention.queue_id
              : this.sessionInQueue.presence.absence_prevention.queue?.id;

            this.form.user_id = this.sessionInQueue.presence.absence_prevention.user_id;

            this.form.max_date = this.dateAng(
              this.sessionInQueue.presence.absence_prevention.queue
                .subscription_activity.can_catch_up_until
            );
          } else {
            this.form.activity_session_id_to_catch_up = this.sessionInQueue
              .pivot?.activity_session_id
              ? this.sessionInQueue.pivot.activity_session_id
              : this.sessionInQueue.activity_session_id;

            this.form.queue_id = this.sessionInQueue.queue_id
              ? this.sessionInQueue.queue_id
              : this.sessionInQueue.queue
              ? this.sessionInQueue.queue.id
              : this.sessionInQueue.id;

            this.form.user_id = this.sessionInQueue.pivot?.user_id
              ? this.sessionInQueue.pivot.user_id
              : this.sessionInQueue.user_id;

            // if (this.sessionInQueue.renewal) {
            //   if ("stop" != this.sessionInQueue.renewal.renewal_status) {
            //     this.form.max_date = null;
            //   } else {
            //     this.form.max_date = this.dateAng(
            //       this.sessionInQueue.can_catch_up_until
            //         ? this.sessionInQueue.can_catch_up_until
            //         : this.sessionInQueue.subscription_activity
            //             .can_catch_up_until
            //     );
            //   }
            // } else {
            this.form.max_date = this.dateAng(
              this.sessionInQueue.can_catch_up_until
                ? this.sessionInQueue.can_catch_up_until
                : this.sessionInQueue.subscription_activity.can_catch_up_until
            );
            // }
          }
        }
      }, 100);
    },

    showModalCalendar: function() {
      this.calendarActive = true;
      setTimeout(() => {
        this.simulateCalendarClickMonth();
      }, 500);
    },

    setSession(session) {
      this.form.activity_session_id_for_catch_up = session.id;
      this.selectedSession = session;
      this.calendarActive = false;
    },

    deselectPlanning() {
      this.form.activity_session_id_for_catch_up = this.defaultSelectedSession?.activity_session_id_for_catch_up;
      this.selectedSession = this.defaultSelectedSession
        ? this.defaultSelectedSession
        : null;
    },

    submitRequest() {
      if (this.defaultSelectedSession) {
        this.loadSaving = true;
        this.$inertia.put(
          route("recuperation_requests.update", this.form.id),
          this.form,
          {
            preserveState: this.emit_after_save,
            onSuccess: () => {
              $("#modal-edit-request-recuperation-" + this.form.queue_id).modal(
                "hide"
              );
              this.emit_after_save
                ? this.$emit("recuperation_request_saved")
                : this.iReload();
            },
            onFinish: () => {
              this.loadSaving = false;
            }
          }
        );
      } else {
        this.$inertia.post(route("recuperation_requests.store"), this.form, {
          preserveState: this.emit_after_save,
          onSuccess: () => {
            $("#modal-request-recuperation").modal("hide");
            this.emit_after_save
              ? this.$emit("recuperation_request_saved")
              : this.iReload();
          }
        });
      }
    },

    getRequestRecuperation() {
      axios.get(route("api.queue.detail", this.queue_id)).then(response => {
        if (response.data) {
          let session_for_catch_up =
            response.data.recuperation_request.session_for_catch_up;

          this.form.id = response.data.recuperation_request.id;
          this.form.content = response.data.recuperation_request.content;
          this.form.original_content =
            response.data.recuperation_request.content;
          this.form.activity_session_id_to_catch_up =
            response.data.activity_session_id;
          this.form.activity_session_id_for_catch_up = session_for_catch_up.id;
          this.form.queue_id = response.data.recuperation_request.queue_id;
          this.form.establishment_id =
            response.data.recuperation_request.session_for_catch_up.establishment_id;
          this.form.original_establishment_id =
            response.data.recuperation_request.session_for_catch_up.establishment_id;
          this.form.user_id = response.data.user_id;
          this.form.max_date = this.dateAng(
            response.data.subscription_activity.can_catch_up_until
          );

          this.selectedSession = this.defaultSelectedSession = {
            activity_session_id_for_catch_up: session_for_catch_up.id,
            date: session_for_catch_up.date,
            timestart: this.H(session_for_catch_up.time_start),
            timeend: this.H(session_for_catch_up.time_end),
            activity: session_for_catch_up.activity.name,
            elapseTime: session_for_catch_up.elapseTime
          };
        }
      });
    }
  }
};
</script>

<style scoped>
.btn-deselect-planning {
  position: absolute;
  right: 10px;
  z-index: 10;
}
.btn-send-request-recuperation {
  background-color: #dc1c4b;
}
</style>
