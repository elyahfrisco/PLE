<template>
  <div v-show="message" class="bg-green-500 text-white text-2xl p-3">
    {{ message }}
  </div>
</template>

<script>
export default {
  props: ["message"]
};
</script>


