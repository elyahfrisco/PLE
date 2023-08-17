<template>
  <a
    v-if="this.customer.medical_certificate_path"
    :href="
      route().t.url +
        $inertia.page.props.appPathMedicalCertificate +
        this.customer.medical_certificate_path
    "
    class="btn btn-outline-info rounded-0"
    target="_blank"
  >
    <i class="fa fa-file mr-1"></i>Consulter</a
  >
  <p v-else class="text-red-500">Aucun</p>
</template>

<script>
export default {
  props: ["customer", "width"],
  data() {
    return {
      width_photo: 150
    };
  },
  mounted() {
    this.width_photo = this.width ? this.width : 150;
  }
};
</script>


