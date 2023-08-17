<template>
  <img
    v-if="user.profile_photo_path"
    :src="
      route().t.url +
        $inertia.page.props.appPathProfilPhoto +
        user.profile_photo_path
    "
    :alt="'Photo de profil ' + user.name"
    :width="width_photo"
  />
  <img
    v-else
    :src="
      'https://ui-avatars.com/api/?format=svg&size=' +
        width_photo +
        '&rounded=' +
        rounded_ +
        '&background=1d9cc4&color=fff&name=' +
        user.first_name +
        ' ' +
        user.name
    "
    alt="Photo de profil par defaut"
    :width="width_photo"
  />
</template>

<script>
export default {
  props: ["user", "width", "rounded"],
  data() {
    return {
      width_photo: 150,
      rounded_: true
    };
  },
  mounted() {
    this.width_photo = this.width ? this.width : 150;
    if (this.rounded != null) {
      this.rounded_ = this.rounded;
    }
  }
};
</script>


