<template>
  <div class="p-3 bg-blue-400 my-1">
    {{ activity }}
    <br />

    <template v-if="showActionEstablishment">
      <a href="#" @click.prevent="deleteActivityEstablishment" class="underline"
        >Detacher du centre</a
      >
      <br />
      <a
        :href="
          route('establishments.activities.out_pass', {
            establishment: this.activity.pivot.establishment_id,
            activity_id: this.activity.id
          })
        "
        class="underline"
        >Hors Pass : {{ activity.pivot.out_pass ? "Oui" : "Non" }}</a
      >
      <!-- <a href="#" @click.prevent="setOutPassActivityEstablishment" class="underline"
        >Hors Pass : {{activity.pivot.out_pass?'Oui':'Non'}}</a
      > -->
    </template>
    <template v-else-if="showActionPass">
      <a href="#" @click.prevent="deleteActivityPass" class="underline"
        >Detacher du Pass</a
      >
      <template v-if="groups.length != 0 && activity.pivot.group_id == null">
        <br />
        Deplacer vers :
        <template v-for="group in groups" :key="group.date">
          <a
            href="#"
            @click.prevent="moveToGroup(group.id)"
            class="underline mr-2"
            >{{ group.name }}</a
          >,
        </template>
      </template>
    </template>
    <template v-else>
      <a :href="route('activities.edit', activity.id)" class="underline"
        >Modifier</a
      >
      <br />
      <a href="#" @click.prevent="deleteActivity" class="underline"
        >Supprimer</a
      >
    </template>
  </div>
</template>

<script>
export default {
  props: [
    "activityData",
    "viewEstablishment",
    "viewPass",
    "pass_id",
    "establishment_id",
    "groups",
    "index"
  ],
  data() {
    return {
      activity: Object,
      showActionEstablishment: false,
      showActionPass: false
    };
  },
  beforeMount() {
    this.activity = this.activityData;
    if (this.viewEstablishment) {
      this.showActionEstablishment = true;
    } else if (this.viewPass) {
      this.showActionPass = true;
    }
  },
  methods: {
    deleteActivity() {
      this.$inertia.delete(route("activities.destroy", this.activity.id), {
        onBefore: () => confirm("Supprimer activité?"),
        preserveScroll: true
      });
    },
    setOutPassActivityEstablishment() {
      var routeParameters = {
        establishment: this.activity.pivot.establishment_id,
        activity_id: this.activity.id
      };

      this.$inertia.post(
        route("establishments.activities.out_pass", routeParameters),
        {
          onBefore: () => confirm("Changer le statut de l'activité?")
        }
      );
    }
  }
};
</script>


