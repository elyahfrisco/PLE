<template>
  <app-layout>
    <template #pageTitle>Modifier certificat medical</template>
    <form
      @submit.prevent="upload"
      enctype="multipart/form-data"
      class="col-md-4"
    >
      <medical-certificate :customer="customer"></medical-certificate>
      <div class="form-group">
        <label class="block">Nouveau certificat</label>
        <div class="input-group">
          <div class="custom-file">
            <input
              type="file"
              class="custom-file-input"
              accept="image/*,.doc,.docx,.pdf"
              @change="getFile"
              id="inputCertificat"
              required
            />
            <label class="custom-file-label" for="inputCertificat"
              >Selectionnez une photo</label
            >
          </div>
        </div>

        <small class="validation-error" v-if="errors.medical_certificate">{{
          errors.medical_certificate
        }}</small>
      </div>
      <br />
      <button type="submit" class="btn btn-success rounded">Changer</button>
    </form>
  </app-layout>
</template>

<script>
import medicalCertificate from "./medicalCertificateTemplate.vue";

export default {
  components: { medicalCertificate },
  props: ["customer", "errors"],
  data() {
    return {
      form: {
        medical_certificate: null
      }
    };
  },
  methods: {
    getFile(e) {
      this.form.medical_certificate = e.target.files[0];
    },
    upload() {
      let data = new FormData();
      data.append("medical_certificate", this.form.medical_certificate);

      this.$inertia.post(
        route("customers.medical_certificate.update", this.customer.id),
        data
      );
    }
  }
};
</script>


