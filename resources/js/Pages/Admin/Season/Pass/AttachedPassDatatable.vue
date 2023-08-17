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
        <tr v-for="item in TableData" :key="item.id" :id="'pass' + item.id">
          <td class="text-capitalize">{{ item.name }}</td>
          <td>{{ item.number_sessions }}</td>
          <td>
            <inertia-link
              :href="route('passes.activities', item)"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              ><i class="fa fa-list"></i> Activités (
              {{ item.activities_count }} )</inertia-link
            >
            <inertia-link
              v-if="!item.pass_trimester && !item.one_session"
              :href="
                route('establishments.seasons.passes.prices.index', {
                  establishment: season.establishment_id,
                  season_id: season.id,
                  pass_id: item.id
                })
              "
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              ><i class="fa fa-dollar-sign"></i> Prix du Pass</inertia-link
            >
            <inertia-link
              :href="
                route('subscriptions.periodes.index', {
                  establishment_id: season.establishment_id,
                  season_id: season.id,
                  pass_id: item.id
                })
              "
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              ><i class="fa fa-calendar-week"></i> Periode de
              souscription</inertia-link
            >
            <a
              @click.prevent="detachPassSeason(item)"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              ><i class="fa fa-unlink"></i> Détacher</a
            >
          </td>
          <td class="column-actions">
            <btn-edit :href="route('passes.edit', item.id)" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
  <jet-modal
    id="editSubscriptionPeriod"
    title="Parametrer période de souscription"
  >
    <form-subscription-parameter :subscriptionData="_subscriptionData" />
  </jet-modal>
</template>

<script>
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";

export default {
  components: {
    BDropdown,
    JetDropdownLink
  },
  props: ["TableData", "season"],
  data() {
    return {
      labels: [],
      _subscriptionData: {}
    };
  },
  beforeMount() {
    this.labels.push({ label: "Nom" });
    this.labels.push({ label: "Nombre des séances" });
    this.labels.push({ label: "Paramètres", width: "200" });
  },
  methods: {
    detachPassSeason(data) {
      var routeParameters = {
        season_id: data.pivot.season_id,
        pass_id: data.id
      };

      this.$inertia.delete(
        route("establishments.seasons.passes.detach", routeParameters),
        {
          onBefore: () => confirm("Détacher le Pass au centre?"),
          preserveScroll: true,
          onSuccess: () => {
            $("#pass" + data.id).hide();
          }
        }
      );
    },
    detachActivityPass(data) {
      var routeParameters = {
        pass_id: data.pivot.pass_id,
        activity_id: data.id
      };
    }
  }
};
</script>


