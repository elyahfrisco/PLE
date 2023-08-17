<template>
  <div class="row">
    <h5 class="mb-3 col-12 px-0">Historique des souscriptions</h5>

    <inertia-link
      :href="route('subscriptions.create', { user_id: user_id })"
      class="d-block btn btn-success text-white mb-4 mx-auto rounded-0"
      >Nouvelle souscription</inertia-link
    >
    <div class="col-12"></div>
    <template
      v-if="subscriptions && subscriptions.length && subscriptions.length > 0"
    >
      <template v-for="subscription in subscriptions" :key="subscription.id">
        <div class="col-xl-12 col-lg-4 col-md-6 col-12">
          <a
            class="
              subsription-item
              info-box
              bg-success
              animate__animated animate__fadeIn
            "
            :class="[subscription.status]"
            href="#"
          >
            <span
              class="info-box-payment badge badge-danger"
              v-if="subscription.payment == null"
              data-toggle="tooltip"
              title="non réglé"
            >
              <i class="fa fa-euro-sign"></i>
            </span>
            <span class="info-box-icon">
              <i
                v-if="subscription.status == 'futur'"
                class="fa fa-hourglass-start"
              ></i>
              <i
                v-if="subscription.status == 'in_progress'"
                class="fa fa-hourglass-half"
              ></i>
              <i
                v-if="subscription.status == 'finished'"
                class="fa fa-hourglass-end"
              ></i>
            </span>
            <div class="info-box-content">
              <span
                class="info-box-text"
                @click.prevent="showDetailSubscription(subscription)"
              >
                <span
                  >{{ subscription.pass.name }}
                  {{ subscription.establishment.sigle }}</span
                >
                <span
                  v-if="subscription.pass.PassCategory == 'trimester'"
                  class="d-block"
                >
                  {{ subscription.num_trimester
                  }}<sup>{{
                    subscription.num_trimester == 1 ? "er" : "e"
                  }}</sup>
                  Trimestre
                </span>
                <!-- nombre de semaine -->
                <span
                  v-if="subscription.pass.PassCategory == 'trimester'"
                  class="d-block nbr-session"
                >
                  Nombre de semaines : {{ subscription.activities.length }}
                </span>
                <span v-else class="d-block nbr-session">
                  Nombre de séances : {{ subscription.activities.length }}</span
                >
                <!-- end -- nombre de semaine -->
              </span>
              <span class="info-box-number text-center row w-100">
                <span
                  class="col-md-6"
                  :class="{
                    'col-12': subscription.activities.length == 1,
                    'col-md-6': subscription.activities.length > 1
                  }"
                >
                  {{ dateFrMin(subscription.date?.start) }}
                </span>
                <span
                  v-if="subscription.activities.length > 1"
                  class="col-md-6"
                >
                  {{ dateFrMin(subscription.date?.end) }}
                </span>
              </span>
              <div class="progress">
                <div
                  class="progress-bar"
                  :style="
                    'width: ' + subscription.date?.days_left_percent + '%'
                  "
                ></div>
              </div>
              <span
                class="
                  progress-description
                  text-center
                  d-flex
                  justify-content-between
                "
              >
                <span v-if="subscription.status == 'in_progress'"
                  >En cours</span
                >
                <span v-if="subscription.status == 'futur'"
                  >début: {{ subscription.date?.elapsetime_start }}</span
                >
                <span v-else>fin: {{ subscription.date?.elapsetime_end }}</span>
              </span>
              <button
                class="btn btn-success"
                @click="showModalAddPayment(subscription.bill_id)"
              >
                <i class="fa fa-money-check-alt"></i> Payer
              </button>
              <modal-form-payment
                v-if="activeAddPaymentShowId == subscription.bill_id"
                :payment_methods="payment_methods"
                :bill="subscription.bill"
              />
            </div>
          </a>
          <modal-detail-subscription :subscription="subscription" />
          <modal-invoice
            v-if="activeBillDetailSubscriptionIdDisplayed == subscription.id"
            :bill_id="subscription.bill_id"
            :subscription_id="subscription.id"
          />
        </div>
      </template>

      <div
        v-if="
          subscriptions &&
            subscriptions.length &&
            subscriptions_pagination_data.last_page >= next_page
        "
        class="col-12"
      >
        <div class="row">
          <div class="mx-auto">
            <button
              class="btn btn-outline-success btn-sm"
              @click.prevent="getSubscriptions(user_id)"
              :disabled="loadSubscriptions"
              :class="{
                disabled: loadSubscriptions
              }"
            >
              plus... <loadings :processing="loadSubscriptions" />
            </button>
            <inertia-link
              class="btn btn-outline-info btn-sm ml-3"
              :href="
                route('subscriptions.index', { q: 'client_id:' + user_id })
              "
              data-toggle="tooltip"
              title="Afficher tous les souscriptions"
              >Tous</inertia-link
            >
          </div>
        </div>
      </div>
    </template>
    <div v-else class="col-sm-12 alert alert-info text-center">
      Souscription vide
    </div>
  </div>
</template>

<script>
import ModalDetailSubscription from "@/Pages/Components/Subscription/ModalDetailSubscription.vue";
import ModalInvoice from "@/Pages/Components/ModalInvoice.vue";
import ModalFormPayment from "@/Pages/Invoice/Unpaid/ModalFormPayment.vue";

export default {
  props: ["user_id"],
  components: {
    ModalDetailSubscription,
    ModalInvoice,
    ModalFormPayment
  },
  data() {
    return {
      subscriptions: null,
      next_page: 1,
      loadSubscriptions: false,
      subscriptions_pagination_data: null,
      activeSubscriptionDetailDisplayed: Object,
      activeBillDetailSubscriptionIdDisplayed: null,
      activeAddPaymentShowId: null,
      payment_methods: []
    };
  },
  mounted() {
    this.getSubscriptions(this.user_id);
    this.getPaymentMethods();
  },
  methods: {
    showModalAddPayment(bill_id) {
      this.activeAddPaymentShowId = bill_id;
      setTimeout(() => {
        $("#add-payment" + bill_id).modal();
      }, 200);
    },
    getPaymentMethods() {
      axios.get(route("api.payments.methods")).then(response => {
        if (response.data) {
          this.payment_methods = this.toSelect(response.data, "name");
        }
      });
    },
    getSubscriptions(user_id = null) {
      this.loadSubscriptions = true;
      axios
        .get(
          route("api.subscription.list", {
            user_id: user_id,
            and_with: [
              "activities.absence_prevention",
              "activities.absence_prevention.queue",
              "activities.absence_prevention.queue.recuperation_request"
            ],
            _query: {
              per_page: 3,
              page: this.next_page
            }
          })
        )
        .then(response => {
          if (response.data.data) {
            if (this.next_page == 1) {
              if (!Array.isArray(response.data.data)) {
                this.subscriptions = Object.keys(response.data.data).map(
                  key => response.data.data[key]
                );
              } else {
                this.subscriptions = response.data.data;
              }
              this.subscriptions_pagination_data = response.data;
            } else {
              this.subscriptions = [
                ...this.subscriptions,
                ...response.data.data
              ];
            }
            setTimeout(() => {
              this.initTooltipe();
            }, 2000);
          }
          this.next_page++;
          this.loadSubscriptions = false;
        });
    },
    showDetailSubscription(subscription) {
      this.activeSubscriptionDetailDisplayed = subscription;
      this.$emitter.emit("get-subscription-comments" + subscription.id);
      $("#modal-detail-subscription" + subscription.id).modal();
    },
    showDetailInvoice(subscription) {
      this.activeBillDetailSubscriptionIdDisplayed = subscription.id;
      setTimeout(() => {
        $("#modal-detail-invoice-" + subscription.bill_id).modal();
      }, 50);
    }
  }
};
</script>

<style scoped lang="scss">
.info-box {
  &.finished {
    background-color: rgb(96, 105, 101) !important;
  }

  .info-box-content {
    max-width: calc(100% - 30px);
  }

  .info-box-icon {
    width: 30px;
  }

  .info-box-payment {
    position: absolute;
    opacity: 0.9;
  }

  .info-box-number {
    font-size: 12px;
    font-weight: 400;
  }

  .progress-description,
  .nbr-session {
    font-size: 12px;
  }

  &:hover {
    cursor: pointer;
    text-decoration: none;
  }
}
</style>
