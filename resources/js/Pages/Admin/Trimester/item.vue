<template>
  <div class="col-md-4">
    <div class="card rounded-0">
      <div class="card-header bg-primary py-1">
        <div class="float-right">
          <a
            href="#"
            class="btn btn-sm btn-outline-light py-1 mx-1 mt-2"
            @click.prevent="editTrimester(trimester.id)"
            ><i class="fa fa-edit"></i
          ></a>
          <a
            href="#"
            class="btn btn-sm btn-outline-light p-1 mx-1 mt-2"
            v-if="this.is_last"
            @click="deleteTrimester"
            ><i class="fa fa-trash"></i
          ></a>
        </div>
        <p class="h2">Trimestre {{ trimester.num_trimester }}</p>
      </div>
      <div class="card-body">
        <p class="h4">{{ dateFr(trimester.date_start) }}</p>
        <p class="h5">au</p>
        <p class="h4">{{ dateFr(trimester.date_end) }}</p>
      </div>
      <!-- <div class="card-footer">
        <a
          :href="
            route(
              'closings.index',
              {
                  'trimester_id' : trimester.id,
              }
            )
          "
          class="small-box-footer text-success"
          ><i class="fa fa-cog mr-1"></i>Paramètre les jours des fermetures</a
        >
      </div> -->
    </div>
  </div>

  <jet-modal :id="'editTrimester' + trimester.id" title="Modifier trimestre">
    <form-trimester :trimesterData="trimester" />
  </jet-modal>
</template>

<script>
import formTrimester from "./form.vue";
import JetModal from "@/Jetstream/Modal";
export default {
  components: {
    formTrimester,
    JetModal
  },
  props: ["trimester", "is_last"],
  methods: {
    editTrimester(id) {
      $("#editTrimester" + id).modal();
    },
    deleteTrimester() {
      this.$inertia.delete(
        route("establishments.seasons.trimesters.destroy", this.trimester.id),
        {
          onBefore: () => confirm("Supprimer trimestre?")
        }
      );
    }
  }
};
</script>


