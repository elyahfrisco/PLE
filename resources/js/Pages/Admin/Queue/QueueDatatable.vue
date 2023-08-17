<template>
  <inertia-data-table id="queus_table" :ajaxMode="true" :pagination="TableData">
    <template #header>
      <table-header name="created_at">Ajouté le</table-header>
      <!-- <table-header>Type</table-header> -->
      <table-header>Client</table-header>
      <table-header name="activity_name">Séance à récupérer</table-header>
      <table-header>Séance de récupération</table-header>
      <table-header name="pass_name">Pass</table-header>
      <table-header>Priorité</table-header>
      <table-header>Absence</table-header>
      <table-header name="presence_confirmed_at">Status</table-header>
      <table-header class="text-right" width="127px">Actions</table-header>
    </template>
    <template #content>
      <tr v-for="item in TableData" :key="item.id">
        <td>{{ dateHFr(item.created_at) }}</td>
        <!-- <td>
          <span v-if="item.type == 'for_recuperation'">Pour recuperation</span>
          <span v-else>{{ item.type }}</span>
        </td> -->
        <td class="text-capitalize">
          <inertia-link
            as="span"
            class="mr-1 pointer"
            :href="route('account.index', { user_id: item.user.id })"
            >{{ item.user.full_name }}</inertia-link
          >
        </td>
        <td>
          <p class="text-uppercase mb-1">
            {{ item.subscription_activity.session.activity.name }}
          </p>
          <a
            target="_blank"
            :href="
              route('establishments.plannings.sessions.participants', {
                establishment: item.subscription_activity.establishment_id,
                activity_session: item.activity_session_id
              })
            "
            data-toggle="tooltip"
            title="Liste des participants"
          >
            {{ dateFr(item.subscription_activity.date) }}
            <br />
            {{ item.subscription_activity.schedule.start }} à
            {{ item.subscription_activity.schedule.end }}
          </a>
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

            <a
              target="_blank"
              :href="
                route('establishments.plannings.sessions.participants', {
                  establishment:
                    item.recuperation_request.session_for_catch_up
                      .establishment_id,
                  activity_session:
                    item.recuperation_request.activity_session_id_for_catch_up
                })
              "
              data-toggle="tooltip"
              title="Liste des participants"
            >
              {{ dateFr(item.recuperation_request.session_for_catch_up.date) }}
              <br />
              {{ H(item.recuperation_request.session_for_catch_up.time_start) }}
              à
              {{ H(item.recuperation_request.session_for_catch_up.time_end) }}
            </a>
          </template>
        </td>

        <td>{{ item.subscription_activity.user_subscription.pass.name }}</td>
        <td>
          <span v-if="item.priority == 0">Normal</span>
          <span v-else>{{ item.priority }}</span>
        </td>
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
                preserve-state
                preserve-scroll
                class="btn btn-success btn-action btn-sm"
                data-toggle="tooltip"
                title="Confirmer la présence"
                :onSuccess="() => $emitter.emit('refreshTable')"
                ><i class="fa fa-check"></i
              ></inertia-link>
              <inertia-link
                :href="
                  route('recuperation_requests.cancel', {
                    id: item.recuperation_request.id,
                    admin: true
                  })
                "
                preserve-state
                preserve-scroll
                class="btn btn-danger btn-action btn-sm"
                data-toggle="tooltip"
                title="Annuler la demande de récupération"
                :onSuccess="() => $emitter.emit('refreshTable')"
                ><i class="fa fa-times"></i
              ></inertia-link>
              <btn-edit-recuperation
                v-if="$can('edit_recuperation_request')"
                :queue_id="item.recuperation_request.queue_id"
                :emit_after_save="true"
                @recuperation_request_saved="
                  () => $emitter.emit('refreshTable')
                "
              />
            </template>
          </template>
          <btn-recuperation
            v-else
            :title="
              auth_user.role_name != 'admin'
                ? 'Donner de la récupération pour cette'
                : 'Récupérer la séance'
            "
            @request_recuperation="() => requestRecuperation(item)"
          />
        </td>
      </tr>
    </template>
  </inertia-data-table>
  <modal-request-recuperation
    v-if="activeSessionForRequestRecuperation"
    :sessionInQueue="activeSessionForRequestRecuperation"
    :emit_after_save="true"
    @recuperation_request_saved="() => $emitter.emit('refreshTable')"
  />
</template>

<script>
import BtnRecuperation from "@/Pages/Components/Planning/BtnRecuperation.vue";
import BtnEditRecuperation from "@/Pages/Components/Planning/BtnEditRecuperation.vue";
import ModalRequestRecuperation from "@/Pages/Components/ModalRequestRecuperation.vue";

import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";

export default {
  props: ["TableData"],
  components: {
    BtnRecuperation,
    BtnEditRecuperation,
    ModalRequestRecuperation,
    InertiaDataTable,
    TableHeader
  },
  data() {
    return {
      activeSessionForRequestRecuperation: null
    };
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


