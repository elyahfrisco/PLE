<template>
  <teleport to="body">
    <div
      class="modal"
      :class="{
        fade: fade !== false
      }"
      tabindex="-1"
      :id="id"
      :aria-labelledby="id"
      aria-hidden="true"
      data-backdrop="static"
      data-keyboard="false"
    >
      <div class="modal-dialog modal-dialog-centered" :class="maxWidthClass">
        <div
          class="modal-content"
          :style="{ 'min-height': minHeight != '' ? minHeight : 'auto' }"
        >
          <div class="modal-header">
            <h5
              class="modal-title"
              id="exampleModalLabel"
              v-if="title"
              v-html="title"
            ></h5>
            <button
              type="button"
              class="close"
              data-dismiss="modal"
              aria-label="Close"
              @click="$emit('modal-closing')"
            >
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">
            <slot></slot>
          </div>
        </div>
      </div>
    </div>
  </teleport>
</template>

<script>
export default {
  props: {
    id: {
      type: String,
      required: true
    },
    maxWidth: {
      default: "md"
    },
    fade: {
      default: true
    },
    title: {
      type: String
    },
    minHeight: {
      type: String
    }
  },
  computed: {
    maxWidthClass() {
      return {
        sm: "modal-sm",
        md: null,
        lg: "modal-lg",
        xl: "modal-xl"
      }[this.maxWidth];
    }
  },
  mounted() {
    this.initTooltipe();
    $(".modal:not(.handle)").each(function() {
      $(this)
        .find(".modal-dialog")
        .draggable({
          handle: ".modal-header"
        });
      $(this).addClass("handle");
      $(this).on("hidden.bs.modal", function(e) {
        if ($(".modal.show")[0]) $("body").addClass("modal-open");
      });
    });
  }
};
</script>

<style lang="scss">
@import "@/Pages/Components/main.scss";

.modal-content {
  border: none !important;
  border-radius: 0.5rem;
}

.modal-header {
  border-top-left-radius: calc(0.5rem - 1px);
  border-top-right-radius: calc(0.5rem - 1px);
  background-color: $color-lpdl;
  color: white;

  [aria-hidden] {
    color: white;
  }
}
</style>
