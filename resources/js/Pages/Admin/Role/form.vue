<template>
  <form action="" class="text-black">
    <label class="block">
      <span>Nom du type d'utilsateur</span>
      <input
        type="text"
        class="mt-1 block w-full rounded-md bg-gray-100
                border-transparent focus:border-gray-500 focus:bg-white
                focus:ring-0"
        placeholder="type d'utilisateur"
        v-model="form.name"
      />
      <small class="validation-error" v-if="errors.name">{{
        errors.name
      }}</small>
    </label>

    <button class="underline" v-if="form.id" @click.prevent="update">
      Metre à jour
    </button>
    <button v-else class="underline" @click.prevent="store">
      Enregistrer
    </button>
    <br />
  </form>
</template>

<script>
export default {
  props: {
    role: Object,
    errors: Object
  },
  data() {
    return {
      form: {
        name: ""
      }
    };
  },
  beforeMount() {
    if (this.role != undefined) {
      this.form = this.role;
    }
  }
};
</script>

