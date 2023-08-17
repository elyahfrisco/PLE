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
    :style="{ 'min-height': width_photo }"
    :onerror="
      'if (this.src != \'' +
        defaultImage +
        '\') this.src = \'' +
        defaultImage +
        '\';'
    "
  />
  <img
    v-else
    :src="defaultImage"
    :style="{ 'min-height': width_photo }"
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
  },
  computed: {
    defaultImage() {
      return (
        "https://ui-avatars.com/api/?format=svg&size=" +
        this.width_photo +
        "&rounded=" +
        this.rounded_ +
        "&background=1d9cc4&color=fff&name=" +
        this.user.first_name +
        " " +
        this.user.name
      );
    }
  }
};
</script>


