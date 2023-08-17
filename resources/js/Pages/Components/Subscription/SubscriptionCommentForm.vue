<template>
  <form action="">
    <h3 class="h5" v-if="editMode">Modification</h3>
    <h3 class="h5" v-else>Ajouter un commentaire</h3>

    <quill-editor
      v-model:value="SubscriptionComment.content"
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

    <div class="mt-1 text-right">
      <template v-if="editMode">
        <button
          class="btn btn-success btn-sm"
          :disabled="!SubscriptionComment.content.length"
          @click.prevent="update"
        >
          Enregistrer
        </button>
        <inertia-link
          method="DELETE"
          preserve-state
          class="btn btn-danger btn-sm ml-2"
          preserve-scroll
          :onSuccess="
            () => {
              $emitter.emit(
                'get-subscription-comments' + comment.user_subscription_id
              );
            }
          "
          :href="route('subscriptions.comments.destroy', comment.id)"
          :onBefore="msgConfirm('Supprimer le commentaire?')"
          ><i class="fa fa-trash"></i> Supprimer</inertia-link
        >
        <button
          class="btn btn-secondary btn-sm ml-2"
          @click.prevent="$emitter.emit('cancel-subscription-comment-edit')"
        >
          Retour
        </button>
      </template>
      <button
        class="btn btn-outline-success btn-sm"
        v-else
        @click.prevent="insert"
        :disabled="!SubscriptionComment.content.length"
      >
        Ajouter
      </button>
    </div>
  </form>
</template>

<script>
export default {
  props: ["comment", "edit", "user_subscription_id"],
  data() {
    return {
      SubscriptionComment: {
        content: ""
      },
      editMode: false
    };
  },
  beforeMount() {
    if (this.$props.comment) {
      this.SubscriptionComment = this.comment;
      this.editMode = true;
    }
  },
  methods: {
    insert() {
      this.$inertia.post(
        route("subscriptions.comments.store", this.user_subscription_id),
        this.SubscriptionComment,
        {
          onSuccess: page => {
            this.SubscriptionComment.content = "";
            // this.iReload();
            this.$emitter.emit(
              "get-subscription-comments" + this.user_subscription_id
            );
          },
          preserveScroll: true,
          preserveState: true
        }
      );
    },
    update() {
      this.$inertia.put(
        route("subscriptions.comments.update", this.SubscriptionComment.id),
        { content: this.SubscriptionComment.content },
        {
          onSuccess: () => {
            this.$emit("update:comment", this.SubscriptionComment);
            this.$emitter.emit("cancel-subscription-comment-edit");
          },
          preserveScroll: true,
          preserveState: true
        }
      );
    }
  }
};
</script>
