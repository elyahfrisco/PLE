<template>
  <table id="table_id" class="display table datatable_">
    <thead>
      <tr>
        <th
          v-for="label in labels"
          :key="label"
          :style="'width:' + (label.width ? label.width + 'px' : '')"
        >
          {{ label.label }}
        </th>
        <th style="width: 127px !important" class="text-right">Actions</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="item in TableData" :key="item.id">
        <td>{{ dateHFr(item.created_at) }}</td>
        <td>
          <span v-if="item.type == 'for_recuperation'">Pour recuperation</span>
          <span v-else>{{ item.type }}</span>
        </td>
        <td>
          <p class="text-uppercase mb-1">
            {{ item.subscription_activity.session.activity.name }}
          </p>
          {{ dateFr(item.subscription_activity.date) }}
          <br />
          {{ item.subscription_activity.schedule.start }} à
          {{ item.subscription_activity.schedule.end }}
        </td>

        <td>
          <template
            v-if="
              item.recuperation_request &&
                item.recuperation_request.session_for_catch_up
            "
          >
            <p class="text-uppercase mb-1">
              {{ item.recuperation_request.session_for_catch_up.activity.name }}
            </p>
            {{ dateFr(item.recuperation_request.session_for_catch_up.date) }}
            <br />
            {{ H(item.recuperation_request.session_for_catch_up.time_start) }} à
            {{ H(item.recuperation_request.session_for_catch_up.time_end) }}
          </template>
        </td>
        <td>{{ item.subscription_activity.user_subscription.pass.name }}</td>
        <td>
          <p class="mb-0" v-if="item.subscription_activity.absence_prevention">
            Prevenu le :
            {{
              dateHFr(item.subscription_activity.absence_prevention.created_at)
            }}
            (
            {{
              item.subscription_activity.absence_prevention
                .ElapseTimeBeforeStart
            }}
            )
          </p>
          <p class="mb-0" v-if="item.subscription_activity.can_catch_up_until">
            Peut se rattraper jusqu'au :
            {{ dateFr(item.subscription_activity.can_catch_up_until) }}
          </p>
        </td>
        <td>
          <template v-if="item.recuperation_request">
            <template v-if="item.recuperation_request.presence_confirmed_at">
              <span class="badge badge-success">Presence confirmée</span>
              <br />
              <span class="badge badge-success">{{
                dateHFr(item.recuperation_request.presence_confirmed_at)
              }}</span>
            </template>
            <template v-else>
              <span class="badge badge-warning">Presence non confirmée</span>
            </template>
          </template>
        </td>
        <td class="column-actions">
          <template v-if="item.recuperation_request">
            <template v-if="!item.recuperation_request.presence_confirmed_at">
              <inertia-link
                :href="
                  route('recuperation_requests.presence.confirm', {
                    id: item.recuperation_request.id,
                    admin: true
                  })
                "
                class="btn btn-success btn-action btn-sm"
                data-toggle="tooltip"
                title="Confirmer la présence"
                :onSuccess="() => this.iReload"
                ><i class="fa fa-check"></i
              ></inertia-link>
              <inertia-link
                :href="
                  route('recuperation_requests.cancel', {
                    id: item.recuperation_request.id,
                    admin: true
                  })
                "
                class="btn btn-danger btn-action btn-sm"
                data-toggle="tooltip"
                title="Annuler la demande de récupération"
                :onSuccess="() => this.iReload"
                ><i class="fa fa-times"></i
              ></inertia-link>
              <btn-edit-recuperation
                v-if="$can('edit_recuperation_request')"
                :queue_id="item.recuperation_request.queue_id"
              />
            </template>
          </template>
          <btn-recuperation
            v-else
            title="Envoyer une demande de rattrapage"
            @request_recuperation="requestRecuperation(item)"
          />
        </td>
      </tr>
    </tbody>
  </table>
  <modal-request-recuperation
    v-if="activeSessionForRequestRecuperation"
    :sessionInQueue="activeSessionForRequestRecuperation"
  />
</template>

<script>
import BtnRecuperation from "@/Pages/Components/Planning/BtnRecuperation.vue";
import ModalRequestRecuperation from "@/Pages/Components/ModalRequestRecuperation.vue";
import BtnEditRecuperation from "@/Pages/Components/Planning/BtnEditRecuperation.vue";

export default {
  props: ["TableData"],
  components: {
    BtnRecuperation,
    ModalRequestRecuperation,
    BtnEditRecuperation
  },
  data() {
    return {
      activeSessionForRequestRecuperation: null,
      labels: []
    };
  },
  beforeMount() {
    this.labels.push({ label: "Ajouté le" });
    this.labels.push({ label: "Type" });
    this.labels.push({ label: "Séance à récupérer" });
    this.labels.push({ label: "Séance de récupération" });
    this.labels.push({ label: "Pass" });
    this.labels.push({ label: "Absence" });
    this.labels.push({ label: "Status" });
  },
  methods: {
    showDetail(data) {
      this.activeQueueDetail = data;
      $("#modal-queue-detail").modal();
    },
    requestRecuperation(data) {
      this.activeSessionForRequestRecuperation = data;
      setTimeout(() => {
        $("#modal-request-recuperation").modal();
      }, 100);
    }
  }
};
</script>


