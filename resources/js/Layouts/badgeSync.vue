<template>
  <span
    class="right badge badge-light badge-count"
    data-toggle="tooltip"
    data-placement="right"
    :title="tooltip"
    :id="id"
  ></span>
</template>

<script>
export default {
  props: ["link", "tooltip", "id", "user_role"]
};
</script>


