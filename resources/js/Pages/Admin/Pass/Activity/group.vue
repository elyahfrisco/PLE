<template>
  <div class="hvr-grow w-100">
    <div class="card rounded-0 pointer" @click="showGroup(group.id)">
      <div class="card-body">
        <div>
          Groupe :
          <label for="" class="mb-0">{{ group.name.toUpperCase() }}</label>
        </div>
        <div class="d-block">
          {{ group.number_session }} séance(s)
          <span class="font-weight-bold">{{
            group.select_mode == "or" ? "OU" : "ET"
          }}</span>
        </div>
      </div>
    </div>
  </div>

  <jet-modal :id="'showGroup' + group.id" :title="'Groupe: ' + group.name">
    <div class="row">
      <button
        @click="editGroup(group.id)"
        class="btn btn-sm py-0 ml-auto btn-success"
      >
        <i class="fa fa-edit"></i>
      </button>
      <button @click="deleteGroup" class="btn btn-sm py-0 ml-1 btn-danger">
        <i class="fa fa-trash"></i>
      </button>
    </div>

    <div
      class="d-block"
      v-for="activity in group.activities"
      :key="activity.id"
    >
      {{ activity.name }} (
      <a
        href="#"
        @click.prevent="detachActivity(activity.id)"
        class="btn btn-sm btn-outline-success py-0"
        >Retirer</a
      >
      )
    </div>
  </jet-modal>

  <jet-modal :id="'editGroup' + group.id" title="Ajouter Groupe d'activités">
    <form-group
      class="mt-3"
      :group="group"
      :pass_id="group.pass_id"
      :number_session="group.number_session"
    />
  </jet-modal>
</template>

<script>
import formGroup from "./form_group.vue";
import JetModal from "@/Jetstream/Modal";

export default {
  props: ["group"],
  components: { formGroup, JetModal },
  methods: {
    showGroup(id) {
      $("#showGroup" + id).modal();
    },
    editGroup(id) {
      $("#editGroup" + id).modal();
    },
    deleteGroup() {
      this.$inertia.delete(
        route("passes.activities.groups.delete", {
          group: this.group.id
        }),
        {
          onBefore: () => confirm("Suppirmer le groupe d'activité?"),
          preserveScroll: true
        }
      );
    },
    detachActivity(activity_id) {
      this.$inertia.put(
        route("passes.activities.groups.detach", {
          pass_id: this.group.pass_id,
          activity_id: activity_id,
          group_id: this.group.id
        }),
        this.form,
        {
          onSuccess: () => {
            this.iReload(this);
          }
        }
      );
    }
  }
};
</script>


