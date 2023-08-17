<template>
  <div class="grid grid-cols-4 gap-4 bg-gray-400 p-10">
    <div class="flex justify-center items-center p-1 bg-white">
      <div
        class="p-1 text-center font-semibold text-2xl cursor-pointer"
        @click="showForm = true"
        v-if="!showForm"
      >
        Ajouter
      </div>
      <form-color
        :errors="errors"
        v-else
        :class="'text-white'"
        @closeForm="showForm = false"
      ></form-color>
    </div>
    <item-color
      :colorData="color"
      v-for="color in this.colors"
      :key="color.id"
    ></item-color>
  </div>
</template>

<script>
import formColor from "./form.vue";
import itemColor from "./item.vue";
export default {
  components: {
    formColor,
    itemColor
  },
  props: ["colors", "errors"],
  data() {
    return {
      showForm: false
    };
  }
};
</script>


