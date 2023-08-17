<template>
  <loading
    v-model:active="isLoading"
    :can-cancel="false"
    :is-full-page="true"
    loader="dots"
    color="#1d9cc4"
    :lock-scroll="true"
    blur="50px"
    @click="isLoading = false"
  />

  <div class="wrapper" v-on:notify="notify">
    <nav-bar />
    <aside
      class="main-sidebar sidebar-dark-black elevation-4"
      :class="{
        'guest-mode': auth_user.guest_mode,
      }"
    >
      <a :href="route('dashboard')" class="brand-link text-center">
        <img src="/images/logo.png" alt="Logo Les plaisirs de l'eau" class="" />
        <br />
      </a>

      <div class="sidebar">
        <side-bar :menus_role="menus_role" />
      </div>
    </aside>

    <div class="content-wrapper" style="min-height: 553px">
      <div class="content mt-md-2 px-md-1 px-0">
        <div class="container-fluid px-md-1 px-2">
          <div class="card shadow-sm bg-light rounded-0">
            <div
              class="card-body px-md-5 px-3 py-3 rounded-sm"
              style="background: white"
            >
              <div class="row mb-2" v-if="hideTitle !== true">
                <h1 class="m-0">
                  <a
                    href="#"
                    @click="backHistory"
                    id="btnBackHistory"
                    class="mr-2"
                    v-if="linkDashboard && lengh_history > 1"
                    ><i class="fa fa-arrow-left text-dark"></i
                  ></a>
                  <span id="pageTitle" class="h4">
                    <slot name="pageTitle"></slot>
                  </span>
                </h1>
              </div>
              <slot></slot>
            </div>
          </div>
        </div>
      </div>
    </div>
    <auth-user-menu :user="auth_user" />
    <footer class="main-footer">
      <div class="float-right d-none d-sm-inline">
        Réalisé par <a href="https://www.club360.fr">club360.fr</a> V1.61
      </div>
      <strong>Copyright © {{ new Date().getFullYear() }}</strong>
    </footer>
    <div id="sidebar-overlay"></div>
  </div>
</template>

<script>
import NavBar from "./NavBar.vue";
import SideBar from "./SideBar.vue";
import AuthUserMenu from "./AuthUserMenu.vue";
import Loading from "vue-loading-overlay";
import { Inertia } from "@inertiajs/inertia";

export default {
  name: "app_layout",
  components: {
    NavBar,
    SideBar,
    AuthUserMenu,
    Loading,
  },

  props: [
    "menus_role",
    "flash",
    "auth_user",
    "loading",
    "hideTitle",
    "collapse",
    "toHistory",
  ],

  data() {
    return {
      showingNavigationDropdown: false,
      linkDashboard: this.route("dashboard") != window.location.href,
      isLoading: true,
    };
  },
  methods: {
    backHistory() {
      if (this.q_.toHistory) {
        this.$inertia.visit(this.q_.toHistory);
      } else {
        window.history.length > 1
          ? window.history.go(-1)
          : this.$router.push("/");
        this.iReload();
      }
    },
    notify() {
      toastr.options.positionClass = "toast-bottom-right mb-5";
      toastr.options.closeButton = true;

      var flash = this.flash;

      if (flash) {
        if (flash.success) {
          toastr.success(flash.success);
        }
        if (this.$page.props.flash.error) {
          toastr.error(this.$page.props.flash.error);
        }
        if (flash.info) {
          toastr.info(flash.info);
        }
        if (flash.warning) {
          toastr.warning(flash.warning);
        }
      }
    },
  },
  watch: {
    flash: {
      deep: true,
      handler() {
        this.notify();
      },
    },
    loading: {
      deep: true,
      handler() {
        this.isLoading = this.loading;
      },
    },
  },
  beforeMount() {
    Inertia.on("start", (event) => {
      this.isLoading = true;
    });

    Inertia.on("finish", (event) => {
      this.isLoading = false;
    });

    if (JSON.parse($("#app").attr("data-page")).props.logged == false) {
      window.location = route("login");
    } else if (JSON.parse($("#app").attr("data-page")).props.user == null) {
      this.reload();
    }
    this.$emitter.on(
      "PageLoading",
      (status = true) => (this.isLoading = status)
    );
  },
  mounted() {
    $("[data-toggle=tooltip]").tooltip();
    $(".datatable:not(.manual)").DataTable({ pageLength: 25 });
    $(".modal-dialog").draggable({
      handle: ".modal-header",
    });
    this.notify();
    this.isLoading = false;
    this.setPageTitle($("#pageTitle").text());

    if (this.$page.props.page_title) {
      this.setPageTitle(this.$page.props.page_title);
    }

    if (this.collapse === "true") {
      $("body").addClass("sidebar-collapse");
    } else if (this.collapse === "false") {
      $("body").removeClass("sidebar-collapse");
    }
    this.clearTooltip();
    this.clearModal();
  },
  computed: {
    lengh_history() {
      return window.history.length;
    },
  },
};
</script>

<style lang="scss">
@import "@/Pages/Components/main.scss";

.subscriptions__activity {
  &.present.debited {
    background-color: $color-green;
  }

  &.prevent_before_6 {
    background-color: $color-yellow;
  }

  &.prevent_out_of_time {
    background-color: $color-orange;
  }

  &.absent.debited {
    background-color: $color-red;
  }
}
</style>
