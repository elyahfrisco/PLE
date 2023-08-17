<template>
  <app-layout>
    <template #pageTitle>Votre file d'attente</template>
    <!-- <queue-filter /> -->
    <span style="margin-top: 10px;">
      <hr />
    </span>
    <queue-table :TableData="queues" />
  </app-layout>
</template>

<script>
import QueueFilter from "@/Pages/Admin/Queue/Filter.vue";
import QueueTable from "./QueueDatatable.vue";
export default {
  components: {
    QueueTable,
    QueueFilter
  },
  data() {
    return { queues: {}, filter: {} };
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
          params: { ...this.filter, user_id: this.auth_user?.id }
        })
        .then(response => {
          if (response.data) {
            this.queues = this.getDataFilter(response.data.data);
            this.initTooltipe();
          } else {
            this.queues = [];
          }
          this.$emitter.emit("PageLoading", false);
        });
    },

    getDataFilter(datas) {
      return datas.filter(item => {
        return ((item.recuperation_request == null) && (new Date(item.subscription_activity.time_start) > moment(new Date()).subtract(3, 'h')._d));
      })
    }
  }
};
</script>


