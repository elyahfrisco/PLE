<template>
  <nav class="mt-2">
    <ul
      class="nav nav-pills nav-sidebar flex-column pb-5"
      data-widget="treeview"
      role="menu"
      data-accordion="false"
    >
      <li
        class="nav-item"
        :class="{
          'menu-is-opening menu-open': isOpen(item),
          'open-tree': item.submenus != undefined && isOpen(item)
        }"
        v-for="(item, index) in filteredMenus"
        :key="index"
       
      >
        <inertia-link
          preserve-scroll
          :href="
            item.link != undefined
              ? route(item.link, item.params ? item.params : {})
              : '#'
          "
          class="nav-link"
          :class="{
            active: isActive(item)
          }"
          v-if="item.submenus == undefined"
        >
          <i :class="'nav-icon fa fa-' + item.icon"></i>
          <p>
            {{ item.title }}
            <i
              class="right fas fa-angle-right"
              v-if="item.submenus != undefined"
            ></i>
          </p>
          <template v-if="item.badgeSync != undefined">
            <badge-sync
              :tooltip="item.badgeSync.tooltip"
              :id="item.badgeSync.id"
              :key="item.title"
            />
          </template>
        </inertia-link>
        <a href="#" class="nav-link" v-else>
          <i :class="'nav-icon fa fa-' + item.icon"></i>
          <p>
            {{ item.title }}
            <i
              class="right fas fa-angle-right"
              v-if="item.submenus != undefined"
            ></i>
          </p>
          <template v-if="item.badgeSync != undefined">
            <badge-sync
              :tooltip="item.badgeSync.tooltip"
              :id="item.badgeSync.id"
              :key="item.title"
            />
          </template>
        </a>

        <ul class="nav nav-treeview pl-3" v-if="item.submenus != undefined">
          <li
            class="nav-item"
            v-for="(item_submenu, indexsub) in item.submenus"
            :key="indexsub"
          >
            <inertia-link
              preserve-scroll
              :href="
                item_submenu.link != undefined
                  ? route(
                      item_submenu.link,
                      item_submenu.params ? item_submenu.params : {}
                    )
                  : '#'
              "
              class="nav-link"
              :class="{
                active: isActive(item_submenu)
              }"
            >
              <i :class="'nav-icon fa fa-' + item_submenu.icon"></i>
              <p>{{ item_submenu.title }}</p>
              <template v-if="item_submenu.badgeSync != undefined">
                <badge-sync
                  :id="item.badgeSync.id"
                  :tooltip="item_submenu.badgeSync.tooltip"
                  :key="item_submenu.title"
                />
              </template>
            </inertia-link>
          </li>
        </ul>
      </li>
    </ul>
  </nav>
</template>

<script>
import badgeSync from "./badgeSync.vue";
export default {
  components: {
    badgeSync
  },
  props: ["menus_role"],
  data() {
    return {
      menus: []
    };
  },
  computed: {
    filteredMenus() {
      const filtered = this.menus.filter(item => !['Boutique', 'Clients suivi', 'Soins'].includes(item.title));
    console.log(filtered); 
    return filtered;
    }
  },
  beforeMount() {
   
  this.menus = this.menus_role;
  },
  mounted() {
    $("#sidebar-overlay").click(function() {
      $("body").toggleClass("sidebar-collapse sidebar-closed sidebar-open");
    });

    $(
      ".nav-sidebar .nav-link:not(.nav-link[href='" +
        window.location.href +
        "#']):not(.nav-link[href='#'])"
    ).each(function() {
      $(this).click(function() {
        $(".nav-link.active").removeClass("active");
        $(this).addClass("active");
      });
    });

    this.syncBadgeCount();

    setInterval(() => {
      this.syncBadgeCount();
    }, 60 * 1000 * 10);
  },
  methods: {
    syncBadgeCount() {
      if ($("#prospect_count")[0] != undefined) {
        axios.get(route("api.user.prospects.count")).then(response => {
          $("#prospect_count").text(response.data);
        });
      }
    },
    checkIfRouteHasSimilarParams(params) {
      if (params && params.length) {
        var route_params = route().params;
        var sim_param = [];
        for (let param of Object.keys(params)) {
          if (route_params[param] && params[param] == route_params[param]) {
            sim_param.push(param);
          }
        }
        return sim_param.length == Object.keys(params).length;
      } else if (!params && route().current() == this.current_route_name) {
        return true;
      }
      return false;
    },
    isActive(menu) {
      if (
        menu.link &&
        route(menu.link, menu.params ? menu.params : {}) == window.location.href
      ) {
        return true;
      }

      if (
        menu.link == route().current() &&
        this.checkIfRouteHasSimilarParams(menu.params)
      ) {
        return true;
      }

      // if (menu.active_routes != undefined) {
      //   for (var route__ of menu.active_routes) {
      //      if (
      //        route(route__, menu.params ? menu.params : {}) == this.current_url
      //      ) {
      //        return true;
      //      }
      //   }
      // }
      return false;
    },
    isOpen(menu) {
      var open = false;
      if (menu.submenus != undefined) {
        for (var submenu of menu.submenus) {
          if (submenu.link == this.current_route_name) {
            open = true;
            break;
          }
        }
      }
      return open;
    }
  }
};
</script>

<style lang="scss" scoped>
@import "@/Pages/Components/main.scss";
.nav-link.active {
  background-color: white !important;
  color: $color-lpdl !important;
  box-shadow: none !important;
  position: relative;
  right: -0.5rem;
  transition: width ease-in-out 0.3s opacity 0.3s;
  top: 0;
  border-radius: 0.25rem 0 0 0.25rem;
  // width: 104% !important;
  padding-left: 8px;

  > i {
    font-size: 1.2rem;
  }

  .badge {
    background-color: $color-lpdl !important;
    color: white;
    right: 0.3rem;
  }

  &:hover {
    right: -0.55rem;
    // transition: 0.2s;
    box-shadow: 0 1px 3px rgb(0 0 0 / 12%), 0 1px 2px rgb(0 0 0 / 24%) !important;
  }

  &:before {
    content: "";
    position: absolute;
    right: 0px;
    width: 30px;
    height: 15px;
    background-color: transparent;
    box-shadow: -15px 0px #fff;
    z-index: 100;

    top: -15px;
    border-top-left-radius: 50px;
    transform: rotate(180deg);
  }

  &:after {
    content: "";
    position: absolute;
    right: 0px;
    width: 30px;
    height: 15px;
    background-color: transparent;
    box-shadow: -15px 0px #fff;
    z-index: 100;

    bottom: -15px;
    border-bottom-left-radius: 50px;
    transform: rotate(-180deg);
  }
}

.open-tree:not(.menu-open) > .nav-link {
  background-color: white !important;
  color: $color-lpdl !important;
  transition: 0.3s;
}
</style>
