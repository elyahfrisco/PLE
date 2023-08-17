<template>
  <div class="grid grid-cols-3 gap-4">
    <div
      class="p-3 bg-blue-400 my-2 flex items-center justify-center text-white text-center"
    >
      <a @click="addPassCategory" class="text-3xl" v-if="!showCreateForm"
        >Ajouter</a
      >
      <div v-else>
        <form-pass-category :errors="errors"></form-pass-category>
      </div>
    </div>

    <item-pass-category
      :pass_category="pass_category"
      v-for="pass_category in this.pass_categories"
      :key="pass_category.id"
      :errors="errors"
    ></item-pass-category>
  </div>
</template>

<script>
import itemPassCategory from "./item.vue";
import formPassCategory from "./form.vue";
export default {
  props: ["pass_categories", "errors"],
  components: {
    itemPassCategory,
    formPassCategory
  },
  data() {
    return {
      showCreateForm: false
    };
  },
  methods: {
    addPassCategory() {
      this.showCreateForm = true;
    }
  }
};
</script>


