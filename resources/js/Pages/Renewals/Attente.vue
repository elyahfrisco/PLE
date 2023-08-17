<template>
  <app-layout>
    <template #pageTitle>Liste d'attente renouvellement</template>
    <filter-renewal ref="renewal_filter" />
    <!-- <vue-excel-xlsx
      :data="users"
      :columns="columns"
      :file-name="'Liste'"
      :file-type="'xlsx'"
      :sheet-name="'sheetname'"
      :class="'btn btn-success ml-2'"
    >
      Exporter
    </vue-excel-xlsx> -->

    <inertia-link
      :href="
        route('attente.renouvellement', {
          _query: {
            filterBy: this.$refs.renewal_filter?.form,
            export: true,
            q: this.q_.q
          }
        })
      "
      preserve-state
      preserve-scroll
      class="btn btn-success"
    >
      <i class="fa fa-file-export"></i> Exporter
    </inertia-link>

    <renewal-table :TableData="users" />
  </app-layout>
</template>

<script>
import RenewalTable from "./RenewalDataTableAttente";
import FilterRenewal from "./FilterAttente";

export default {
  components: {
    FilterRenewal,
    RenewalTable
  },
  props: ["users"],
  data() {
    return {
      columns: [
        {
          label: "Mis à jour",
          field: "updated_at",
          dataFormat: this.dateHFr
        },
        {
          label: "Prenom et nom",
          field: "name"
        },
        {
          label: "Centre",
          field: "establishment"
        },
        {
          label: "Activités souhaités",
          field: "activities",
          dataFormat: this.formatActivite
        },
        {
          label: "Status",
          field: "renewal_status",
          dataFormat: this.formatStatus
        }
      ]
    };
  },
  methods: {
    dateHFr(date) {
      return this.$moment(date).format("DD/MM/YYYY HH:mm");
    },
    formatActivite(data) {
      var act = "";
      data.forEach(value => {
        act += value[0] + " " + value[1] + ", \n";
      });
      return act;
    },
    formatStatus(data) {
      return "continue_change_time_else_stop" == data
        ? "Continuer et changer horaire sinon STOP"
        : "Continuer et changer horaire";
    }
  }
};
</script>


