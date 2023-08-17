<template>
  <div class="card flat">
    <div class="card-header">
      <h3 class="card-title">
        <i class="ion ion-clipboard mr-1"></i>
        Liste journalière des relances prospects
      </h3>
    </div>
    <div class="card-body">
      <ul class="todo-list" data-widget="todo-list">
        <template v-for="item in relaunchs.prospects" :key="item.id">
          <li>
            <small class="badge badge-success">{{
              formatDate(item.created_at)
            }}</small>
            <span
              class="pointer text"
              @click="
                this.$inertia.visit(
                  route('account.index', { user_id: item.user_id })
                )
              "
              >{{ item.name }}</span
            >
          </li>
        </template>
      </ul>
    </div>
  </div>
</template>

<script>
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";

export default {
  components: {
    InertiaDataTable,
    TableHeader
  },
  data() {
    return {
      relaunchs: {
        prospects: []
      },
      date: {
        prospect: this.dateAng()
      }
    };
  },
  mounted() {
    this.getProspectRelaunch();
  },
  methods: {
    getProspectRelaunch() {
      axios
        //.get(route("api.relaunchs.list"), {
        .get(route("api.statistic.prospects.list"), {
          params: {}
        })
        .then(response => {
          this.relaunchs.prospects = response.data;
        });
    },
    SendNow(id) {
      msgConfirm("Êtes-vous sûr d'envoyer la relance maintenant ?")
        ? axios
            .get(route("api.relaunch.send_now"), {
              params: {
                relaunch_id: id
              }
            })
            .then(response => {
              this.getProspectRelaunch();
            })
        : null;
    },
    formatDate(value) {
      if (value) {
        return this.$moment(String(value)).format("DD/MM/YYYY");
      }
    }
  }
};
</script>


