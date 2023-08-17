<template>
  <button
    type="button"
    class="
      btn btn-planning btn-success btn-action btn-sm
      edit-request-recuperation
      text-white
    "
    _data-toggle="tooltip"
    _data-placement="top"
    :title="title"
    @click.prevent="showModal()"
  >
    <i class="fa fa-user-clock"></i>
  </button>
  <modal-request-recuperation
    v-if="active"
    :queue_id="queue_id"
    :emit_after_save="emit_after_save"
    @recuperation_request_saved="() => $emit('recuperation_request_saved')"
  />
</template>

<script>
import ModalRequestRecuperation from "@/Pages/Components/ModalRequestRecuperation.vue";
export default {
  components: {
    ModalRequestRecuperation
  },
  props: {
    title: {
      default: "Modifier la séance de récupération"
    },
    queue_id: {
      default: null
    },
    emit_after_save: {
      default: null
    }
  },
  data() {
    return {
      active: false
    };
  },
  methods: {
    showModal() {
      this.active = true;
      setTimeout(() => {
        $("#modal-edit-request-recuperation-" + this.queue_id).modal();
      }, 500);
    }
  }
};
</script>
