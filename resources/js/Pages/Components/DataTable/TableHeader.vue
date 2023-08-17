<template>
  <th
    :style="{
      width: width ? width + 'px' : '',
      'max-width': maxWidth ? maxWidth + 'px' : '',
      'min-width': minWidth ? minWidth + 'px' : ''
    }"
    :class="{ pointer: name }"
    @click="name ? sort() : false"
  >
    <slot />
    <i
      v-if="
        direction === 'asc' && (q_.sortBy == name || sortParams.sortBy == name)
      "
      class="ml-1 text-muted fa fa-sort-alpha-down"
    ></i>
    <i
      v-else-if="
        direction === 'desc' && (q_.sortBy == name || sortParams.sortBy == name)
      "
      class="ml-1 text-muted fa fa-sort-alpha-down-alt"
    ></i>
  </th>
</template>

<script>
export default {
  props: ["width", "maxWidth", "minWidth", "name"],
  data() {
    return {
      direction: null
    };
  },
  mounted() {
    if (this.q_.sortBy && this.q_.sortBy == this.name)
      this.direction = this.q_.sortDirection;
    this.$emitter.on(
      "resetOtherActiveSort",
      () => (this.sortParams.sortBy = {})
    );
  },
  methods: {
    sort() {
      this.$emitter.emit("resetOtherActiveSort");
      this.direction =
        !this.direction || this.direction == "desc" ? "asc" : "desc";
      this.sortParams = {
        sortBy: this.name,
        sortDirection: this.direction
      };
      this.$emitter.emit("PageLoading");
      this.$emitter.emit("sortTable", this.sortParams);
    }
  }
};
</script>
