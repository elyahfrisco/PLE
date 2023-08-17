<template>
  <div class="lpdl-input-text">
    <input
      type="text"
      :placeholder="placeholder ?? ''"
      :value="modelValue"
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

.lpdl-input-text {
  input {
    display: flex;
    position: relative;
    box-sizing: border-box;
    width: 100%;
    border: 1px solid $color-border-input;
    outline: none;
    border-radius: 3px;
    align-items: center;
    min-height: 37.5px !important;
    height: 37.5px !important;
    padding: 0 1rem;
  }
}
</style>
