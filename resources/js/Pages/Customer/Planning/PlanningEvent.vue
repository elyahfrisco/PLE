<template>
  <div class="w-100">
    <div
      class="vuecal__event-title text-uppercase"
      @click="showDetail(event)"
      v-html="event.title"
    />
    <small class="vuecal__event-time">
      <span
        >{{ event.start.formatTime("HH:mm") }} -
        {{ event.end.formatTime("HH:mm") }}</span
      >
    </small>

    <modal-coach-select
      v-if="activeSessionForCoachAssignation.id"
      :modal-id="`select-coach-${activeSessionForCoachAssignation.id}`"
      :session="activeSessionForCoachAssignation"
      @coachAssigned="$emit('refreshSession')"
    />
    <modal-add-participants
      v-if="activeSessionForParticipantsAdd.id"
      :modal-id="`add-participants-${activeSessionForParticipantsAdd.id}`"
      :session="activeSessionForParticipantsAdd"
      @participantsAdded="$emit('refreshSession')"
    />
    <modal-session-activity-detail
      :session="activeSessionForDetail"
      @participantsAdded="$emit('refreshSession')"
    />
  </div>
</template>

<script>
import modalCoachSelect from "./modalCoachSelect.vue";
import ModalSessionActivityDetail from "./ModalSessionActivityDetail.vue";
import ModalAddParticipants from "@/Pages/Components/Planning/ModalAddParticipants.vue";

export default {
  props: {
    event: {
      required: true,
      type: Object
    },
    toHistory: {
      default: ""
    }
  },
  components: {
    modalCoachSelect,
    ModalSessionActivityDetail,
    ModalAddParticipants
  },
  data() {
    return {
      activeSessionForCoachAssignation: {},
      activeSessionForParticipantsAdd: {},
      activeSessionForDetail: {}
    };
  },
  methods: {
    deleteActivitySession() {
      this.$inertia.delete(
        route("establishments.plannings.sessions.destroy", {
          establishment: this.event.establishment_id,
          session: this.event.id
        }),
        {
          preserveScroll: true,
          onBefore: () => confirm("Supprimer la séance?"),
          onSuccess: () => {
            this.$emit("refreshSession");
          }
        }
      );
    },
    AddParticipants(session) {
      this.activeSessionForParticipantsAdd = session;
      setTimeout(() => {
        $(`#add-participants-${session.id}`).modal();
      }, 200);
    },
    setSessionCoach(session) {
      this.activeSessionForCoachAssignation = session;
      setTimeout(() => {
        $(`#select-coach-${session.id}`).modal();
      }, 200);
    },
    showDetail(event) {
      return 0;
      this.activeSessionForDetail = event;
      setTimeout(() => {
        $("#session-activity-detail").modal();
      }, 200);
    }
  }
};
</script>


