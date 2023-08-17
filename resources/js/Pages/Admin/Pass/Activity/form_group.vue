<template>
  <form>
    <div class="form-group">
      <label>Nom du group</label>
      <input
        type="text"
        class="form-control"
        placeholder="Nom du group"
        v-model="form.name"
      />
      <small class="validation-error" v-if="errors.name">{{
        errors.name
      }}</small>
    </div>
    <div class="form-group">
      <label>Nombre de séances</label>
      <input
        type="text"
        class="form-control"
        placeholder="Nombre de séances"
        v-model="form.number_session"
      />
      <small class="validation-error" v-if="errors.number_session">{{
        errors.number_session
      }}</small>
    </div>
    <div class="form-group">
      <label>Type</label>
      <div class="">
        <div class="icheck-success d-inline">
          <input
            type="radio"
            value="or"
            :id="'or' + form.id"
            v-model="form.select_mode"
          />
          <label :for="'or' + form.id">OU</label>
        </div>
        <div class="icheck-success d-inline ml-4">
          <input
            type="radio"
            value="and"
            :id="'and' + form.id"
            v-model="form.select_mode"
          />
          <label :for="'and' + form.id">ET</label>
        </div>
      </div>
    </div>

    <div class="form-group">
      <button
        v-if="!form.id"
        @click.prevent="saveGroup"
        class="btn btn-sm btn-success"
      >
        Enregistrer
      </button>
      <button
        v-else
        @click.prevent="updateGroup"
        class="btn btn-sm btn-success"
      >
        Modifier
      </button>
    </div>
  </form>
</template>

<script>
export default {
  props: ["group", "pass_id", "number_session"],
  data() {
    return {
      form: {
        id: null,
        name: null,
        select_mode: "or",
        number_session: null
      }
    };
  },
  beforeMount() {
    if (this.group) {
      this.form = cpp(this.group);
    } else {
      this.form.pass_id = this.pass_id;
      this.form.number_session = this.number_session;
    }
  },
  methods: {
    saveGroup() {
      const pass_id = this.pass_id;

      this.$inertia.post(
        route("passes.activities.groups.store", {
          pass_id: pass_id
        }),
        {
          name: this.form.name,
          select_mode: this.form.select_mode,
          pass_id: pass_id
        },
        {
          onSuccess: () => {
            this.iReload(this);
          }
        }
      );
    },

    updateGroup() {
      this.$inertia.put(
        route("passes.activities.groups.update", {
          pass_id: this.pass_id,
          group: this.group
        }),
        this.form,
        {
          onSuccess: () => {
            this.iReload(this);
          }
        }
      );
    }
  }
};
</script>


