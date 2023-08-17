<template>
  <div class="input-group mb-2">
    <input
      type="text"
      class="form-control"
      :value="content"
      @change="updateValue($event.target.value)"
      required
    />
    <div class="input-group-append">
      <button
        type="button"
        @click.prevent="deleteAnswer"
        class="btn btn-outline-danger"
        tabindex="-1"
      >
        <i class="fa fa-times"></i>
      </button>
    </div>
  </div>
  <small class="validation-error" v-if="error">{{ error }}</small>
</template>

<script>
export default {
  props: ["content", "error"],
  methods: {
    updateValue(value) {
      this.$emit("update:content", value);
    },
    deleteAnswer() {
      this.$emit("delete-answer");
    }
  }
};
</script>


