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
          <td>{{ item.num_trimester }}</td>
          <td>{{ dayfr($moment(item.date_start).format("dddd")) }}</td>
          <td>{{ dateFr(item.date_start) }}</td>
          <td>{{ dateFr(item.date_end) }}</td>
          <td>{{ item.week_count }}</td>
          <td class="column-actions">
            <btn-edit type="button" @click.prevent="editTrimester(item)" />
            <btn-delete @click="deleteTrimester(item.id)" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <jet-modal id="editTrimester" title="Modifier trimestre">
    <form-trimester :trimesterData="trimesterData" />
  </jet-modal>
</template>

<script>
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import formTrimester from "./form.vue";

export default {
  components: {
    BDropdown,
    JetDropdownLink,
    formTrimester
  },
  props: ["TableData"],
  data() {
    return {
      labels: [],
      activeShow: Object,
      trimesterData: {}
    };
  },
  beforeMount() {
    this.labels.push({ label: "Num trimestre", width: "20" });
    this.labels.push({ label: "Jour" });
    this.labels.push({ label: "Date debut" });
    this.labels.push({ label: "Date fin" });
    this.labels.push({ label: "Nombre de semaine" });
  },
  methods: {
    editTrimester(data) {
      this.trimesterData = data;
      $("#editTrimester").modal();
    },
    deleteTrimester(id) {
      this.$inertia.delete(
        route("establishments.seasons.trimesters.destroy", id),
        {
          onBefore: () => confirm("Supprimer trimestre?")
        }
      );
    }
  }
};
</script>


