<template>
  <app-layout>
    <template #pageTitle>{{ post.title }}</template>
    <div class="post-container">
      <div class="post-header">
        <h5 class="post-date ml-3 pl-3">
          <i>{{ post.created_at_fr }}</i>
        </h5>
        <template v-if="auth_user.role_name == 'admin'">
          <btn-edit :href="route('posts.edit', post.id)">Modifier</btn-edit>
          <btn-delete @click="deletePost(post.id)">Supprimer</btn-delete>
        </template>
      </div>
      <div class="post-content m-3 p-3 ck-content" v-html="post.content"></div>
    </div>
  </app-layout>
</template>

<script>
export default {
  name: "PostView",
  props: ["post"],
  methods: {
    deletePost(id) {
      this.$inertia.delete(route("posts.destroy", id), {
        onBefore: () => confirm("Supprimer l'article ?")
      });
    }
  }
};
</script>


