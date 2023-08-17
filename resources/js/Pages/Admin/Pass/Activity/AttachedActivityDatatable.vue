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
        <tr v-for="item in TableData" :key="item.id" :id="'activity' + item.id">
          <td class="text-capitalize">{{ item.name }}</td>
          <td class="text-capitalize">
            <a v-if="item.group_name" href="#">
              {{ item.group_name }}
            </a>
          </td>
          <td>
            <a
              href="#"
              @click.prevent="detachActivityPass(item)"
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
              ><i class="fa fa-unlink"></i> Détacher</a
            >
            <a
              href="#"
              @click.prevent="
                showModalToChangeGroup(item.id, item.pivot.group_id)
              "
              class="btn btn-sm py-0 d-block mb-1 btn-outline-secondary mx-1"
            >
              <template v-if="item.group_name">Changer le groupe</template>
              <template v-else>Ajouter à un groupe</template>
            </a>
          </td>
          <td class="column-actions">
            <btn-edit :href="route('activities.edit', item.id)" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>

  <jet-modal id="choicheGroup" title="Choisir le Groupe">
    <div class="row">
      <template v-for="group in groups" :key="group.id">
        <div class="col-md-4">
          <button
            class="btn btn-success"
            @click.prevent="setActivityGroup(group.id)"
            v-if="_active_activity_groupe_id != group.id"
          >
            {{ group.name }}
          </button>
          <button
            class="btn btn-danger"
            @click.prevent="detachActivityGroup(group.id)"
            v-else
          >
            Retirer du
            {{ group.name }}
          </button>
        </div>
      </template>
    </div>
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
  props: ["TableData", "groups", "pass"],
  data() {
    return {
      labels: [],
      _active_activity_id: null,
      _active_activity_groupe_id: null
    };
  },
  beforeMount() {
    this.labels.push({ label: "Nom" });
    this.labels.push({ label: "Groupe" });
    this.labels.push({ label: "Paramètres", width: "200" });
  },
  methods: {
    detachActivityPass(data) {
      var routeParameters = {
        pass_id: data.pivot.pass_id,
        activity_id: data.id
      };

      this.$inertia.delete(route("passes.activities.detach", routeParameters), {
        onBefore: () => confirm("Détacher l'activité du Pass?"),
        preserveScroll: true,
        onSuccess: () => {
          $("#activity" + data.id).hide();
        }
      });
    },
    showModalToChangeGroup(activity_id, group_id) {
      this._active_activity_id = activity_id;
      this._active_activity_groupe_id = group_id;
      $("#choicheGroup").modal();
    },
    setActivityGroup(activity_group_id) {
      this.$inertia.put(
        route("passes.activities.groups.move", {
          pass_id: this.pass.id,
          activity_id: this._active_activity_id,
          group_id: activity_group_id
        }),
        {},
        {
          onSuccess: () => {
            // $("#choicheGroup").modal("hide");
            this.iReload(this);
          }
        }
      );
    },
    detachActivityGroup(activity_group_id) {
      this.$inertia.put(
        route("passes.activities.groups.detach", {
          pass_id: this.pass.id,
          activity_id: this._active_activity_id,
          group_id: activity_group_id
        }),
        this.form,
        {
          onSuccess: () => {
            // $("#choicheGroup").modal("hide");
            this.iReload(this);
          }
        }
      );
    }
  }
};
</script>


