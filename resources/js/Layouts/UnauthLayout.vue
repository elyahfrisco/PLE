<template>
  <div
    class="unauth-layout"
    :style="
      'background: url(' + route('home') + '/images/index-background.jpg)'
    "
  >
    <nav
      class="
        navbar navbar-expand-lg
        justify-content-between
        fixed-top
        shadow-sm
      "
    >
      <a class="navbar-brand" href="#">Les plaisirs de l'eau</a>
      <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#navbarSupportedContent"
        aria-controls="navbarSupportedContent"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon">
          <i class="fa fa-bars"></i>
        </span>
      </button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav ml-auto">
          <!-- <li class="nav-item active">
            <inertia-link :href="route('home')" class="nav-link">
              Accueil
            </inertia-link>
          </li> -->
          <template v-if="!connected">
            <li class="nav-item">
              <inertia-link :href="route('login')" class="nav-link">
                Se connecter
              </inertia-link>
            </li>
            <li class="nav-item">
              <inertia-link :href="route('signup')" class="nav-link">
                S'inscrire
              </inertia-link>
            </li>
          </template>
          <template v-else>
            <li class="nav-item">
              <a :href="route('dashboard')" class="nav-link"> Mon compte </a>
            </li>
          </template>
        </ul>
      </div>
    </nav>
    <div
      class="px-20 content-page pt-5"
      :style="
        `
      background: rgba(29,156,196,1);
      background: linear-gradient(
      0deg,
      rgba(29,156,196,1) 0%,
      rgba(29,156,196,1) 0%,
      rgba(29,156,196,0) 50%,
      rgba(29,156,196,0) 100%
      );
      `
      "
    >
      <main>
        <slot></slot>
      </main>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      connected: false
    };
  },
  beforeMount() {
    this.connected = this.$page.props.user ? true : false;
  },
  mounted() {
    toastr.options.positionClass = "toast-bottom-right";
    if (this.$page.props.flash.success) {
      toastr.success(this.$page.props.flash.success);
    }
    if (this.$page.props.flash.info) {
      toastr.info(this.$page.props.flash.info);
    }
    if (this.$page.props.flash.error) {
      toastr.options.positionClass = "toast-top-center mt-5";
      toastr.error(this.$page.props.flash.error);
      setTimeout(() => {
        toastr.options.positionClass = "toast-bottom-right mb-5";
      }, 100);
    }
    if (this.$page.props.flash.warning) {
      toastr.warning(this.$page.props.flash.warning);
    }

    if (this.$page.props.page_title) {
      this.setPageTitle(this.$page.props.page_title);
    }
  }
};
</script>
<style scoped></style>
