<template>
  <button
    v-if="type == 'button'"
    href="#"
    class="btn btn-primary btn-sm btn-action btn-action-eye"
    data-toggle="tooltip"
    title="Afficher"
  >
    <i class="fa fa-eye"></i> <slot />
  </button>
  <inertia-link
    v-else
    href="#"
    class="btn btn-primary btn-sm btn-action btn-action-eye"
    data-toggle="tooltip"
    title="Afficher"
  >
    <i class="fa fa-eye"></i> <slot />
  </inertia-link>
</template>

<script>
export default {
  props: ["type"]
};
</script>
