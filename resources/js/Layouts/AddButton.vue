<template>
  <a
    class="right badge badge-success text-white add-button"
    :href="route(this.link)"
    data-toggle="tooltip"
    data-placement="right"
    :title="tooltip"
    ><i class="fa fa-plus m-0"></i
  ></a>
</template>

<script>
export default {
  props: ["link", "tooltip"]
};
</script>


