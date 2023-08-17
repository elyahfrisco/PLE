<template>
  <span
    v-if="processing"
    class="spinner-border spinner-border-sm"
    :class="class_"
    role="status"
    aria-hidden="true"
  ></span>
</template>

<script>
export default {
  props: ["processing", "class_"]
};
</script>


