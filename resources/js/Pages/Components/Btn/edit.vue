<template>
  <button
    v-if="type == 'button'"
    href="#"
    class="btn btn-success btn-sm btn-action btn-action-edit"
    data-toggle="tooltip"
    title="Modifier"
  >
    <i class="fa fa-edit"></i> <slot />
  </button>
  <inertia-link
    v-else
    href="#"
    class="btn btn-success btn-sm btn-action btn-action-edit"
    data-toggle="tooltip"
    title="Modifier"
  >
    <i class="fa fa-edit"></i> <slot />
  </inertia-link>
</template>

<script>
export default {
  props: ["type"]
};
</script>
