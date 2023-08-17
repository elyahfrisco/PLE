<template>
  <div class="w-100">
    <div class="vuecal__event-title text-uppercase" v-html="event.title" />
    <small class="vuecal__event-time">
      <span
        >{{ event.start.formatTime("HH:mm") }} -
        {{ event.end.formatTime("HH:mm") }}</span
      >
      <br />
      <inertia-link
        v-if="
          auth_user.role_name != 'customer' && auth_user.role_name != 'prospect'
        "
        :href="
          route('establishments.plannings.sessions.participants', {
            establishment: event.establishment_id,
            activity_session: event.id
          })
        "
        class="btn btn-sm btn-primary py-0 text-white"
        data-toggle="tooltip"
        title="Liste des participants"
        ><i class="fa fa-users"></i> {{ event.participants_count }}/{{
          event.max_effective
        }}</inertia-link
      >

      <template v-if="!event.accomplished">
        <!-- <a
                class="btn btn-sm btn-success py-0 text-white ml-1"
                data-toggle="tooltip"
                title="Marquer comme terminé ?"
                @click="setAccomplished(event.id)"
                ><i class="fa fa-check"></i
              ></a> -->
      </template>
    </small>
  </div>
</template>

<script>
export default {
  props: {
    event: {
      type: Object,
      required: true
    }
  }
};
</script>


