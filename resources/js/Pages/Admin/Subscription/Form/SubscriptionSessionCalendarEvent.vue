<template>
  <small
    v-if="pass_type != 'other' && pass_type != 'decouvert'"
    class="activity-price"
    :style="{
      'background-color': event.font_color,
      color: event.bgcolor,
      'border-color': event.bgcolor
    }"
    >{{ event.price.price }}€</small
  >
  <!-- <perfect-scrollbar> -->
  <button
    class="btn-success btn btn-sm session-selected rounded-0"
    v-if="selectedSessions[event.id] != undefined"
  >
    <i class="fa fa-check"></i>
    selectionnée
  </button>
  <div class="w-100 py-1 row mx-0">
    <div
      class="
        vuecal__event-title
        font-wieght-bold
        col-md-7
        d-flex
        align-items-center
        justify-centent-center
        px-1
      "
    >
      <div class="text-center">
        <div class="text-uppercase" v-html="event.title" />
      </div>
    </div>
    <div
      class="
        vuecal__event-time
        col-md-5
        d-flex
        align-items-center
        justify-centent-center
        px-1
      "
    >
      <small>
        <span class="d-block timeInterval"
          >{{ event.start.formatTime("HH:mm") }} à
          {{ event.end.formatTime("HH:mm") }}</span
        >
        <span
          class="d-block"
          data-toggle="tooltip"
          title="Nombre des participants"
        >
          <i class="fa fa-users"></i>
          {{ event.participants_count }}/{{ event.max_effective }}
        </span>
      </small>
    </div>
  </div>
  <!-- </perfect-scrollbar> -->
</template>

<script>
export default {
  props: {
    event: {
      required: true
    },
    selectedSessions: {
      default: {}
    },
    pass_type: {
      default: "trimester"
    }
  }
};
</script>


