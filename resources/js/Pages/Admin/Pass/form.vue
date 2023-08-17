<template>
  <form>
    <div class="form-group">
      <span>Nom</span>
      <input
        type="text"
        class="form-control"
        placeholder=""
        v-model="form.name"
        required
      />
      <small class="validation-error" v-if="errors.name">{{
        errors.name
      }}</small>
    </div>
    <div class="form-group">
      <span>Nombre de seances par defaut</span>
      <input
        type="number"
        class="form-control"
        v-model="form.number_sessions"
        required
      />
      <small class="validation-error" v-if="errors.number_sessions">{{
        errors.number_sessions
      }}</small>
    </div>
    <div class="form-group">
      <span>Validité ( en mois )</span>
      <input
        type="number"
        class="form-control"
        v-model="form.period_validity"
        required
      />
      <small class="validation-error" v-if="errors.period_validity">{{
        errors.period_validity
      }}</small>
    </div>
    <div class="form-group mt-3">
      <div class="icheck-primary icheck-sm d-inline">
        <input type="checkbox" value="care" v-model="form.care" id="care" />
        <label for="care">Soin</label>
      </div>
      <small class="validation-error" v-if="errors.care">{{
        errors.care
      }}</small>
    </div>

    <div class="form-group">
      <div class="icheck-primary icheck-sm d-inline">
        <input
          type="checkbox"
          v-model="form.pass_trimester"
          id="pass_trimester"
        />
        <label for="pass_trimester">Pass trimestriel</label>
      </div>
      <small class="validation-error" v-if="errors.pass_trimester">{{
        errors.pass_trimester
      }}</small>
    </div>

    <button
      type="submit"
      @click.prevent="update"
      v-if="editMode"
      class="btn btn-success"
    >
      Enregistrer la Modification
    </button>
    <button
      type="submit"
      @click.prevent="submit"
      v-else
      class="btn btn-success"
    >
      Enregistrer
    </button>
  </form>
</template>

<script>
export default {
  props: ["errors", "pass", "edit"],
  data() {
    return {
      form: {
        name: null,
        period_validity: null,
        number_sessions: null,
        care: null,
        pass_trimester: null
      },
      editMode: false
    };
  },
  mounted() {
    if (this.$props.edit == true) {
      this.editMode = true;
      this.form = this.pass;
      this.form.care = this.form.care ? true : false;
      this.form.pass_trimester = this.form.pass_trimester ? true : false;
    }
  },
  methods: {
    submit() {
      this.$inertia.post(route("passes.store"), this.form);
    },
    update() {
      this.$inertia.put(route("passes.update", this.form.id), this.form);
    }
  }
};
</script>


