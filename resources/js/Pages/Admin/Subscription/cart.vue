<template>
  <p class="mb-0">
    <label for="">
      - Panier <span>( Total : {{ totalCart }}€ )</span></label
    >
  </p>
  <table class="table min">
    <thead>
      <tr>
        <th>Pass</th>
        <th>Activité</th>
        <th>Jour</th>
        <th>Date</th>
        <th>Horaire</th>
        <th>
          Prix
          <template v-if="auth_user.role_name != 'customer'">
            <button
              type="button"
              @click.prevent="discountPriceAll()"
              class="ml-2 float-right btn btn-sm border-0 btn-outline-danger"
              data-toggle="tooltip"
              title="Gratuit"
              :disabled="!subscriptionData.length"
            >
              <i class="fab fa-creative-commons-nc-eu"></i> Gratuit
            </button>
          </template>
        </th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <template v-if="subscriptionData.length > 0">
        <template v-for="(passSubscription, i) in subscriptionData" :key="i">
          <tr
            v-for="(sessionSubscription, j) in passSubscription.data.sessions"
            :key="j"
            :class="{
              'row-end-list-pass':
                passSubscription.data.sessions.length - 1 == j
            }"
          >
            <template v-if="passSubscription.data.pass_type == 'other'">
              <td
                v-if="j == 0"
                :rowspan="passSubscription.data.sessions.length"
                class="col-other-pass"
              >
                {{ passSubscription.display.pass }}
              </td>
            </template>
            <td v-else>
              {{ passSubscription.display.pass }}
            </td>
            <td>
              {{ sessionSubscription.display.activity }}
            </td>
            <td>
              {{ sessionSubscription.display.day }}
            </td>
            <td>
              {{ sessionSubscription.display.date }}
            </td>
            <td>
              {{ sessionSubscription.display.planning }}
            </td>
            <template v-if="passSubscription.data.pass_type == 'other'">
              <td
                v-if="j == 0"
                :rowspan="passSubscription.data.sessions.length"
                class="col-other-pass"
              >
                {{ passSubscription.data.amount }}€
              </td>
            </template>
            <td v-else>
              <!-- <span> {{ sessionSubscription.price.price }}€ </span> -->
              <span> {{ passSubscription.data.amount }}€ </span>
            </td>
            <template v-if="passSubscription.data.pass_type == 'other'">
              <td
                v-if="j == 0"
                class="text-right col-other-pass"
                :rowspan="passSubscription.data.sessions.length"
              >
                <button
                  class="btn btn-outline-danger py-0 px-1 btn-sm"
                  data-toggle="tooltip"
                  title="Retirer le Pass"
                  @click.prevent="removeFromCart(i, j)"
                >
                  <!-- correction -->
                  <i class="fa fa-trash"></i>
                </button>
              </td>
            </template>
            <td v-else class="text-right">
              <button
                class="btn btn-outline-danger py-0 px-1 btn-sm"
                data-toggle="tooltip"
                title="Retirer"
                @click.prevent="removeFromCart(i, j)"
              >
                <i class="fa fa-trash"></i>
              </button>
              <template v-if="auth_user.role_name != 'customer'">
                <button
                  v-if="!sessionSubscription.price.price_discounted"
                  type="button"
                  @click.prevent="discountPrice(i, j)"
                  class="
                    ml-2
                    float-right
                    btn btn-sm
                    border-0
                    btn-outline-danger
                  "
                  data-toggle="tooltip"
                  title="Remise de prix"
                >
                  <i class="fab fa-creative-commons-nc-eu"></i>
                </button>
                <button
                  v-else
                  type="button"
                  @click.prevent="resetPrice(i, j)"
                  class="
                    ml-2
                    float-right
                    btn btn-sm
                    border-0
                    btn-outline-success
                  "
                  data-toggle="tooltip"
                  title="Réinitialiser le prix"
                >
                  <i class="fa fa-undo"></i>
                </button>
              </template>
            </td>
          </tr>
        </template>
      </template>
      <template v-else>
        <tr>
          <td colspan="5" class="text-center">
            Panier vide
            <small
              class="validation-error d-block"
              v-if="errors.subscriptionData"
              >{{ errors.subscriptionData }}</small
            >
          </td>
        </tr>
      </template>
    </tbody>
  </table>

  <div v-if="registration_fees.not_having_paid === true">
    <p class="mb-0">
      <label for="">
        - Frais d'inscription :
        <span v-if="!isNaN(registration_fees.amount)"
          >{{ registration_fees.amount }}€</span
        ><span v-else>( non paramètré )</span>
        <button
          v-if="auth_user.role_name == 'admin'"
          class="btn btn-outline-warning py-0 text-danger ml-1 px-1 btn-sm"
          data-toggle="tooltip"
          title="Retirer le frais"
          @click.prevent="removeRegistrationFees()"
        >
          <i class="fa fa-trash"></i>
        </button>
      </label>
    </p>
  </div>
  <template v-if="form_data.season_id">
    <div v-if="management_fees.not_having_paid === true">
      <p class="mb-0">
        <label for="">
          - Frais de gestion :
          <span v-if="!isNaN(management_fees.amount)"
            >{{ management_fees.amount }}€</span
          ><a
            target="_blank"
            :href="
              route('establishments.seasons.index', {
                establishment: form_data.establishment_id,
                management_fee_season_id: form_data.season_id
              })
            "
            v-else
            >( non paramètré )</a
          >
          <button
            v-if="auth_user.role_name == 'admin'"
            class="btn btn-outline-warning py-0 text-danger ml-1 px-1 btn-sm"
            data-toggle="tooltip"
            title="Retirer le frais"
            @click.prevent="removeManagementFees()"
          >
            <i class="fa fa-trash"></i>
          </button>
        </label>
      </p>
    </div>
    <button
      v-else
      class="btn btn-outline-success btn-sm"
      data-toggle="tooltip"
      title="Ajouter le frais de gestion"
      @click.prevent="() => $emitter.emit('add_management_fees')"
    >
      <i class="fa fa-euro-sign mr-1"></i> Ajouter le frais de gestion
    </button>
  </template>
</template>

<script>
export default {
  props: [
    "subscriptionData",
    "form_data",
    "registration_fees",
    "management_fees"
  ],
  methods: {
    removeFromCart(iPass, iSession = null) {
      var subs_ = cpp(this.subscriptionData);
      if (iSession || iSession === 0) {
        subs_[iPass].data.sessions.splice(iSession, 1);
      } else {
        subs_.splice(iPass, 1);
      }
      this.$emit("update:subscriptionData", subs_);
      this.clearTooltip();
    },
    removeRegistrationFees() {
      this.$emit("discount_registration_fees");
      this.$emit("update:registration_fees", {
        amount: null,
        not_having_paid: null
      });
      this.clearTooltip();
    },
    removeManagementFees() {
      this.$emit("discount_management_fees");
      this.$emit("update:management_fees", {
        amount: null,
        not_having_paid: null
      });
      this.clearTooltip();
    },
    discountPriceAll() {
      var subs_ = cpp(this.subscriptionData);
      for (let iPass in this.subscriptionData) {
        for (let index in this.subscriptionData[iPass].data.sessions) {
          subs_[iPass].data.sessions[index].price.price_ =
            subs_[iPass].data.sessions[index].price.price;
          subs_[iPass].data.sessions[index].price.price = 0;
          subs_[iPass].data.sessions[index].price.price_discounted = true;
        }
      }
      this.$emit("update:subscriptionData", subs_);
      this.clearTooltip();
    },

    discountPrice(iPass, index) {
      var subs_ = cpp(this.subscriptionData);

      subs_[iPass].data.sessions[index].price.price_ =
        subs_[iPass].data.sessions[index].price.price;
      subs_[iPass].data.sessions[index].price.price = 0;
      subs_[iPass].data.sessions[index].price.price_discounted = true;

      this.$emit("update:subscriptionData", subs_);
      this.clearTooltip();
    },
    resetPrice(iPass, index) {
      var subs_ = cpp(this.subscriptionData);

      subs_[iPass].data.sessions[index].price.price =
        subs_[iPass].data.sessions[index].price.price_;
      subs_[iPass].data.sessions[index].price.price_discounted = false;

      this.$emit("update:subscriptionData", subs_);
      this.clearTooltip();
    }
  },
  watch: {
    subscriptionData: function() {
      if (this.subscriptionData.length) {
        for (var passSubscription of this.subscriptionData) {
          if (
            passSubscription.data.pass_type == "trimester" ||
            passSubscription.data.pass_type == "one_session"
          ) {
            passSubscription.data.amount = 0;
            for (var session of passSubscription.data.sessions) {
              passSubscription.data.amount += session.price.price * 1;
            }
          }
        }
        setTimeout(() => {
          this.initTooltipe();
        }, 200);
      }
    }
  },
  computed: {
    totalCart() {
      var total = 0;
      if (this.subscriptionData.length) {
        for (var passSubscription of this.subscriptionData) {
          total += passSubscription.data.amount * 1;
        }
      }

      if (
        this.registration_fees.not_having_paid &&
        !isNaN(this.registration_fees.amount)
      ) {
        total += this.registration_fees.amount * 1;
      }

      if (
        this.management_fees.not_having_paid &&
        !isNaN(this.management_fees.amount)
      ) {
        total += this.management_fees.amount * 1;
      }

      return total;
    }
  }
};
</script>

<style>
.col-other-pass {
  vertical-align: middle !important;
  border-bottom: 1px solid #333;
}
.row-end-list-pass {
  border-bottom: 1px solid #333;
}
.row-end-list-pass + tr td {
  border-top: 1px solid #333;
}
</style>
