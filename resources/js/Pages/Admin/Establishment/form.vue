<template>
  <form class="p-5">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>Nom/Ville</label>
          <input
            type="text"
            class="form-control"
            placeholder=""
            v-model="form.name"
          />
          <small class="validation-error" v-if="errors.name">{{
            errors.name
          }}</small>
        </div>

        <div class="row">
          <div class="form-group col-md-6">
            <label>Adresse</label>
            <input
              type="text"
              class="form-control"
              placeholder=""
              v-model="form.address"
            />
            <small class="validation-error" v-if="errors.address">{{
              errors.address
            }}</small>
          </div>
          <div class="form-group col-md-6">
            <label>Code postal</label>
            <input
              type="text"
              class="form-control"
              placeholder=""
              v-model="form.postal_code"
            />
            <small class="validation-error" v-if="errors.postal_code">{{
              errors.postal_code
            }}</small>
          </div>
        </div>
        <div class="row">
          <div class="form-group col-md-6">
            <label>Email</label>
            <input
              type="email"
              class="form-control"
              placeholder=""
              v-model="form.email"
            />
            <small class="validation-error" v-if="errors.email">{{
              errors.email
            }}</small>
          </div>
          <div class="form-group col-md-6">
            <label>Téléphone</label>
            <input
              type="text"
              class="form-control"
              placeholder=""
              v-model="form.phone"
            />
            <small class="validation-error" v-if="errors.phone">{{
              errors.phone
            }}</small>
          </div>
        </div>
      </div>
      <div class="col-md-6">
        <div class="form-group">
          <div class="row">
            <div class="col-md-6">
              <label>Horaire d'ouverture</label>
              <input
                type="time"
                class="form-control"
                placeholder=""
                v-model="form.start_time"
              />
              <small class="validation-error" v-if="errors.start_time">{{
                errors.start_time
              }}</small>
            </div>
            <div class="col-md-6">
              <label>Horaire de fermeture</label>
              <input
                type="time"
                class="form-control"
                placeholder=""
                v-model="form.end_time"
              />
              <small class="validation-error" v-if="errors.end_time">{{
                errors.end_time
              }}</small>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Localisation</label>
          <div class="row">
            <div class="col-md-6">
              <label>Latitude</label>
              <input
                type="number"
                class="form-control"
                placeholder=""
                v-model="form.latitude"
              />
              <small class="validation-error" v-if="errors.latitude">{{
                errors.latitude
              }}</small>
            </div>
            <div class="col-md-6">
              <label>Longitude</label>
              <input
                type="number"
                class="form-control"
                placeholder=""
                v-model="form.longitude"
              />
              <small class="validation-error" v-if="errors.longitude">{{
                errors.longitude
              }}</small>
            </div>
          </div>
        </div>

        <div class="form-group">
          <div class="icheck-primary icheck-sm d-inline">
            <input
              type="checkbox"
              v-model="form.relaxation_center"
              id="relaxation_center"
            />
            <label for="relaxation_center">Pôle détente/Centre de soin</label>
          </div>
          <small class="validation-error" v-if="errors.relaxation_center">{{
            errors.relaxation_center
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
      </div>
    </div>
  </form>
</template>

<script>
export default {
  props: ["errors", "establishment", "edit"],
  data() {
    return {
      form: {
        name: null,
        address: null,
        postal_code: null,
        phone: null,
        email: null,
        start_time: null,
        end_time: null,
        latitude: null,
        longitude: null,
        relaxation_center: null
      },
      editMode: false
    };
  },
  mounted() {
    if (this.$props.edit == true) {
      this.editMode = true;
      this.form = this.establishment;
      this.form.relaxation_center = this.form.relaxation_center ? true : false;
    }
  },
  methods: {
    submit() {
      this.$inertia.post(route("establishments.store"), this.form, {
        onSuccess: () => {
          this.iReload();
        }
      });
    },
    update() {
      this.$inertia.put(
        route("establishments.update", this.form.id),
        this.form,
        {
          onSuccess: () => {
            this.iReload();
          }
        }
      );
    }
  }
};
</script>


