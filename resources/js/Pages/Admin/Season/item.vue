<template>
  <div class="p-3">
    <p>{{ season.year_start }} - {{ season.year_end }}</p>
    <p>{{ season.date_start }} - {{ season.date_end }}</p>
    <a
      :href="route('establishments.seasons.trimesters.index', season.id)"
      class="underline"
      >Trimestres</a
    >
    <br />
    <a
      :href="
        route('establishments.seasons.activities.index', {
          establishment: season.establishment_id,
          season_id: season.id
        })
      "
      class="underline"
      >Activités</a
    >
    <br />
    <a
      :href="
        route('establishments.seasons.passes.index', {
          establishment: season.establishment_id,
          season_id: season.id
        })
      "
      class="underline"
      >Passes</a
    >
    <br />
    <a :href="route('establishments.seasons.edit', season.id)" class="underline"
      >Modifier</a
    >
    <br />
    <a href="#" @click.prevent="deleteSeason" class="underline">Supprimer</a>
  </div>
</template>

<script>
export default {
  props: ["seasonData"],
  data() {
    return {
      season: {}
    };
  },
  beforeMount() {
    this.season = this.seasonData;
  },
  methods: {
    deleteSeason() {
      this.$inertia.delete(
        route("establishments.seasons.destroy", this.season.id),
        {
          onBefore: () => confirm("Supprimer saison?")
        }
      );
    }
  }
};
</script>


