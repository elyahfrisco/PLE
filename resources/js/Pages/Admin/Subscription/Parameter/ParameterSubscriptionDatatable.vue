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
          <td>{{ item.establishment.name.toUpperCase() }}</td>
          <td>{{ item.season.year_start }} - {{ item.season.year_end }}</td>
          <td>{{ item.pass.name.toUpperCase() }}</td>
          <td>{{ dateFr(item.start_at) }}</td>
          <td>{{ dateFr(item.end_at) }}</td>
          <td>
            <span class="badge badge-success" v-if="item.status == 'in_progress'">
              En cours
            </span>
            <span
              class="badge badge-secondary"
              v-else-if="item.status == 'futur'"
            >
              Terminé
            </span>
            <span class="badge badge-info" v-else> prochaine souscription </span>
          </td>
          <td>
            <b-dropdown btn="outline-secondary float-right py-0" sm="true">
              <template #title> Action </template>
              <template #content>
                <a
                  href="#"
                  class="dropdown-item"
                  @click.prevent="editParameterSubscription(item)"
                  ><i class="fa fa-edit"></i> Modifier</a
                >
              </template>
            </b-dropdown>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <jet-modal
    id="editParameterSubscription"
    title="Modifier la période de souscription"
  >
    <form-subscription-parameter :subscriptionData="subscriptionData" />
  </jet-modal>
</template>

<script>
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import formSubscriptionParameter from "./form.vue";

export default {
  components: {
    BDropdown,
    JetDropdownLink,
    formSubscriptionParameter
  },
  props: ["TableData"],
  data() {
    return {
      labels: [],
      activeShow: Object,
      subscriptionData: {}
    };
  },
  beforeMount() {
    this.labels.push({ label: "Centre" });
    this.labels.push({ label: "Saison" });
    this.labels.push({ label: "PASS" });
    this.labels.push({ label: "Date debut" });
    this.labels.push({ label: "Date fin" });
    this.labels.push({ label: "Statut" });
  },
  methods: {
    editParameterSubscription(data) {
      this.subscriptionData = data;
      $("#editParameterSubscription").modal();
    }
  }
};
</script>


