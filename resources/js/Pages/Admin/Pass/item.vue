<template>
  <div class="">
    {{ pass }}
    <br />

    <template v-if="showActionEstablishment">
      <a href="#" @click.prevent="deletePassEstablishment" class="underline"
        >Detacher du centre</a
      >
      <br />
      <a :href="route('passes.edit', pass.id)" class="underline"> Modifier </a>
    </template>

    <template v-else-if="showActionSeason">
      <a href="#" @click.prevent="deletePassSeason" class="underline"
        >Detacher de la saison</a
      >
      <br />
      <a :href="route('passes.activities', pass.id)" class="underline"
        >Activités</a
      >
      <template v-if="!pass.one_session">
        <br />
        <a
          :href="
            route('establishments.seasons.passes.prices.index', {
              establishment: establishment_id,
              season_id: pass.pivot.season_id,
              pass_id: pass.id
            })
          "
          class="underline font-bold"
          >Prix</a
        >
      </template>
    </template>

    <template v-else>
      <a :href="route('passes.edit', pass.id)" class="underline"> Modifier </a>
      <br />
      <a :href="route('passes.activities', pass.id)" class="underline">
        Activités
      </a>
      <br />
      <a href="#" @click.prevent="deletePass" class="underline"> Supprimer </a>
    </template>
  </div>
</template>

<script>
export default {
  props: ["passData", "viewEstablishment", "viewSeason", "establishment_id"],
  data() {
    return {
      pass: Object,
      showActionEstablishment: false,
      showActionSeason: false
    };
  },
  beforeMount() {
    this.pass = this.passData;

    if (this.viewEstablishment) {
      this.showActionEstablishment = true;
    }
    if (this.viewSeason) {
      this.showActionSeason = true;
    }
  },
  methods: {
    deletePass() {
      this.$inertia.delete(route("passes.destroy", this.pass.id), {
        onBefore: () => confirm("Supprimer Pass?")
      });
    }
  }
};
</script>


