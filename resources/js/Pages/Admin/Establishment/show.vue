<template>
  <jet-modal id="establishmentShow" title="Détail du centre">
    <p class="text-capitalize">
      <strong>Nom: </strong>{{ establishment.name }}
    </p>
    <p><strong>Email: </strong>{{ establishment.email }}</p>
    <p><strong>Téléphone: </strong>{{ establishment.phone }}</p>
    <p><strong>Adresse: </strong>{{ establishment.address }}</p>
    <p><strong>CP: </strong>{{ establishment.postal_code }}</p>
    <p>
      <strong>Horaire: </strong>{{ establishment.start_time }} à
      {{ establishment.end_time }}
    </p>
    <p>
      <strong>Pôle détente: </strong
      ><span class="text-uppercase">{{
        establishment.relaxation_center ? "oui" : "non"
      }}</span>
    </p>
  </jet-modal>
</template>

<script>
import JetModal from "@/Jetstream/Modal";
export default {
  props: ["establishment"],
  components: {
    JetModal
  }
};
</script>


