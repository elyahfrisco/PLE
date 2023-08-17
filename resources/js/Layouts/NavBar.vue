<template>
  <nav
    class="main-header navbar navbar-expand navbar-white navbar-light d-sm-flex d-block"
    :class="{
      'guest-mode': auth_user.guest_mode
    }"
  >
    <!-- Left navbar links -->
    <ul class="navbar-nav">
      <li class="nav-item">
        <a class="nav-link" data-widget="pushmenu" href="#" role="button">
          <i class="fas fa-bars exp"></i>
          <i class="fas fa-stream min"></i>
        </a>
      </li>
      <li
        class="nav-item px-1"
        v-if="
          auth_user.role_name == 'admin' || auth_user.role_name == 'assistant'
        "
      >
        <input-search
          w_auto="true"
          class_="mx-0 my-sm-0 my-3"
          :url="route('customers.index', { both: true })"
        />
      </li>
    </ul>

    <!-- Right navbar links -->
    <ul class="navbar-nav ml-auto">
      <li
        class="nav-item"
        v-if="
          (isLocal() || $page.props.req.clear) &&
            (auth_user.role_name == 'admin' ||
              auth_user.role_name == 'assistant')
        "
      >
        <inertia-link
          class="nav-link text-danger"
          :href="route('tests.clear.cache')"
        >
          <small>Clear cache</small>
        </inertia-link>
      </li>
      <li
        class="nav-item"
        v-if="
          auth_user.role_name == 'admin' || auth_user.role_name == 'assistant'
        "
      >
        <inertia-link
          class="nav-link"
          data-toggle="tooltip"
          data-placement="bottom"
          title="Signaler un bug"
          :href="
            route('bugs.create', {
              _query: { source: $page.props.current_url }
            })
          "
        >
          <i class="fas fa-exclamation-circle mr-1 text-warning"></i>
          <span class="d-inline d-sm-none"> Signaler un bug</span>
        </inertia-link>
      </li>

      <li
        class="nav-item"
        v-if="
          auth_user.role_name == 'admin' || auth_user.role_name == 'assistant'
        "
      >
        <inertia-link
          class="nav-link"
          data-toggle="tooltip"
          data-placement="bottom"
          title="Créer un enseignant"
          :href="route('coach.create')"
        >
          <i class="fas fa-user-plus mr-1"></i>
          <span class="d-inline d-sm-none"> Enseignant</span>
        </inertia-link>
      </li>

      <!-- Notifications Dropdown Menu -->
      <li class="nav-item dropdown">
        <a class="nav-link" data-toggle="dropdown" href="#">
          <i class="far fa-bell"></i>
          <span class="d-inline d-sm-none"> Notifications</span>
          <span class="badge badge-danger navbar-badge">0</span>
        </a>

        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
          <span class="dropdown-header">1 Notification(s)</span>
          <div class="dropdown-divider"></div>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item">
            <i class="fas fa-users mr-2"></i> { notification_text }
            <span class="float-right text-muted text-sm">11 hours</span>
          </a>
          <div class="dropdown-divider"></div>
          <div class="dropdown-divider"></div>
          <a href="#" class="dropdown-item dropdown-footer"
            >Afficher toutes les notifications</a
          >
        </div>


        
      </li>

      <li class="nav-item">
        <inertia-link
          class="nav-link"
          data-widget_="control-sidebar"
          data-slide_="true"
          role_="button"
          data-toggle="tooltip"
          data-placement="bottom"
          title="Info compte"
          :href="route('account.index')"
        >
          <i class="fas fa-user-circle mr-1"></i>
          <span class="text-capitalize"> {{ auth_user.first_name }}</span>
        </inertia-link>
      </li>
      <li class="nav-item">
        <a
          class="nav-link pointer"
          data-toggle="tooltip"
          data-placement="bottom"
          title="Se déconnecter"
          @click.prevent="logout"
        >
          <i class="fas fa-sign-out-alt"></i>
          <span class="d-inline d-sm-none"> Se déconnecter</span>
        </a>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  methods: {
    logout() {
      axios.get(route("following.cookie.clear"));
      document.cookie =
        "guest_mode=; Path=/; Expires=Thu, 01 Jan 1970 00:00:01 GMT;";
      axios.post(route("logout")).then(() => {
        window.location.reload();
      });
    }
  }
};
</script>

<style lang="scss" scoped>
.main-header.navbar {
  .nav-link {
    &:not(.active) {
      color: white !important;
    }

    font-size: 22px;
    padding: 5px 1rem !important;

    .badge {
      font-size: 10px;
    }
  }

  @media screen and (max-width: 770px) {
    &.navbar-expand .navbar-nav,
    .navbar-nav {
      flex-direction: column !important;

      .nav-item {
        margin-top: 5px;
      }

      .d-inline {
        display: inline !important;
      }
    }
  }
}
</style>
