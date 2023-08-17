<template>
  <aside class="control-sidebar control-sidebar-dark" style="display: none">
    <div class="p-3">
      <h5>Title</h5>
      <p>{{ user }}</p>
    </div>
  </aside>
</template>

<script>
export default {
  props: ["user"]
};
</script>


