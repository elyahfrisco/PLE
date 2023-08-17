<template>
  <app-layout>
    <template #pageTitle><span v-html="this.title"></span></template>
    <div class="row">
      <inertia-link
        :href="route('customers.create', params)"
        class="btn btn-success rounded-0"
      >
        <i class="fa fa-plus"></i> Ajouter
        {{ prospectListe ? "Prospect" : "Client" }}
      </inertia-link>
    </div>
    <div class="row mx-0">
      <inertia-link
        v-if="!prospectListe"
        :href="route('customers.index') + '?birthdate=' + dateBirth"
        class="ml-auto"
        :class="'text-' + (anniversaryCount ? 'danger' : 'secondary')"
      >
        <i class="fa fa-gift"></i> Anniversaires ( {{ anniversaryCount }} )
      </inertia-link>
    </div>
    <customer-table
      :TableData="this.customersList"
      :prospectListe="prospectListe"
    />
  </app-layout>
</template>

<script>
import profilPhoto from "@/Pages/Components/profilPhoto.vue";

export default {
  components: {
    profilPhoto
  },
  props: ["customers", "title", "prospectListe"],
  data() {
    return {
      customersList: this.customers,
      dateBirth: null,
      anniversaryCount: 0,
      params: {}
    };
  },
  beforeMount() {
    this.dateBirth = this.formatDate(new Date());
    if (this.prospectListe) {
      this.params = { account_type: "prospect" };
    }
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
  methods: {
    formatDate(date) {
      var d = new Date(date),
        month = "" + (d.getMonth() + 1),
        day = "" + d.getDate(),
        year = d.getFullYear();

      if (month.length < 2) month = "0" + month;
      if (day.length < 2) day = "0" + day;

      return [year, month, day].join("-");
    }
  }
};
</script>
