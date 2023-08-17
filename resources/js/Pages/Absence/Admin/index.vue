<template>
  <app-layout>
    <template #pageTitle>Liste des absences prévenues</template>
    <absence-filter />
    <absence-table :TableData="notified_absences" />
  </app-layout>
</template>

<script>
import AbsenceTable from "./AbsenceDataTable.vue";
import AbsenceFilter from "./Filter.vue";
export default {
  components: {
    AbsenceTable,
    AbsenceFilter
  },
  data() {
    return {
      notified_absences: {},
      filter: {
        establishment_id: null,
        user_id: null,
        minDate: null,
        maxDate: null,
        session_id: null
      },
      page: 1
    };
  },
  beforeMount() {
    this.$emitter.on("filterTable", q => {
      this.getNotifiedAbsences({ ...this.filter, ...q });
    });
    this.$emitter.on("sortTable", q => {
      this.getNotifiedAbsences({ ...this.filter, ...q });
    });
    this.$emitter.on("refreshTable", this.getNotifiedAbsences);
  },
  methods: {
    getNotifiedAbsences(filter = null) {
      if (filter !== null) {
        if (filter.q == "") {
          delete this.filter.q;
        }
        if (this.filter.q) {
          this.filter = { ...filter, q: this.filter.q };
        } else {
          this.filter = filter;
        }
      }
      axios.get(route("api.absences.notified", this.filter)).then(response => {
        this.notified_absences = response.data;
        this.$emitter.emit("PageLoading", false);
        this.initTooltipe();
      });
    }
  }
};
</script>


