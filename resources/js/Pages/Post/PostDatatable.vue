<template>
  <inertia-data-table
    :searchable="true"
    id="table_relaunchs"
    :pagination="TableData"
  >
    <template #header>
      <table-header name="title">Titre</table-header>
      <table-header name="created_at">Date de création</table-header>
      <table-header>Extrait de Contenu</table-header>
      <table-header name="category_name">Categorie</table-header>
      <table-header name="author_name">Auteur</table-header>
      <th style="width: 127px" class="text-right">Actions</th>
    </template>

    <template #content>
      <template v-if="TableData.data">
        <tr v-for="item in TableData.data" :key="item.id">
          <td>{{ item.title }}</td>
          <td>{{ dateHFr(item.created_at) }}</td>
          <td v-html="item.content_min"></td>
          <td>{{ item.category.name }}</td>
          <td>{{ item.author.full_name }}</td>
          <td class="column-actions">
            <btn-show :href="route('posts.show', item.id)" />
            <btn-edit :href="route('posts.edit', item.id)" />
            <btn-delete @click="deletePost(item.id)" />
          </td>
        </tr>
      </template>
      <tr v-else>
        <td colspan="6" class="text-center">Aucun article à afficher</td>
      </tr>
    </template>
  </inertia-data-table>
</template>

<script>
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";

export default {
  components: {
    InertiaDataTable,
    TableHeader
  },
  props: ["TableData"],
  data() {
    return {
      activeShow: Object,
      subscriptionData: {}
    };
  },
  methods: {
    postRelaunch(data) {
      this.$inertia.delete(route("relaunchs.destroy", data.id), {
        onBefore: () => confirm("Supprimer la relance?"),
        onSuccess: () => this.iReload()
      });
    },
    deletePost(id) {
      this.$inertia.delete(route("posts.destroy", id), {
        onBefore: () => confirm("Supprimer l'article ?")
      });
    }
  }
};
</script>

<style scoped>
.ps {
  height: 100px;
}
</style>
