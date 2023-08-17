<template>
  <div class="lpdl-input-text-area">
    <textarea
      :placeholder="placeholder ?? ''"
      v-bind:v-model="modelValue"
      @input="updateValue($event.target.value)"
    />
  </div>
</template>

<script>
export default {
  props: ["placeholder", "modelValue"],
  methods: {
    updateValue: function(v) {
      this.$emit("update:modelValue", v);
    }
  }
};
</script>

<style lang="scss" scoped>
@import "@/Pages/Components/main.scss";

.lpdl-input-text-area {
  textarea {
    display: flex;
    position: relative;
    box-sizing: border-box;
    width: 100%;
    border: 1px solid $color-border-input;
    outline: none;
    border-radius: 3px;
    align-items: center;
    padding: 1rem;
    min-height: 10rem;
  }
}
</style>
