<template>
  <div v-if="!editClosing">
    <pre>{{ this.closing }}</pre>
    <a href="#" class="underline" @click="editClosing = true">Modifier</a>
    <br />
    <a href="#" class="underline" @click="deleteClosing">Supprimer</a>
  </div>
  <form-closing
    v-else
    :closing="this.closing"
    @close_closing_form="editClosing = false"
  ></form-closing>
</template>

<script>
import formClosing from "./form.vue";
export default {
  components: {
    formClosing
  },
  props: {
    closing: Object
  },
  data() {
    return {
      editClosing: false
    };
  },
  methods: {
    deleteClosing() {
      this.$inertia.delete(
        route(
          "establishments.seasons.trimesters.closings.destroy",
          this.closing.id
        ),
        {
          onBefore: () => confirm("Supprimer jour de fermeture?")
        }
      );
    }
  }
};
</script>


