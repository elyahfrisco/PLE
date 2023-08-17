<template>
  <inertia-data-table
    id="absences_table"
    :ajaxMode="true"
    :pagination="TableData.meta"
  >
    <template #header>
      <table-header name="created_at">Date de prevention</table-header>
      <table-header name="activity_session_time_start">Groupe</table-header>
      <table-header name="motif">Motif d'abscence</table-header>
      <table-header>Délai de prévention</table-header>
      <table-header name="first_name">Client</table-header>
      <table-header name="activity_name">Activité</table-header>
      <table-header name="pass_name">Pass</table-header>
      <table-header name="pass_name">Status</table-header>
      <table-header class="text-right" width="127px">Actions</table-header>
    </template>

    <template #content>
      <tr
        v-for="item in TableData.data"
        :key="item.id"
        class="subscriptions__activity"
        :class="item.session_status_txt + ' ' + item.presence_status_txt"
      >
        <td>
          {{ dateHFr(item.created_at) }}
        </td>
        <td>
          <a
            target="_blank"
            :href="
              route('establishments.plannings.sessions.participants', {
                establishment: item.establishment_id,
                activity_session: item.activity_session
              })
            "
            data-toggle="tooltip"
            title="Liste des participants"
          >
            {{ item.activity_session.group_name }}
          </a>
        </td>
        <td>{{ item.motif }} : {{ item.reason }}</td>
        <td>{{ item.prevention_time.human }}</td>
        <td>
          <inertia-link
            as="span"
            class="mr-1 pointer"
            :href="route('account.index', { user_id: item.user?.id })"
            >{{ item.user?.full_name }}</inertia-link
          >
        </td>
        <td>
          {{
            item.activity_session && item.activity_session.activity
              ? item.activity_session.activity.name
              : ""
          }}
        </td>
        <td>{{ item.pass ? item.pass.name : "" }}</td>
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
                    redirect_back: true
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
                    redirect_back: true
                  })
                "
                preserve-state
                preserve-scroll
                class="btn btn-danger btn-action btn-sm ml-1"
                data-toggle="tooltip"
                title="Annuler la demande de récupération"
                :onSuccess="() => $emitter.emit('refreshTable')"
                ><i class="fa fa-times"></i
              ></inertia-link>
              <btn-edit-recuperation
                class="btn-action btn-sm"
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
            class="btn-action btn-sm ml-1"
            :title="
              auth_user.role_name != 'admin'
                ? 'Donner de la récupération pour cette'
                : 'Récupérer la séance'
            "
            @request_recuperation="requestRecuperation(item)"
          />

          <inertia-link
            :onBefore="msgConfirm('Confirmer la suppression')"
            :onSuccess="() => this.$emitter.emit('refreshTable')"
            :href="route('absences.destroy', item.id)"
            method="DELETE"
            :preserve-scroll="true"
            class="btn btn-danger btn-sm btn-action btn-action-delete"
            data-toggle="tooltip"
            title="Supprimer"
          >
            <i class="fa fa-trash"></i> <slot />
          </inertia-link>
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
    InertiaDataTable,
    TableHeader,
    BtnRecuperation,
    BtnEditRecuperation,
    ModalRequestRecuperation
  },
  data() {
    return {
      activeSessionForRequestRecuperation: null
    };
  },
  methods: {
    requestRecuperation(data) {
      this.activeSessionForRequestRecuperation = data;
      setTimeout(() => {
        $("#modal-request-recuperation").modal();
      }, 100);
    }
  }
};
</script>
