<template>
  <app-layout>
    <template #pageTitle><span v-html="this.title"></span></template>
    <div class="row">
      <inertia-link
        :href="route('customers.create', params)"
        class="btn btn-success rounded-0"
      >
        <i class="fa fa-plus"></i> Ajouter
        {{ account_type == "customer" ? "Client" : "Prospect" }}
      </inertia-link>
    </div>
    <customer-filter
      class="mt-3"
      ref="customer_filter"
      :account_type="account_type"
    />
    <div class="row mx-0">
      <inertia-link
        v-if="account_type != 'prospect' && account_type != 'attente'"
        :href="route('customers.index') + '?birthdate=' + dateBirth"
        class="ml-auto"
        :class="'text-' + (anniversaryCount ? 'danger' : 'secondary')"
      >
        <i class="fa fa-gift"></i> Anniversaires ( {{ anniversaryCount }} )
      </inertia-link>
    </div>
    <!-- <vue-excel-xlsx
      :data="customersList.data"
      :columns="columns"
      :file-name="'Liste'"
      :file-type="'xlsx'"
      :sheet-name="'sheetname'"
      :class="'btn btn-success ml-2'"
    >
      Exporter la liste affiché
    </vue-excel-xlsx> -->
    <inertia-link
      :href="
        route('customers.export', {
          _query: {
            filterBy: this.$refs.customer_filter?.form,
            account_type,
            q: this.q_.q,
            birthdate: this.q_.birthdate
          }
        })
      "
      preserve-state
      preserve-scroll
      class="btn btn-success"
    >
      <i class="fa fa-file-export"></i> Exporter
    </inertia-link>
    <customer-table :TableData="customersList" :account_type="account_type" />
  </app-layout>
</template>

<script>
import customerTable from "./CustomerDataTable.vue";
import CustomerFilter from "./Filter.vue";
import { Inertia } from "@inertiajs/inertia";

export default {
  components: {
    customerTable,
    CustomerFilter
  },
  props: ["customers", "title", "account_type"],
  data() {
    return {
      customersList: {},
      dateBirth: null,
      anniversaryCount: 0,
      params: {},
      columns: [],
      in_progress: false,
      export_info: {}
    };
  },
  beforeMount() {
    Inertia.on("start", () => {
      this.in_progress = true;
    });

    this.customers.data.map(c => {
      c.user_establishments = [];
      c.user_activities = [];
      return c;
    });

    this.customersList = this.customers;

    this.dateBirth = this.formatDate(new Date());
    this.params = { account_type: this.account_type };
    axios
      .get(route("customers.birthday.count"), {
        params: {
          birthdate: this.dateBirth
        }
      })
      .then(response => {
        this.anniversaryCount = response.data;
      });
  },
  mounted() {
    let delay = 200;
    if (this.account_type == "customer") {
      (async () => {
        let i = 0;
        for await (let customer of this.customersList.data) {
          if (this.in_progress) break;
          await this.get_user_activities(i);
          await this.get_user_establishments(i++);
        }
      })();

      this.columns = [
        {
          label: "Code",
          field: "id"
        },
        {
          label: "Date de création",
          field: "created_at",
          dataFormat: this.dateHFr
        },
        {
          label: "Prenom et Nom",
          field: "full_name"
        },
        {
          label: "Centre",
          field: "user_establishments",
          dataFormat: this.formatCentre
        },
        {
          label: "Email",
          field: "email"
        },
        {
          label: "Activités",
          field: "user_activities",
          dataFormat: this.formatActivite
        }
      ];
    } else if (
      this.account_type == "prospect" ||
      this.account_type == "attente"
    ) {
      (async () => {
        let i = 0;
        for await (let customer of this.customersList.data) {
          if (this.in_progress) break;
          await this.get_user_activities(i++, true);
        }
      })();

      this.columns = [
        {
          label: "Code",
          field: "id"
        },
        {
          label: "Date de création",
          field: "created_at",
          dataFormat: this.dateHFr
        },
        {
          label: "Prenom et Nom",
          field: "full_name"
        },
        {
          label: "Email",
          field: "email"
        },
        {
          label: "Activités souhaités",
          field: "user_activities",
          dataFormat: this.formatActivite
        }
      ];
    }
  },
  methods: {
    get_active_filters() {
      console.log(this.$refs.customer_filter.form);

      return false;
    },
    async get_user_activities(index, wished = false) {
      await axios
        .get(route("api.user.activities"), {
          params: {
            user_id: this.customersList.data[index].user_id,
            wished: wished ? 1 : 0
          }
        })
        .then(response => {
          this.customersList.data[index].user_activities =
            response.data != undefined ? response.data : [];
        });
    },
    async get_user_establishments(index) {
      await axios
        .get(route("api.user.establishments"), {
          params: {
            user_id: this.customersList.data[index].user_id
          }
        })
        .then(response => {
          this.customersList.data[index].user_establishments =
            response.data != undefined ? response.data : [];
        });
    },

    formatDate(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;

      return [year, month, day].join("-");
    },
    dateHFr(date) {
      return this.$moment(date).format("DD/MM/YYYY HH:mm");
    },
    formatActivite(data) {
      var act = "";
      data.forEach(value => {
        act += value.name + " " + value.group_name + ", \n";
      });
      return act;
    },
    formatCentre(data) {
      var cent = "";
      data.forEach(value => {
        cent += value.name + ", \n";
      });
      return cent;
    }
  }
};
</script>
