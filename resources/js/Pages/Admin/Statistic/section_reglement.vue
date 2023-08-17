<template>
  <div class="card flat">
    <div class="card-header">
      <div class="">
        <h3 class="card-title">
          <i class="ion ion-clipboard mr-1"></i>
          Liste des relances règlements
        </h3>
        <div class="row col-12">
          <div class="form-group col-md-4">
            <Multiselect
              v-model="form.establishment_id"
              placeholder="--centres--"
              :options="$page.props.establishments_list"
              label="name"
              valueProp="id"
            />
          </div>
          <div class="form-group col-lg-4 col-md-4">
            <Multiselect
              v-model="form.pass_id"
              placeholder="--pass--"
              :options="select.passes"
              label="name"
              trackBy="name"
              valueProp="id"
              :searchable="true"
            />
          </div>

          <div class="form-group col-lg-4 col-md-4">
            <Multiselect
              v-model="form.planning_id"
              :options="
                async function (query) {
                  return await getPlannings(query);
                }
              "
              placeholder="--Saisir le nom du groupe--"
              :filterResults="false"
              :resolveOnLoad="false"
              :minChars="2"
              :searchable="true"
              :loading="loadPlanning"
              :clearOnSelect="false"
              delay="500"
              trackBy="group_name"
              label="group_name"
              valueProp="id"
              ref="selectPlanning"
              required
            />
          </div>
          <div class="col-md-4">
            <input
              class="form-control"
              type="date"
              placeholder="date min"
              v-model="form.min_date"
            />
          </div>
          <div class="col-md-4">
            <input
              class="form-control"
              type="date"
              placeholder="date max"
              v-model="form.max_date"
            />
          </div>
          <div class="form-group col-md">
            <button
              @click.prevent="getReglements"
              class="btn btn-success btn-sm"
            >
              <i class="fa fa-filter"></i>
            </button>
            <button
              @click.prevent="resetFilters"
              class="btn btn-primary btn-sm ml-2"
            >
              <i class="fa fa-trash"></i>
            </button>
          </div>
        </div>
      </div>
    </div>
    <div class="card-body">
      <ul class="todo-list" data-widget="todo-list">
        <template v-for="item in relaunchs.reglements.data" :key="item.id">
          <li>
            <div class="icheck-primary d-inline ml-2">
              <input
                type="checkbox"
                :checked="item.payment?.id"
                value="1"
                disabled
                :id="'relaunch' + item.id"
              />
              <label :for="'relaunch' + item.id"></label>
            </div>
            <span class="text">
              {{ item.user?.full_name }} : {{ item.amount }}€
              {{ item.payment_method }}
            </span>
            <small
              class="badge"
              :class="[item.timeSpent ? 'badge-success' : 'badge-info']"
              ><i class="far fa-clock"></i> {{ item.elapseTime }}
            </small>
            <div class="tools">
              <template v-if="!item.payment?.id">
                <button
                  class="btn btn-success btn-sm btn-action"
                  @click="showModalAddPayment(item.id)"
                >
                  <i class="fa fa-money-check-alt"></i>
                </button>
                <modal-form-payment
                  v-if="activeShowId == item.id"
                  :payment_methods="payment_methods"
                  :bill="item"
                  @payment-saved="$emit('refresh-list')"
                />
              </template>

              <inertia-link
                v-if="!item.payment?.id"
                class="btn btn-outline-success btn-sm btn-action"
                data-toggle="tooltip"
                data-placement="top"
                title="Envoyer la relance maintenant"
                method="POST"
                preserve-scroll
                :href="
                  route('api.relaunchs.send_now', {
                    relaunch_id: item.relaunch_id,
                    bill_id: item.id,
                  })
                "
                :onBefore="
                  msgConfirm('Êtes-vous sûr d\'envoyer la relance maintenant ?')
                "
                :onSuccess="() => getReglements()"
              >
                <i class="fa fa-paper-plane"></i>
              </inertia-link>

              <inertia-link
                class="btn btn-outline-danger btn-sm btn-action"
                method="DELETE"
                data-toggle="tooltip"
                data-placement="top"
                title="Supprimer la relance"
                preserve-scroll
                :href="route('relaunchs.destroy', item.id)"
                :onBefore="msgConfirm('Supprimer la relance ?')"
                :onSuccess="() => getReglements()"
              >
                <i class="fas fa-trash"></i>
              </inertia-link>
            </div>
          </li>
        </template>
      </ul>
    </div>
  </div>
</template>

<script>
import InertiaDataTable from "@/Pages/Components/DataTable/InertiaDataTable.vue";
import TableHeader from "@/Pages/Components/DataTable/TableHeader.vue";
import ModalFormPayment from "@/Pages/Invoice/Unpaid/ModalFormPayment.vue";

export default {
  components: {
    InertiaDataTable,
    TableHeader,
    ModalFormPayment,
  },
  data() {
    return {
      form: {},
      select: {},
      relaunchs: {
        reglements: [],
      },
      loadPlanning: false,
      activeShowId: null,
    };
  },
  mounted() {
    this.getReglements();
    this.getPass();
    this.getPlannings();
    this.getPaymentMethods();
  },
  methods: {
    resetFilters() {
      this.form.establishment_id = null;
      this.form.pass_id = null;
      this.form.planning_id = null;
      this.form.min_date = null;
      this.form.max_date = null;
    },
    getReglements() {
      axios
        .get(route("api.bills.list"), {
          params: this.form,
        })
        .then((response) => {
          this.relaunchs.reglements = response.data;
          this.initTooltipe();
        });
    },
    getPass() {
      axios.get(route("passes.index")).then((response) => {
        if (response.data != undefined) {
          this.select.passes = response.data.passes;
        }
      });
    },
    getPlannings(query = "") {
      if (query.length) {
        this.loadPlanning = true;
        let params = {
          q: query,
        };
        return axios
          .get(route("api.plannings.list", params))
          .then((response) => {
            this.select.plannings = response.data;
            this.loadPlanning = false;
            return this.select.plannings;
          });
      }
      return [];
    },
    SendNow(id) {
      msgConfirm("Êtes-vous sûr d'envoyer la relance maintenant ?")
        ? axios
            .get(route("api.relaunch.send_now"), {
              params: {
                relaunch_id: id,
              },
            })
            .then((response) => {
              this.getReglements();
            })
        : null;
    },
    showModalAddPayment(bill_id) {
      this.activeShowId = bill_id;
      setTimeout(() => {
        $("#add-payment" + bill_id).modal();
      }, 200);
    },
    getPaymentMethods() {
      axios.get(route("api.payments.methods")).then((response) => {
        if (response.data) {
          this.payment_methods = this.toSelect(response.data, "name");
        }
      });
    },
  },
};
</script>


