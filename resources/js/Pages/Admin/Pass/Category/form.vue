<template>
  <form class="p-5">
    <label class="block">
      <span>Nom</span>
      <input
        type="text"
        class="mt-1 block text-black w-full rounded-md bg-gray-100 border-transparent focus:border-gray-500 focus:bg-white focus:ring-0"
        placeholder=""
        v-model="form.name"
      />
      <small class="validation-error" v-if="errors.name">{{
        errors.name
      }}</small>
    </label>
    <button
      type="submit"
      @click.prevent="update"
      v-if="editMode"
      class="underline"
    >
      Enregistrer la Modification
    </button>
    <button type="submit" @click.prevent="submit" v-else class="underline">
      Enregistrer
    </button>
  </form>
</template>

<script>
export default {
  props: {
    pass_category: Object,
    errors: Object,
    edit: Boolean
  },
  data() {
    return {
      form: {
        name: null
      },
      editMode: false
    };
  },
  beforeMount() {
    if (this.$props.edit == true) {
      console.log(this.pass_category);
      this.editMode = true;
      this.form = this.pass_category;
    }
  },
  methods: {
    submit() {
      this.$inertia.post(route("passes.categories.store"), this.form, {
        onSuccess: () => {
          this.form.name = "";
        }
      });
    },
    update() {
      this.$inertia.put(
        route("passes.categories.update", this.form.id),
        this.form,
        {
          onSuccess: () => {
            this.$emit("passesCategoryUpdated");
          }
        }
      );
    }
  }
};
</script>


