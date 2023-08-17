<template>
  <div class="card card-outline card-success rounded-0">
    <div class="card-header py-2">
      <p class="font-weight-bold mb-0 d-inline">
        {{ session.display.activity }}
      </p>
      <div class="card-tools">
        <button
          type="button"
          @click="deselectSession"
          class="btn btn-tool"
          data-card-widget="remove"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    <div class="card-body py-2">
      <p class="mb-0">
        <i class="fa fa-calendar text-default mr-1"></i
        >{{ session.display.day }}
        {{ session.display.date }}
      </p>
      <p class="mb-0">
        <i class="fa fa-clock text-default mr-1"></i
        >{{ session.display.planning }}
      </p>
    </div>
  </div>
</template>

<script>
export default {
  props: {
    session: {
      type: Object,
      default: {
        display: {
          activity: "Activité",
          day: "jour",
          date: "00/00/0000",
          planning: "00:00 à 00:00"
        }
      }
    },
    pass_type: {
      type: String
    }
  },
  methods: {
    deselectSession() {
      this.$emit("deselectSession", this.session.id);
    }
  }
};
</script>


