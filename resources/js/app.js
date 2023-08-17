require("./bootstrap");

// Import modules...
import { createApp, ErrorCodes, h, ref } from "vue";
import {
  App as InertiaApp,
  plugin as InertiaPlugin
} from "@inertiajs/inertia-vue3";
import { InertiaProgress } from "@inertiajs/progress";

InertiaProgress.init({
  color: "#12d28b",
  includeCSS: true
  // showSpinner: true,
});

/* plugins */
import VueCal from "vue-cal";
import Datepicker from "vue3-datepicker";
import Multiselect from "@vueform/multiselect";
import AppLayout from "@/Layouts/AppLayout_.vue";
import UnauthLayout from "@/Layouts/UnauthLayout.vue";
import JetModal from "@/Jetstream/Modal";
import loadings from "@/Pages/Components/LoadingSpinner";
import StatusBadge from "@/Pages/Components/StatusBadge";
import PerfectScrollbar from "vue3-perfect-scrollbar";
import mitt from "mitt";
import moment from "moment";
import VueExcelXlsx from "vue-excel-xlsx";
import { quillEditor } from "vue3-quill";
const emitter = mitt();

/** btn actions */
import BtnDelete from "@/Pages/Components/Btn/delete";
import BtnEdit from "@/Pages/Components/Btn/edit";
import BtnShow from "@/Pages/Components/Btn/show";
import InertiaPagination from "@/Pages/Components/InertiaPagination";
import InputSearch from "@/Pages/Components/InputSearch";

// import 'jquery-ui/ui/widgets/datepicker.js';
import "admin-lte/plugins/jquery-ui/jquery-ui.min.js";
import "admin-lte/plugins/datatables/jquery.dataTables.min.js";
import "admin-lte/plugins/datatables-bs4/js/dataTables.bootstrap4.min.js";
import "admin-lte/plugins/datatables-responsive/js/dataTables.responsive.min.js";
import "admin-lte/plugins/datatables-responsive/js/responsive.bootstrap4.min.js";
import "admin-lte/plugins/toastr/toastr.min.js";

import "css-skeletons";

const el = document.getElementById("app");
const host = window.location.origin + "/";

const app = createApp({
  render: () =>
    h(InertiaApp, {
      initialPage: JSON.parse(el.dataset.page),
      resolveComponent: async name =>
        await import(`./Pages/${name}`).then(module => module.default)
    })
})
  .mixin({
    components: {
      VueCal,
      Datepicker,
      Multiselect,
      AppLayout,
      UnauthLayout,
      JetModal,
      loadings,
      StatusBadge,
      BtnDelete,
      BtnEdit,
      BtnShow,
      InertiaPagination,
      InputSearch
    },
    // beforeMount() {
    //     console.log(route().params);
    //     console.log(route().current());
    // },
    computed: {
      errors() {
        return this.$page.props.errors;
      },
      auth_user() {
        return this.$page.props.auth_user;
      },
      current_route_name() {
        return this.$page.props.current_route_name;
      },
      current_url() {
        return this.$page.props.current_url;
      },
      host() {
        return window.location.origin + "/";
      },
      q_() {
        return this.$page.props.req;
      },
      current_href() {
        return window.location.href;
      }
    },
    methods: {
      route,

      reload: () => {
        window.location.reload();
      },

      log: value => {
        toastr.options.positionClass = "toast-top-center mt-5";
        toastr.info(value);
        setTimeout(() => {
          toastr.options.positionClass = "toast-bottom-right mb-5";
        }, 100);
      },

      $can(action, yes = null, no = null) {
        if (typeof action !== "string") return false;
        if (yes) return this.$page.props.permissions[action] ? yes : no;
        return this.$page.props.permissions[action] ? true : false;
      },

      iReload(
        i = null,
        param = {
          preserveScroll: true
        }
      ) {
        $(".modal.fade").removeClass("fade");
        setTimeout(() => {
          $(".modal.show").modal("hide");
          this.$inertia.visit(window.location.href, {
            preserveScroll: true,
            replace: true,
            onSuccess: () => {
              $(".modal-backdrop").remove();
              $(".modal-open").removeClass("modal-open");
            }
          });
        }, 200);
      },

      iReloadPartial(...param) {
        $(".modal.show").modal("hide");
        this.$inertia.visit(window.location.href, {
          preserveScroll: true,
          replace: true,
          only: param
        });
      },

      realMail(mail) {
        if (mail) {
          if (mail != "www." && mail.search(/@lesplaisirsdeleau/i) < 0)
            return true;
        }
        return false;
      },

      isLocal: () => {
        return (
          window.location.hostname == "127.0.0.1" ||
          window.location.hostname == "localhost"
        );
      },

      plural: (word, number = 2, sing = null) => {
        return number > 1 ? word + "s" : sing ? sing : word;
      },

      msgConfirm(message) {
        return () => confirm(message);
      },

      days() {
        return this.$page.props.days;
      },

      iValue(array_, id, index = "id") {
        return array_[array_.findIndex(x => x[index] == id)];
      },

      OValueTo(obj, val = null) {
        for (var e in obj) {
          obj[e] = val;
        }
        return obj;
      },

      toSelect(list, valueI = "id", labelI = "name") {
        if (list && Array.isArray(list)) {
          list = list.map(a => {
            a["name"] = a["name"] != null ? a["name"] : "";
            a["first_name"] = a["first_name"] != null ? a["first_name"] : "";

            switch (labelI) {
              case "fullname":
                return {
                  value: a[valueI],
                  label: a["first_name"] + " " + a["name"].toUpperCase(),
                  ...a
                };
                break;
              case "fullname&mail":
                return {
                  value: a[valueI],
                  label:
                    a["first_name"] +
                    " " +
                    a["name"].toUpperCase() +
                    " (" +
                    a["email"] +
                    ")",
                  ...a
                };
                break;
              case "season":
                return {
                  value: a[valueI],
                  label:
                    a["year_start"] +
                    " - " +
                    a["year_end"] +
                    " (" +
                    this.dateFrMin2(a["date_start"]) +
                    "-" +
                    this.dateFrMin2(a["date_end"]) +
                    ")",
                  ...a
                };
                break;
              case "subscription_trimester":
                return {
                  value: a[valueI],
                  label: "Trimester " + a["num_trimester"],
                  ...a
                };
                break;
              default:
                return {
                  value: a[valueI],
                  label: a[labelI],
                  ...a
                };
                break;
            }
          });
          return list;
        }
        return [];
      },

      modalClosed() {
        this.$emit("modal-closed");
      },

      isDate_(date) {
        try {
          return (
            date &&
            date != null &&
            date.match("([0-9]{2,4}[-/][0-9]{2}[-/][0-9]{2,4})")
          );
        } catch (error) {
          return false;
        }
      },

      dayfr(dayname, min = false) {
        dayname = this.$page.props.daysfr[dayname.toLowerCase()];
        return min ? dayname.substring(0, 3) : dayname;
      },

      dayAng(date) {
        return this.$moment(date)
          .format("dddd")
          .toLowerCase();
      },

      dateAng(date) {
        return this.$moment(date).format("YYYY-MM-DD");
      },

      dateHAng(date) {
        return this.$moment(date).format("YYYY-MM-DD HH:mm");
      },

      dateFr(date) {
        return this.isDate_(date)
          ? this.$moment(date).format("DD/MM/YYYY")
          : null;
      },

      dateDayFr(date) {
        return this.dayfr(
          this.$moment(date)
            .format("dddd")
            .toLowerCase()
        );
      },

      dateFrMin(date) {
        return this.$moment(date).format("DD/MM/YY");
      },

      dateFrMin2(date) {
        return this.$moment(date).format("DD/MM");
      },

      dateHFr(date) {
        return this.$moment(date).format("DD/MM/YYYY HH:mm");
      },

      dateHFrFinDecouverte(date, month = 6) {
        return this.$moment(date)
          .add(month, "M")
          .format("DD/MM/YYYY");
      },

      dateDayHFr(date) {
        return (
          this.dateDayFr(date) +
          " " +
          this.$moment(date).format("DD/MM/YYYY HH:mm")
        );
      },

      H(date) {
        if (date.search(/[0-9]{2}[\/\-][0-9]{2}/g) < 0) {
          return this.$moment(this.dateAng(this.$moment()) + " " + date).format(
            "HH:mm"
          );
        } else {
          return this.$moment(date).format("HH:mm");
        }
      },

      float2(num) {
        return num.toFixed(2);
      },

      infoCenter(message) {
        toastr.options.positionClass = "toast-top-center mt-5";
        toastr.info(message);
        setTimeout(() => {
          toastr.options.positionClass = "toast-bottom-right mb-5";
        }, 100);
      },

      disabledIndexDate(day) {
        var days = this.$page.props.days;
        var index = days.findIndex(x => x.en == day);
        var disabledDaysWeek = [1, 2, 3, 4, 5, 6, 0];
        disabledDaysWeek.splice(index, 1);
        return disabledDaysWeek;
      },

      getDayDate(startDate, endDate, days) {
        startDate = this.$moment(startDate);
        endDate = this.$moment(endDate).add(1, "d");

        var _dates = [];
        var _tmp = startDate.clone();

        while (
          _tmp.format("dd-mm-yyyy") == startDate.format("dd-mm-yyyy") ||
          _tmp.isBetween(startDate, endDate)
        ) {
          if (days.includes(_tmp.day())) {
            _dates.push(new Date(_tmp.format("YYYY-MM-DD")));
          }
          _tmp = _tmp.add(1, "d");
        }
        return _dates;
      },

      setClassOverlappedEventInCalendar() {
        setTimeout(() => {
          var events = $(
            ".vuecal--week-view .vuecal__event, .vuecal--day-view .vuecal__event"
          );
          for (var event of events) {
            if (event.getAttribute("style").search("33.3333") > 0) {
              event.setAttribute("overlapped-event", true);
            }
          }
        }, 1000);
      },

      initTooltipe() {
        this.clearTooltip();
        setTimeout(() => {
          $("[data-toggle=tooltip]").tooltip();
          $("[data-toggle=popover]").popover();
        }, 1000);
      },

      clearTooltip() {
        setTimeout(() => {
          $(".tooltip.show").remove();
        }, 500);
      },

      clearModal() {
        setTimeout(() => {
          $(".modal-backdrop").remove();
          $(".modal-open").removeClass("modal-open");
        }, 500);
      },

      initDataTable(id) {
        setTimeout(() => {
          $(id).DataTable();
        }, 200);
      },

      toUL(array_, type) {
        let list = "<ul>";
        for (let item of array_) {
          list += "<li>";
          switch (type) {
            case "user":
              list +=
                "<span class='text-capitalize'>" +
                item.first_name +
                "</span>" +
                " " +
                "<span class='text-uppercase'>" +
                item.name +
                "</span>";
              break;
            default:
              list += item.value ? item.value : item.name ? item.name : "";
              break;
          }
          list += "</li>";
        }
        return list + "</ul>";
      },

      nav_print() {
        window.print();
      },

      // getEstablishments() {
      //     return axios.get(host + 'api/establishments').then(response => response.data);
      // }

      setPageTitle(title) {
        document.title =
          this.$page.props.appName + (title ? " - " + title : "");
      },

      setUrl(url, push_ = false) {
        if (push_) {
          history.pushState({}, null, url);
        } else {
          history.replaceState({}, null, url);
        }
      },

      initDraggable() {
        $(".modal-dialog").draggable({
          handle: ".modal-header"
        });
      },

      filterEmpty(params) {
        Object.keys(params).forEach(key => {
          if (
            params[key] == "" ||
            params[key] === null ||
            key == "cancelToken"
          ) {
            delete params[key];
          }
        });
        return params;
      },

      serializeUrl(obj) {
        var str = [];
        for (var p in obj)
          if (obj.hasOwnProperty(p)) {
            str.push(encodeURIComponent(p) + "=" + encodeURIComponent(obj[p]));
          }
        return str.join("&");
      },

      simulateCalendarClick() {
        $(".vuecal__view-btn[aria-label='Mois view']").click();
        setTimeout(() => {
          $(".vuecal__view-btn[aria-label='Semaine view']").click();
        }, 500);
      },

      simulateCalendarClickMonth() {
        $(".vuecal__view-btn[aria-label='Mois view']").click();
        setTimeout(() => {
          $(".vuecal__view-btn[aria-label='Année view']").click();
        }, 300);
      }
    }
  })
  .use(PerfectScrollbar)
  .use(InertiaPlugin)
  .use(VueExcelXlsx)
  .use(quillEditor);
app.config.globalProperties.$emitter = emitter;
app.config.globalProperties.$moment = moment;
app.config.globalProperties.sortParams = {};
app.config.warnHandler = () => null;

app.mount(el);

$(() => {
  $("[data-toggle=tooltip]").tooltip();
});
