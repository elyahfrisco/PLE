<template>
  <div class="table-responsive">
    <table id="table_id" class="display table datatable">
      <thead>
        <tr>
          <th
            v-for="label in labels"
            :key="label"
            :style="'width:' + (label.width ? label.width + 'px' : '')"
          >
            {{ label.label }}
          </th>
          <th style="width: 127px" class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in TableData" :key="item.id">
          <td>
            {{ dateFr(item.date_start) }}
            <template v-if="item.date_start != item.date_end">
              au {{ dateFr(item.date_end) }}
            </template>
          </td>
          <td class="text-capitalize">{{ item.reason }}</td>
          <td>{{ item.days_closing }}</td>
          <td class="column-actions">
            <btn-edit type="button" @click.prevent="editClosing(item)" />
            <btn-delete @click="deleteClosing(item.id)" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <jet-modal
    id="editClosing"
    title="Modifier le jour de fermeture"
    minHeight="425px"
  >
    <form-closing :closing="dataClosing"></form-closing>
  </jet-modal>
</template>

<script>
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import JetModal from "@/Jetstream/Modal";
import formClosing from "./form.vue";

export default {
  components: {
    BDropdown,
    JetDropdownLink,
    JetModal,
    formClosing
  },
  props: ["TableData"],
  data() {
    return {
      labels: [],
      dataClosing: Object
    };
  },
  beforeMount() {
    this.labels.push({ label: "Date" });
    this.labels.push({ label: "Motif" });
    this.labels.push({ label: "Nombre de jour" });
  },
  methods: {
    editClosing(data) {
      this.dataClosing = data;
      $("#editClosing").modal();
    },
    deleteClosing(id) {
      this.$inertia.delete(
        route("establishments.seasons.trimesters.closings.destroy", id),
        {
          onBefore: () => confirm("Supprimer jour de fermeture?")
        }
      );
    }
  }
};
</script>


