<template>
  <jet-modal
    :id="'activityShow' + (id != null ? id : '')"
    title="Détail de l'activité"
  >
    <p class="text-capitalize"><strong>Nom: </strong><span class="text-uppercase">{{ activity.name }}</span></p>
    <p><strong>Description: </strong>{{ activity.description }}</p>
    <p>
      <strong>Activité de soin: </strong>{{ activity.care ? "OUI" : "NON" }}
    </p>
    <p>
      <strong>Activité pour enfant: </strong
      >{{ activity.for_kid ? "OUI" : "NON" }}
    </p>
    <p>
      <strong>Activité(s) de récupération: </strong>
      <span v-if="activity.activities_for_recuperation" class="text-uppercase">
        {{
          activity.activities_for_recuperation.reduce(
            (acc, activity) => acc + activity.name + ", ",
            ""
          )
        }}
      </span>
    </p>
    <div class="d-flex align-items-center">
      <div><strong>Affichage dans le planning:</strong></div>
      <div
        class="p-2 ml-2 text-center text-uppercase"
        :style="{
          'background-color': activity.background_color,
          color: activity.font_color,
        }"
      >
        {{ activity.name }}
      </div>
    </div>
  </jet-modal>
</template>

<script>
import JetModal from "@/Jetstream/Modal";
export default {
  props: ["activity", "id"],
  components: {
    JetModal,
  },
};
</script>


