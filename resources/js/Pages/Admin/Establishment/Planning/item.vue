<template>
  <div
    class="p-2 my-1 text-center"
    :style="{
      'background-color': planning.activity.background_color
    }"
  >
    <!-- <p class="text-right mb-2">
      <span class="badge badge-light">T-{{ planning.trimester_num }}</span>
    </p> -->

    <p
      class="text-uppercase font-bold pointer activity-name hvr-grow"
      :style="{ color: planning.activity.font_color }"
      @click="showDetail()"
    >
      {{ planning.activity.name }}
    </p>

    <div class="rounded shadow-md p-2 detail-planning">
      <p class="h6 font-weight-bold">
        {{ planning.time_start }} - {{ planning.time_end }}
      </p>
      <div class="text-xs">
        <p>
          {{ dateFrMin(planning.start_at) }} au {{ dateFrMin(planning.end_at) }}
        </p>
        <div class="row">
          <div class="col-md-4">
            <span class="d-block">Séance</span
            >{{ planning.number_activity_sessions }}
          </div>
          <div class="col-md-4">
            <span class="d-block">Effectif</span>{{ planning.max_effective }}
          </div>
          <div class="col-md-4">
            <span class="d-block">Super-Pass</span>{{ planning.super_pass }}
          </div>
        </div>
      </div>
      <div class="d-flex justify-content-center">
        <inertia-link
          :href="
            route('establishments.plannings.edit', {
              establishment: planning.establishment_id,
              planning: planning.id
            })
          "
          class="btn btn-sm btn-success px-1 py-0 mr-1"
          data-toggle="tooltip"
          title="Modifier"
          ><i class="fa fa-edit"></i
        ></inertia-link>
        <!-- <br />
        <a
          v-if="planning.finished_at == null"
          :href="
            route('establishments.plannings.stop', {
              establishment: planning.establishment_id,
              planning_id: planning.id,
            })
          "
          class="underline"
          >Terminer</a
        > -->
        <br />
        <inertia-link
          :href="route('plannings.sessions.organize', planning.id)"
          class="btn btn-sm btn-warning px-1 py-0 mr-1"
          v-if="!planning.organized"
          data-toggle="tooltip"
          title="Générer sur le calendrier des semaines"
          ><i class="fa fa-calendar-check"></i
        ></inertia-link>
        <inertia-link
          :href="
            route(
              'establishments.plannings.sessions.index',
              planning.establishment_id
            )
          "
          class="btn btn-sm btn-info px-1 py-0 mr-1 text-white"
          data-toggle="tooltip"
          title="Voir le calendrier"
          v-else
          ><i class="fa fa-calendar"></i
        ></inertia-link>
        <a
          href="#"
          class="btn btn-sm btn-danger px-1 py-0 mr-1 text-white"
          data-toggle="tooltip"
          title="Supprimer"
          @click="deletePlanning"
          ><i class="fa fa-trash"></i
        ></a>
      </div>
    </div>
  </div>
  <activity-show :activity="planning.activity" :id="planning.activity.id" />
</template>

<script>
import ActivityShow from "../../Activity/show.vue";

export default {
  components: {
    ActivityShow
  },
  props: ["planning"],
  data() {
    return {
      activeShow: Object
    };
  },
  methods: {
    showDetail() {
      $("#activityShow" + this.planning.activity.id).modal();
    },
    deletePlanning() {
      this.$inertia.delete(
        route("establishments.plannings.destroy", {
          establishment: this.planning.establishment_id,
          planning: this.planning.id
        }),
        {
          onBefore: () => confirm("Supprimer le planning?"),
          onSuccess: () => this.iReload(this)
        }
      );
    }
  }
};
</script>

<style scoped>
.detail-planning {
  background: rgba(255, 255, 255, 0.75);
  font-weight: 550;
}
.detail-planning p {
  margin-bottom: 2px;
}
.activity-name {
  font-size: 14px;
  font-weight: 600;
}
</style>
