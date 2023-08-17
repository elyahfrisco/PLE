<template>
  <div class="w-100">
    <div
      v-if="event.absence_count"
      class="btn btn-danger absence_count"
      v-html="event.absence_count"
    />
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
      <br />
      <inertia-link
        :href="
          route('establishments.plannings.sessions.participants', {
            establishment: event.establishment_id,
            activity_session: event.id,
            _query: {
              toHistory: toHistory
            }
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
            (event.TimeSpent && !event.presence_checked_at
              ? '<br> <u>Vous devez effectué la presence</u>'
              : '')
        "
      >
        <i class="fa fa-user-friends"></i>
        {{ event.max_effective - event.participants_pass_trimester_count }}R
      </inertia-link>

      <a
        class="btn btn-sm btn-success py-0 text-white ml-1"
        :class="{
          'no-coach': event.coachs.length == 0
        }"
        @click.prevent="setSessionCoach(event)"
      >
        <div
          v-if="event.coachs.length"
          title="Enseignants : "
          data-toggle="popover"
          data-trigger="hover"
          :data-content="toUL(event.coachs, 'user')"
          data-html="true"
        >
          <i class="fa fa-user-friends"></i>
        </div>
        <div v-else data-toggle="tooltip" title="Assigner l'enseignant">
          <i class="fa fa-user-plus"></i>
        </div>
      </a>
      <template v-if="!event.TimeSpent">
        <a
          class="btn btn-sm btn-warning py-0 ml-1"
          data-toggle="tooltip"
          title="Ajouter des participants"
          @click.prevent="AddParticipants(event)"
          ><i class="fa fa-user-tag"></i
        ></a>
        <!-- <a
                  class="btn btn-sm btn-warning py-0 ml-1"
                  data-toggle="tooltip"
                  title="Marquer comme terminé ?"
                  @click="setAccomplished(event.id)"
                  ><i class="fa fa-check"></i
                ></a> -->
        <a
          href="#"
          class="btn btn-sm btn-danger py-0 text-white ml-1"
          data-toggle="tooltip"
          title="Supprimer la séance"
          @click.prevent="deleteActivitySession"
          ><i class="fa fa-trash"></i
        ></a>
      </template>
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


