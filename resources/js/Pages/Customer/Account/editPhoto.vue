<template>
  <app-layout>
    <template #pageTitle>Modifier photo</template>
    <form
      @submit.prevent="upload"
      enctype="multipart/form-data"
      class="col-md-4"
    >
      <div class="text-center d-block">
        <p>Photo actuel</p>
        <profil-photo :user="this.customer"></profil-photo>
      </div>
      <div class="form-group">
        <label class="block">Nouvelle photo</label>

        <div class="input-group">
          <div class="custom-file">
            <input
              type="file"
              class="custom-file-input"
              accept="image/*"
              @change="getFile"
              id="inputPhoto"
              required
            />
            <label class="custom-file-label" for="inputPhoto"
              >Selectionnez une photo</label
            >
          </div>
        </div>

        <small class="validation-error" v-if="errors.profile_photo">{{
          errors.profile_photo
        }}</small>
      </div>
      <button type="submit" class="btn btn-success block">Changer</button>
    </form>
  </app-layout>
</template>

<script>
import profilPhoto from "@/Pages/Components/profilPhoto.vue";
export default {
  components: { profilPhoto },
  props: ["customer", "errors"],
  data() {
    return {
      form: {
        profile_photo: null
      }
    };
  },
  methods: {
    getFile(e) {
      this.form.profile_photo = e.target.files[0];
    },
    upload() {
      let data = new FormData();
      data.append("profile_photo", this.form.profile_photo);

      this.$inertia.post(
        route("customers.photo.update", this.customer.id),
        data
      );
    }
  }
};
</script>


