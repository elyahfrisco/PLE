<template>
  <form
    action=""
    :class="{
      'col-md-10 offset-md-1': !userComment.id
    }"
  >
    <h3 class="h5" v-if="userComment.id">Modification</h3>
    <h3 class="h5" v-else>Ajouter un commentaire</h3>

    <quill-editor
      v-model:value="userComment.content"
      :options="{
        placeholder: 'Saisissez votre commentaire....',
        height: 200,
        modules: {
          toolbar: [
            ['bold', 'italic'],
            [{ list: 'ordered' }, { list: 'bullet' }],
            [{ color: [] }, { background: [] }],
            [{ font: [] }],
            [{ align: [] }]
          ]
        }
      }"
      required
    />

    <small
      class="validation-error"
      v-if="
        userComment.content == null ||
          userComment.content.length == 0 ||
          errors.content
      "
      >* Le commentaire est requis</small
    >

    <div class="mt-1">
      <button
        class="btn btn-outline-success btn-sm"
        v-if="userComment.id"
        @click.prevent="update"
        :disabled="!userComment.content.length"
      >
        Enregistrer
      </button>
      <button
        class="btn btn-outline-success btn-sm"
        v-else
        @click.prevent="insert"
        :disabled="!userComment.content.length"
      >
        Ajouter
      </button>
    </div>
  </form>
</template>

<script>
export default {
  props: ["comment", "edit", "user_id"],
  data() {
    return {
      userComment: {
        content: "",
        user_id: null
      }
    };
  },
  beforeMount() {
    if (this.comment) {
      this.userComment = this.comment;
    }
    if (this.user_id) {
      this.userComment.user_id = this.user_id;
    }
  },
  methods: {
    insert() {
      this.$inertia.post(route("customers.comment.store"), this.userComment, {
        onSuccess: page => {
          this.userComment.content = "";
          this.iReload();
        },
        preserveScroll: true
      });
    },
    update() {
      this.$inertia.put(
        route("customers.comment.update", this.userComment.id),
        { content: this.userComment.content },
        {
          onSuccess: page => {
            this.$emit("comment-edit-saved");
          },
          preserveScroll: true
        }
      );
    }
  }
};
</script>


