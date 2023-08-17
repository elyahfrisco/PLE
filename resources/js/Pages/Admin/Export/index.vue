<template>
  <app-layout>
    <template #pageTitle>Liste des fichier exportés</template>
    <p
      v-if="$page.props.export_in_progress"
      class="alert text-white bg-default"
    >
      <span
        class="spinner-border spinner-info spinner-border-sm mr-2"
        role="status"
        aria-hidden="true"
      ></span>
      Exportation en cours de traitement
    </p>
    <p class="alert alert-info border-0 py-1">
      Les fichiers exportés seront affichés ici après la génération.
    </p>
    <export-table :TableData="exported_files" />
  </app-layout>
</template>

<script>
import ExportTable from "./ExportDatatable.vue";
export default {
  components: { ExportTable },
  props: ["exported_files"]
};
</script>


