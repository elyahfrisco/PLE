<template>
  <div class="p-3 bg-blue-400 my-2 text-white text-center">
    <div class="" v-if="!editMode">
      {{ this.pass_category }}
      <br />
      <a @click="editMode = true" class="underline"> Modifier </a>
      <br />
      <a
        :href="route('passes.categories.passes.index', this.pass_category.id)"
        class="underline"
      >
        Passes
      </a>
      <br />
      <a href="#" @click.prevent="deletePassCategory" class="underline">
        Supprimer
      </a>
    </div>
    <div class="" v-else>
      <form-pass-category
        :pass_category="this.pass_category"
        :edit="true"
        :errors="this.errors"
        @passesCategoryUpdated="editMode = false"
      ></form-pass-category>
      <button type="button" @click="editMode = false" class="underline">
        Annuler
      </button>
    </div>
  </div>
</template>

<script>
import formPassCategory from "./form.vue";

export default {
  components: {
    formPassCategory
  },
  props: {
    pass_category: Object,
    errors: Object
  },
  data() {
    return {
      editMode: false
    };
  },
  methods: {
    deletePassCategory() {
      this.$inertia.delete(
        route("passes.categories.destroy", this.pass_category.id),
        {
          onBefore: () => confirm("Supprimer Categorie pass?")
        }
      );
    }
  }
};
</script>


