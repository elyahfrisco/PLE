<template>
  <app-layout>
    <template #pageTitle>Activités pass: {{ this.pass.name }}</template>
    <div class="row">
      <label>Nombre de séances Pass : {{ this.pass.number_sessions }}</label>
    </div>

    <div class="row" v-if="!pass.pass_trimester && !pass.one_session">
      <a
        href="#"
        class="btn btn-success text-white mb-2 rounded-0"
        @click="addGroup"
      >
        <i class="fa fa-plus"></i> Ajouter un groupe d'activités
      </a>
    </div>

    <div class="">
      <div class="row" v-if="!pass.pass_trimester && !pass.one_session">
        <div
          class="col-md-3 pl-0"
          v-for="group in this.activitiesGroups"
          :key="group.id"
        >
          <item-group :group="group"></item-group>
        </div>
      </div>

      <activity-table
        :TableData="activitiesList"
        :groups="activities_groups"
        :pass="pass"
      />

      <!-- Not attached activities -->
      <item-activity-unattached
        :activitiesNotAttached="activitiesNotAttached"
        :pass_id="this.pass.id"
        v-if="activitiesNotAttached.length != 0"
      ></item-activity-unattached>
    </div>

    <jet-modal id="addGroup" title="Ajouter Groupe d'activités">
      <form-group :pass_id="pass.id" :number_session="pass.number_session" />
    </jet-modal>
  </app-layout>
</template>

<script>
import JetModal from "@/Jetstream/Modal";
import itemActivityUnattached from "./unattached.vue";
import itemGroup from "./group.vue";
import ActivityTable from "./AttachedActivityDatatable.vue";
import formGroup from "./form_group.vue";

export default {
  components: {
    itemActivityUnattached,
    itemGroup,
    JetModal,
    ActivityTable,
    formGroup
  },
  props: ["pass", "activities", "activitiesNotAttached", "activities_groups"],
  data() {
    return {
      activitiesGroups: [],
      activitiesList: Array,
      name_group_error: false
    };
  },
  beforeMount() {
    this.activitiesList = this.activities;
    this.activitiesGroups = this.activities_groups;
  },
  methods: {
    addGroup() {
      $("#addGroup").modal();
    }
  }
};
</script>
