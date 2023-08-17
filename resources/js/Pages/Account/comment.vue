<template>
  <div class="py-1 my-2" v-if="!comment_deleted">
    <p
      v-if="!edit_comment"
      class="mb-1 ck-content"
      v-html="comment.content"
    ></p>
    <comment-form
      v-if="edit_comment"
      :comment="comment"
      :user_id="comment.user_id"
      v-on:comment-edit-saved="commentEditedSaved"
    ></comment-form>

    <div class="py-2 text-red-400" v-if="commentEdited">
      Commentaire modifié
    </div>

    <div class="row mx-0">
      <small class="ml-auto">{{ dateHFr(comment.created_at) }}</small>
    </div>

    <div class="actions">
      <button
        v-if="!edit_comment"
        @click="edit_comment = true"
        class="btn btn-outline-success btn-sm mr-2 py-0"
      >
        Modifier
      </button>
      <button
        @click="deleteComment(comment.id)"
        class="btn btn-outline-danger btn-sm py-0"
      >
        Supprimer
      </button>
      <button
        v-if="edit_comment"
        @click="() => (this.edit_comment = false)"
        class="btn btn-outline-secondary btn-sm ml-2 py-0"
      >
        Annuler
      </button>
    </div>
    <hr />
  </div>
  <div class="bg-red-300 py-3 text-1xl" v-else>Commentaire supprimer</div>
</template>

<script>
import commentForm from "./commentForm.vue";

export default {
  components: {
    commentForm
  },
  props: ["comment"],
  data() {
    return {
      edit_comment: false,
      commentEdited: false,
      comment_deleted: false
    };
  },
  methods: {
    deleteComment(comment_id) {
      this.$inertia.delete(route("customers.comment.delete", comment_id), {
        onBefore: () => confirm("Supprimer commentaire?"),
        onSuccess: page => {
          this.comment_deleted = true;
        },
        preserveScroll: true
      });
    },
    commentEditedSaved() {
      this.commentEdited = true;
      this.edit_comment = false;
    }
  }
};
</script>


