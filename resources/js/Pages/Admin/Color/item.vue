<template>
  <div
    :style="'background-color:' + color.background + ';color:' + color.font"
    class="p-3 text-center"
  >
    <h1 class="font-bold text-xl">Title</h1>
    <p class="font-semibold">contenu</p>
  </div>
</template>

<script>
export default {
  props: ["colorData"],
  beforeMount() {
    this.color = this.colorData;
  },
  data() {
    return {
      color: {}
    };
  }
};
</script>


