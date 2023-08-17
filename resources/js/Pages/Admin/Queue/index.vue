<template>
  <app-layout>
    <template #pageTitle>File d'attente</template>
    <queue-filter />
    <queue-table :TableData="queues" />
  </app-layout>
</template>

<script>
import QueueFilter from "./Filter.vue";
import QueueTable from "./QueueDatatable.vue";
export default {
  components: {
    QueueTable,
    QueueFilter
  },
  data() {
    return {
      queues: {},
      filter: {}
    };
  },
  mounted() {
    this.getQueue();

    this.$emitter.on("filterTable", q => {
      this.getQueue({ ...this.filter, ...q });
    });
    this.$emitter.on("sortTable", q => {
      this.getQueue({ ...this.filter, ...q });
    });
    this.$emitter.on("refreshTable", this.getQueue);
  },

  methods: {
    getQueue(filter = null) {
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

      axios
        .get(route("api.queue.list"), {
          params: this.filter
        })
        .then(response => {
          if (response.data) {
            this.queues = response.data.data;
            this.initTooltipe();
          } else {
            this.queues = [];
          }
          this.$emitter.emit("PageLoading", false);
        });
    }
  }
};
</script>


