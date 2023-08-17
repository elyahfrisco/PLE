<template>
  <app-layout>
    <template #pageTitle>Liste des actualités</template>
    <div class="row">
      <inertia-link
        :href="route('posts.create')"
        class="btn btn-success text-white mb-2 rounded-0"
      >
        <i class="fa fa-plus"></i> Ajouter une actualité
      </inertia-link>
    </div>
    <post-filter />
    <post-table :TableData="posts" />
  </app-layout>
</template>

<script>
import PostTable from "./PostDatatable.vue";
import PostFilter from "./Filter.vue";
export default {
  components: { PostTable, PostFilter },
  props: ["posts"]
};
</script>


