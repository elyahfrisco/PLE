<template>
  <div class="text-center">
    <h1>Aperçu</h1>
    <item-color :colorData="form"></item-color>
  </div>
  <form class="p-5 text-gray-600 font-semibold text-center">
    <label class="block">
      <span class="text-lg">Couleur de fond</span>
      <input
        type="color"
        class="mt-1 block w-full bg-white p-1 border-transparent h-10 cursor-pointer"
        placeholder=""
        v-model="form.background"
      />
      <small class="validation-error" v-if="errors.background">{{
        errors.background
      }}</small>
    </label>
    <label class="block col-span-2">
      <span class="text-lg">Couleur du text</span>
      <input
        type="color"
        class="mt-1 block w-full bg-white p-1 border-transparent h-10 cursor-pointer"
        placeholder=""
        v-model="form.font"
      />
      <small class="validation-error" v-if="errors.font">{{
        errors.font
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
import itemColor from "./item.vue";

export default {
  components: {
    itemColor
  },
  props: ["errors", "color", "edit"],
  data() {
    return {
      form: {
        background: "#0c5d89",
        font: "#FFFFFF"
      },
      editMode: false
    };
  },
  mounted() {
    if (this.$props.edit == true) {
      this.editMode = true;
      this.form = this.color;
    }
  },
  methods: {
    submit() {
      this.$inertia.post(route("colors.store"), this.form, {
        onSuccess: () => {
          this.$emit("closeForm");
        }
      });
    },
    update() {
      this.$inertia.put(route("colors.update", this.form.id), this.form);
    }
  }
};
</script>


