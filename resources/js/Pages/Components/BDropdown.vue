<template>
  <div class="dropdown" :class="dropright ? 'dropright' : ''">
    <button
      class="btn dropdown-toggle"
      :class="'btn-' + (btn ? btn : 'secondary') + (sm ? ' btn-sm' : '')"
      type="button"
      :id="id"
      data-toggle="dropdown"
      aria-haspopup="true"
      aria-expanded="false"
    >
      <slot name="title"></slot>
    </button>
    <div
      class="dropdown-menu"
      :class="'dropdown-menu-' + (position ? position : 'right')"
      :aria-labelledby="id"
    >
      <slot name="content"></slot>
    </div>
  </div>
</template>

<script>
export default {
  props: ["btn", "sm", "position", "dropright"],
  data() {
    return {
      id: parseInt(Math.random() * 100000)
    };
  }
};
</script>


