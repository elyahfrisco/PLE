<template>
  <form>
    <div class="row">
      <div class="col-lg-6">
        <div class="form-group" v-if="auth_user.role_name == 'admin'">
          <label
            >Client
            <inertia-link
              :href="
                route('customers.create', { create_for_subscription: true })
              "
              class="no-account"
              v-if="!$page.props.user_id"
            >
              ( pas encore du compte ? )</inertia-link
            >
          </label>
          <Multiselect
            v-model="form.user_id"
            :options="
              async function(query) {
                return await getUsers(query);
              }
            "
            placeholder="--Saisir le nom/prenom/mail du client--"
            :filterResults="false"
            :resolveOnLoad="false"
            :minChars="2"
            :searchable="true"
            :canDeselect="false"
            :disabled="disableInput || isRenewal"
            :loading="loadUsers"
            delay="0"
            trackBy="full_name"
            label="full_name"
            valueProp="id"
            ref="selectUser"
            required
          />
          <!--
            -->
          <small class="validation-error" v-if="errors.user_id">{{
            errors.user_id
          }}</small>
        </div>
        <div class="row">
          <div class="form-group col-md-6">
            <label>Centre</label>
            <Multiselect
              v-model="form.establishment_id"
              placeholder="--centre--"
              :options="select.establishments"
              :searchable="true"
              class="text-capitalize"
              :canDeselect="false"
              :disabled="disableInput || isRenewal"
              @change="getSeasons"
              required
            />
            <small class="validation-error" v-if="errors.establishment_id">{{
              errors.establishment_id
            }}</small>
          </div>
          <div class="form-group col-md-6">
            <label>Saison</label>
            <Multiselect
              v-model="form.season_id"
              placeholder="--saison--"
              :options="select.seasons"
              :searchable="true"
              class="text-capitalize"
              :loading="loadSeasons"
              required
              :disabled="disableInput || isRenewal || !form.establishment_id"
              :canDeselect="false"
              @change="
                () => {
                  getPassesCategories();
                  verifyRegistrationFees();
                }
              "
            />
            <small class="validation-error" v-if="errors.season_id">{{
              errors.season_id
            }}</small>
          </div>
        </div>

        <div class="form-group">
          <label>Type d'inscription</label>
          <div class="">
            <div class="icheck-success d-inline">
              <input
                type="radio"
                value="adult"
                id="adult"
                :disabled="isRenewal || !form.season_id"
                v-model="form.subscription_type"
              />
              <label for="adult">Adulte</label>
            </div>
            <div class="icheck-success d-inline ml-4">
              <input
                type="radio"
                value="child"
                id="child"
                :disabled="isRenewal || !form.season_id"
                v-model="form.subscription_type"
              />
              <label for="child">Enfant</label>
            </div>
          </div>
        </div>

        <span
          class="text-danger d-block"
          v-if="
            auth_user.role_name == 'admin' &&
              form.not_having_paid_registration_fees === true &&
              form.season_id != null
          "
          >Le client n'a pas encore payé son frais d'inscription au centre</span
        >
        <span
          class="text-danger d-block"
          v-if="
            data_management_fees.not_having_paid === true &&
              !form.management_fees_manual_added &&
              seasonActive?.id
          "
        >
          Le client n'a pas encore payé son frais de gestion pour la saison
          {{ seasonActive.label }}
        </span>

        <div
          class="form-group mt-1"
          v-if="
            auth_user.role_name == 'admin' &&
              form.not_having_paid_registration_fees === true &&
              form.season_id
          "
        >
          <label class="d-block">Type de frais d'inscription</label>
          <div class="">
            <div class="icheck-success d-inline">
              <input
                type="radio"
                value="normal"
                id="normal"
                v-model="form.type_of_fees"
              />
              <label for="normal"
                >Normal
                <span v-if="!isNaN(season_registration_fees.price)"
                  >( {{ season_registration_fees.price }}€ )</span
                ><span v-else>( non paramètré )</span></label
              >
            </div>
            <div class="icheck-success d-inline ml-4">
              <input
                type="radio"
                value="reduced"
                id="reduced"
                v-model="form.type_of_fees"
              />
              <label for="reduced"
                >Reduit
                <span v-if="!isNaN(season_registration_fees.reduced_price)"
                  >( {{ season_registration_fees.reduced_price }}€ )</span
                ><span v-else>(no parametré)</span></label
              >
            </div>
            <div class="icheck-success d-inline ml-4">
              <input
                type="radio"
                value="offered"
                id="offered"
                v-model="form.type_of_fees"
              />
              <label for="offered">Offert</label>
            </div>
          </div>
        </div>

        <div class="form-group mt-1" v-if="auth_user.role_name == 'admin'">
          <label class="d-block">Réduction activité</label>
          <div class="">
            <div class="icheck-success d-inline">
              <input
                type="radio"
                value="normal"
                id="reduction_normal"
                v-model="form.reduction"
                :disabled="isRenewalAndSessionsSelected || !form.season_id"
              />
              <label for="reduction_normal">Normal</label>
            </div>
            <div class="icheck-success d-inline ml-4">
              <input
                type="radio"
                value="reduced"
                id="reduction_reduced"
                v-model="form.reduction"
                :disabled="isRenewalAndSessionsSelected || !form.season_id"
              />
              <label for="reduction_reduced">Reduit</label>
            </div>
            <div class="icheck-success d-inline ml-4">
              <input
                type="radio"
                value="reduced2"
                id="reduction_reduced2"
                v-model="form.reduction"
                :disabled="isRenewalAndSessionsSelected || !form.season_id"
              />
              <label for="reduction_reduced2">Reduit 2</label>
            </div>
          </div>
        </div>

        <div class="form-group pass-category-dispo mb-1">
          <div class="row mx-0">
            <div
              class="btn-group w-100"
              role="group"
              aria-label="Basic example"
            >
              <label
                v-if="auth_user.role_name != 'customer'"
                for="trimester"
                class="btn btn-outline-success"
                :class="{
                  active: pass_type == 'trimester',
                  disabled:
                    !passCategories.trimester ||
                    (loadPasses && pass_type != 'trimester') ||
                    isRenewal
                }"
              >
                <input
                  type="radio"
                  value="trimester"
                  id="trimester"
                  class="d-none"
                  :disabled="
                    !passCategories.trimester ||
                      (loadPasses && pass_type != 'trimester') ||
                      isRenewal
                  "
                  v-model="pass_type"
                />
                <loadings :processing="loadPassesCategories" />
                Pass trimestriel</label
              >
              <label
                for="decouvert"
                class="btn btn-outline-success"
                :class="{
                  active: pass_type == 'decouvert',
                  disabled:
                    !passCategories.decouvert ||
                    (loadPasses && pass_type != 'decouvert') ||
                    isRenewal
                }"
              >
                <input
                  type="radio"
                  value="decouvert"
                  id="decouvert"
                  :disabled="
                    !passCategories.decouvert ||
                      (loadPasses && pass_type != 'decouvert') ||
                      isRenewal
                  "
                  class="d-none"
                  v-model="pass_type"
                />
                <loadings :processing="loadPassesCategories" />
                Pass decouverte
              </label>
              <label
                for="one_session"
                class="btn btn-outline-success"
                :class="{
                  active: pass_type == 'one_session',
                  disabled:
                    !passCategories.oneSession ||
                    (loadPasses && pass_type != 'one_session') ||
                    isRenewal
                }"
              >
                <input
                  type="radio"
                  value="one_session"
                  id="one_session"
                  :disabled="
                    !passCategories.oneSession ||
                      (loadPasses && pass_type != 'one_session') ||
                      isRenewal
                  "
                  class="d-none"
                  v-model="pass_type"
                />
                <loadings :processing="loadPassesCategories" />
                Séance à l’unité
              </label>
              <label
                v-if="form.subscription_type == 'adult'"
                for="other"
                class="btn btn-outline-success"
                :class="{
                  active: pass_type == 'other',
                  disabled:
                    !passCategories.other ||
                    (loadPasses && pass_type != 'other') ||
                    isRenewal
                }"
              >
                <input
                  type="radio"
                  value="other"
                  id="other"
                  class="d-none"
                  v-model="pass_type"
                  :disabled="
                    !passCategories.other ||
                      (loadPasses && pass_type != 'other') ||
                      isRenewal
                  "
                />
                <loadings :processing="loadPassesCategories" />
                PASS PRIVILEGES</label
              >
            </div>
          </div>
        </div>

        <div class="form-group">
          <label
            >Liste Pass
            <span>{{ pass_list_title }}</span>
          </label>

          <div
            class="btn-group w-100 pass-dispo"
            role="group"
            aria-label="Basic example"
          >
            <loadings :processing="loadPasses" class_="mt-3 mx-auto" />
            <template v-for="pass in select.passes" :key="pass.id">
              <label
                :for="'pass' + pass.value"
                :id="'f_pass' + pass.value"
                class="btn btn-warning"
                :class="{
                  active: form.pass_id == pass.id,
                  disabled: isRenewal
                }"
                :disabled="isRenewal"
              >
                <input
                  type="radio"
                  :value="pass.id"
                  :id="'pass' + pass.value"
                  class="d-none"
                  v-model="form.pass_id"
                  @click="getDaysActivitiesPass"
                  :class="{
                    disabled: isRenewal
                  }"
                  :disabled="isRenewal"
                />
                {{ pass.label }}
                <span v-if="pass_type == 'other' || pass_type == 'decouvert'"
                  >{{ pass.price ? pass.price.price : "_" }}€</span
                ></label
              >
            </template>
          </div>
          <small class="validation-error" v-if="errors.pass_id">{{
            errors.pass_id
          }}</small>
        </div>

        <div
          :class="{
            'choice-disabled': passActive.name == undefined
          }"
        >
          <label
            >Choix horaire
            <span v-if="passActive.name"
              >: {{ passActive.name }}
              <span v-if="pass_type == 'other' || pass_type == 'decouvert'">
                - {{ passActive.price ? passActive.price.price : "_" }}€</span
              ></span
            >
          </label>
          <template v-for="id in formNumber" :key="id">
            <schedule-choice-form
              :passChanged="loadDaysActivitiesPass"
              :season="seasonActive"
              :pass="passActive"
              :pass_type="pass_type"
              :sessionsInfo="sessionsInfo"
              :reduction="form.reduction"
              :id_="id"
              v-on:showSessionCalendar="showCalendar"
              v-on:addSessionsActivityToCart="addSessionsActivityToCart"
            />
          </template>
          <jet-modal
            id="planningCalendar"
            :title="
              'Calendrier des séances : ' +
                (passActive.name != undefined
                  ? passActive.name.toUpperCase()
                  : '')
            "
            maxWidth="xl"
            minHeight="600px"
          >
            <SubscriptionSessionCalendar
              v-if="passActive.id"
              :season="seasonActive"
              :establishment="establishmentActive"
              :filter="filterCalendar"
              :pass="passActive"
              :user_id="form.user_id"
              :reduction="form.reduction"
              :cartIdSelectedSessions="cartIdSelectedSessions"
              @setSubscriptionPassInfo="setSubscriptionPassInfo"
            />
          </jet-modal>
        </div>
      </div>
      <div class="col-lg-6">
        <activity-cart
          v-model:subscriptionData="form.subscriptionData"
          v-model:registration_fees="data_registration_fees"
          v-model:management_fees="data_management_fees"
          :subscriptionData="form.subscriptionData"
          :form_data="{
            establishment_id: form.establishment_id,
            season_id: form.season_id
          }"
          :registration_fees="data_registration_fees"
          :management_fees="data_management_fees"
          @discount_registration_fees="discount_registration_fees"
          @discount_management_fees="discount_management_fees"
        />

        <div class="row" v-if="totalAmount > 0">
          <div class="col-md-6">
            <div class="form-group">
              <label>Date prévisible de règlement</label>
              <datepicker
                :lower-limit="payment.minDate"
                :upper-limit="payment.maxDate"
                :disabled-dates="payment.disabledDates"
                v-model="form.predictable_payment_date"
                class="form-control"
              />
              <small v-if="payment.maxDate" class="text-muted">
                max {{ dateFr(payment.maxDate) }}
              </small>
              <small
                class="validation-error d-block"
                v-if="errors.predictable_payment_date"
                >{{ errors.predictable_payment_date }}</small
              >
            </div>
          </div>
          <div class="col-md-6">
            <div class="form-group">
              <label>Mode de règlement</label>

              <Multiselect
                v-model="form.payment_method"
                :options="select.payment_methods"
                :canDeselect="false"
              />

              <small class="validation-error" v-if="errors.payment_method">{{
                errors.payment_method
              }}</small>
            </div>
          </div>
        </div>

        <div class="row">
          <button
            type="button"
            @click.prevent="submit"
            class="btn btn-success mx-auto"
            v-if="!loadSubmit"
            :disabled="form.subscriptionData.length == 0"
          >
            Souscrire
          </button>
          <button v-else type="button" class="btn btn-info mx-auto">
            Traitement...
          </button>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import scheduleChoiceForm from "./scheduleChoiceForm.vue";
import SubscriptionSessionCalendar from "./SubscriptionSessionCalendar.vue";
import ActivityCart from "../cart.vue";

export default {
  props: ["establishments", "customers", "user_id"],
  components: {
    scheduleChoiceForm,
    SubscriptionSessionCalendar,
    ActivityCart
  },
  data() {
    return {
      form: {
        day: null,
        subscription_type: "adult",
        type_of_fees: "normal",
        reduction: "normal",
        establishment_id: null,
        season_id: null,
        pass_id: null,
        user_id: null,
        subscriptionData: [],
        not_having_paid_registration_fees: null,
        not_having_paid_management_fees: null,
        payment_method: null,
        relaunch_date: null
      },
      select: Object,
      season_registration_fees: {},
      season_management_fees: {},
      pass_type: "trimester",
      establishmentActive: {},
      seasonActive: {},
      passActive: {},
      feesActive: {},
      loadUsers: false,
      loadSeasons: false,
      loadPassesCategories: false,
      loadPasses: false,
      loadDaysActivitiesPass: false,
      loadSubmit: false,
      passCategories: {
        trimester: false,
        other: false,
        oneSession: false,
        decouvert: false
      },
      filterCalendar: {},
      sessionsInfo: {},
      payment: {
        minDate: new Date(this.$moment().format()),
        maxDate: new Date(
          this.$moment()
            .add(6, "month")
            .format()
        ),
        disabledDates: []
      },
      initUser: false
    };
  },
  watch: {
    pass_type: {
      deep: true,
      handler() {
        this.getPasses();
      }
    },
    "form.subscription_type": {
      deep: true,
      handler() {
        setTimeout(() => {
          this.verifyManagementFees();
        }, 200);
      }
    },
    "form.user_id": {
      deep: true,
      handler() {
        setTimeout(() => {
          this.verifyRegistrationFees();
        }, 200);
      }
    }
  },
  computed: {
    minDate: () => {},
    maxDate: () => {},
    disableInput() {
      return this.form.subscriptionData.length > 0;
    },
    isRenewal() {
      return this.$page.props.renewal_id ? true : false;
    },
    isRenewalAndSessionsSelected() {
      return (
        (this.$page.props.renewal_id ? true : false) &&
        this.cartIdSelectedSessions.length
      );
    },
    pass_list_title() {
      switch (this.pass_type) {
        case "trimester":
          return ": Pass trimestriel";
          break;
        case "decouvert":
          return ": Passe decouverte";
          break;
        case "one_session":
          return ": Séance à l’unité";
          break;
        case "other":
          return ": PASS PRIVILEGES";
          break;
        default:
          return "";
          break;
      }
    },
    formNumber() {
      return 1;
    },
    data_registration_fees() {
      return {
        not_having_paid: this.form.not_having_paid_registration_fees,
        amount:
          this.form.type_of_fees === "normal"
            ? this.season_registration_fees.price
            : this.form.type_of_fees === "reduced"
            ? this.season_registration_fees.reduced_price
            : 0
      };
    },
    data_management_fees() {
      return {
        not_having_paid: this.form.not_having_paid_management_fees,
        amount: this.season_management_fees.price
      };
    },
    cartIdSelectedSessions() {
      var ids = [];
      for (var sub of this.form.subscriptionData) {
        // console.log(sub);
        for (var session of sub.data.sessions) {
          ids.push(session.id);
        }
      }
      return ids;
    },
    totalAmount() {
      console.log(this.form.subscriptionData);
      var total = 0;
      if (this.form.subscriptionData.length) {
        for (var passSubscription of this.form.subscriptionData) {
          total += passSubscription.data.amount * 1;
        }

        if (
          this.data_registration_fees.not_having_paid &&
          !isNaN(this.data_registration_fees.amount)
        ) {
          total += this.data_registration_fees.amount * 1;
        }

        if (
          this.data_management_fees.not_having_paid &&
          !isNaN(this.data_management_fees.amount)
        ) {
          total += this.data_management_fees.amount * 1;
        }
      }
      return total;
    }
  },
  beforeMount() {
    this.select.establishments = this.toSelect(this.establishments);
    this.select.customers = [];
    this.select.activities = [];
    this.form.relaunch_date = this.$moment().format("YYYY-MM-DD HH:mm");

    if (this.auth_user.role_name == "customer") {
      this.form.user_id = this.auth_user.id;
    } else {
      this.form.user_id = this.$page.props.user_id;
    }
    if (this.$page.props.subscription_type === "child")
      this.form.subscription_type = this.$page.props.subscription_type;

    if (this.$page.props.establishment_id) {
      this.form.establishment_id = this.$page.props.establishment_id;
      this.getSeasons(true);
    }

    if (this.$page.props.renewal_id) {
      this.form.renewal_id = this.$page.props.renewal_id;
      this.form.subscription_id = this.$page.props.subscription_id;
    }

    if (this.$page.props.pass_id) this.form.pass_id = this.$page.props.pass_id;
  },
  mounted() {
    setTimeout(() => {
      this.getDisabledDateForChoiceOfPredictablePaymentDate();
      this.getPaymentMethods();
      this.select.customers = [];

      if (this.form.user_id && this.$refs.selectUser) {
        this.initUser = true;
        this.$refs.selectUser.refreshOptions();
      }
    }, 100);

    this.$emitter.on("add_management_fees", this.AddManagementFees);
  },
  methods: {
    getUsers(q, init = false) {
      let attr = [];

      if (!q && !init && !this.initUser) {
        return this.select.customers;
      } else if (this.initUser) {
        q = this.form.user_id;
        attr = ["id"];
      }
      this.loadUsers = true;
      return axios
        .get(route("api.user.search"), {
          params: {
            q: q,
            attr: attr
          }
        })
        .then(response => {
          this.select.customers = response.data.data;
          this.loadUsers = false;

          if (this.initUser) {
            this.initUser = false;
            setTimeout(() => {
              this.$refs.selectUser.select(this.form.user_id);
            }, 100);
          }

          return this.select.customers;
        });
    },
    addSessionsActivityToCart(event) {
      this.form.subscriptionData.push(cp(event));
      setTimeout(() => {
        this.form.pass_id = null;
        this.passActive = {};
      }, 100);
    },
    getDisabledDateForChoiceOfPredictablePaymentDate() {
      axios
        .get(route("api.plannings.dates.disabled"), {
          params: {
            minDate: this.payment.minDate,
            maxDate: this.payment.maxDate
          }
        })
        .then(response => {
          if (response.data) {
            this.payment.disabledDates = response.data.map(d => new Date(d));
          }
        });
    },
    setSubscriptionPassInfo(event) {
      this.sessionsInfo = event;
    },
    showCalendar(event) {
      if (this.seasonActive.id != undefined) {
        this.filterCalendar = event;
        $("#planningCalendar").modal();
      }
    },
    submit() {
      this.loadSubmit = true;
      let form = { ...this.form };
      form.predictable_payment_date = form.predictable_payment_date
        ? this.dateHAng(this.form.predictable_payment_date)
        : null;

      if (this.totalAmount == 0) {
        form.predictable_payment_date = this.dateHAng();
        form.payment_method = "gratuit";
      }

      this.$inertia.post(
        route("subscriptions.store"),
        {
          ...form,
          ...{ amount_registration_fees: this.data_registration_fees.amount },
          ...{ amount_management_fees: this.data_management_fees.amount }
        },
        {
          onBefore: () => {
            if (this.form.subscriptionData.length != 0) {
              if (confirm("Confirmer la souscription")) return true;
              else {
                this.loadSubmit = false;
              }
            }
            return true;
          },
          onSuccess: () => {
            if (this.$page.props.renewal_id) {
              this.$inertia.visit(
                route("account.index", { user_id: this.$page.props.user_id })
              );
            } else {
              this.iReload();
              this.select.passes = [];
            }
          },
          onFinish: () => {
            this.loadSubmit = false;
          }
        }
      );
    },
    refreshPassButton() {
      if (this.passCategories.trimester) {
        this.pass_type =
          this.auth_user.role_name != "customer" ? "trimester" : "one_session";
      } else if (this.passCategories.oneSession) {
        this.pass_type = "one_session";
      } else if (this.passCategories.other) {
        this.pass_type = "other";
      } else if (this.passCategories.decouvert) {
        this.pass_type = "decouvert";
      }
      this.getPasses();
    },
    getSeasons(init = false) {
      setTimeout(() => {
        if (init !== true) {
          this.form.pass_id = null;
          this.select.seasons = [];
          this.form.season_id = null;
          this.seasonActive = {};
          this.select.passes = {};
          this.OValueTo(this.passCategories);
        }
        if (this.form.establishment_id) {
          this.loadSeasons = true;
          this.establishmentActive = this.iValue(
            this.select.establishments,
            this.form.establishment_id
          );
          axios
            .get(
              route(
                "api.establishments.seasons.unfinished",
                this.form.establishment_id
              )
            )
            .then(response => {
              if (response.data) {
                this.select.seasons = this.toSelect(
                  response.data,
                  "id",
                  "season"
                );
                if (this.$page.props.season_id) {
                  this.form.season_id = this.$page.props.season_id;
                  this.seasonActive = this.iValue(
                    this.select.seasons,
                    this.form.season_id
                  );
                  this.getPassesCategories(true);
                }
              } else {
                this.select.seasons = {};
              }
              this.loadSeasons = false;
            });
        } else {
          this.select.seasons = {};
          this.select.passes = {};
          this.select.activities = {};
          this.OValueTo(this.passCategories);
          this.passActive = {};
        }
      }, 100);
    },
    verifyRegistrationFees() {
      setTimeout(() => {
        if (!isNaN(this.form.user_id)) {
          axios
            .get(
              route("api.user.fees", {
                user_id: this.form.user_id,
                type: "registration",
                check: true
              })
            )
            .then(response => {
              this.form.not_having_paid_registration_fees = response.data
                ? false
                : true;
              if (this.form.not_having_paid_registration_fees) {
                this.getSeasonRegistrationFees();
              } else {
                this.verifyManagementFees();
              }
            });
        } else {
          this.form.not_having_paid_registration_fees = false;
        }
      }, 100);
    },
    verifyManagementFees() {
      setTimeout(() => {
        this.form.management_fees_manual_added = false;
        if (
          this.form.not_having_paid_registration_fees ||
          this.form.not_having_paid_registration_fees == null
        ) {
          this.getSeasonRegistrationFees();
          return 0;
        }
        if (!isNaN(this.form.season_id) && !isNaN(this.form.user_id)) {
          axios
            .get(
              route("api.user.fees", {
                season_id: this.form.season_id,
                user_id: this.form.user_id,
                type: "management",
                check: true
              })
            )
            .then(response => {
              this.form.not_having_paid_management_fees = response.data
                ? false
                : true;

              if (this.form.not_having_paid_management_fees) {
                this.getSeasonManagementFees();
              }
            });
        } else {
          this.form.not_having_paid_management_fees = false;
        }
      }, 100);
    },
    AddManagementFees() {
      this.form.not_having_paid_management_fees = true;
      this.form.management_fees_manual_added = true;
      this.getSeasonManagementFees();
    },
    getSeasonRegistrationFees() {
      setTimeout(() => {
        if (
          this.form.season_id != null &&
          !isNaN(this.form.season_id) &&
          this.form.not_having_paid_registration_fees
        ) {
          axios
            .get(
              route("api.seasons.fees", {
                season: this.form.season_id,
                category: this.form.subscription_type,
                type: "registration",
                first: true
              })
            )
            .then(response => {
              if (response.data) {
                this.season_registration_fees = response.data;
              } else {
                this.season_registration_fees = {};
              }
            });
        } else {
          this.season_registration_fees = {};
        }
      }, 100);
    },
    getSeasonManagementFees() {
      setTimeout(() => {
        if (
          this.form.season_id != null &&
          !isNaN(this.form.season_id) &&
          this.form.not_having_paid_management_fees
        ) {
          axios
            .get(
              route("api.seasons.fees", {
                season: this.form.season_id,
                category: this.form.subscription_type,
                type: "management",
                first: true
              })
            )
            .then(response => {
              if (response.data) {
                this.season_management_fees = response.data;
              } else {
                this.season_management_fees = {};
              }
            });
        } else {
          this.season_management_fees = {};
        }
      }, 100);
    },
    getPassesCategories(init) {
      setTimeout(() => {
        if (!init) {
          this.form.pass_id = null;
        }
        if (this.form.season_id) {
          this.loadPassesCategories = true;
          axios
            .get(route("api.seasons.passes.categories", this.form.season_id))
            .then(response => {
              this.passCategories = response.data;
              this.loadPassesCategories = false;

              if (this.$page.props.pass_type) {
                this.pass_type = this.$page.props.pass_type;
                this.getPasses(true);
              } else {
                this.refreshPassButton();
              }
            });
          this.seasonActive = this.iValue(
            this.select.seasons,
            this.form.season_id
          );
        } else {
          this.select.passes = {};
          this.select.activities = {};
          this.passCategories = this.OValueTo(this.passCategories, false);
          this.seasonActive = {};
        }
      }, 100);
    },
    getPasses(init) {
      setTimeout(() => {
        if (!init) {
          this.form.pass_id = null;
          this.passActive = {};
        }

        if (this.form.season_id) {
          this.select.passes = {};
          this.loadPasses = true;
          axios
            .get(route("api.seasons.passes", this.form.season_id), {
              params: {
                pass_type: this.pass_type,
                season_id: this.form.season_id
              }
            })
            .then(response => {
              this.select.passes = this.toSelect(response.data);
              if (this.$page.props.pass_id) {
                this.form.pass_id = this.$page.props.pass_id;
                this.passActive = this.iValue(
                  this.select.passes,
                  this.form.pass_id
                );
              } else if (
                this.pass_type == "trimester" &&
                this.select.passes.length == 1
              ) {
                setTimeout(() => {
                  $("#f_pass" + this.select.passes[0].id).click();
                }, 100);
              }

              this.loadPasses = false;
            });
        } else {
          this.select.passes = {};
          this.select.daysActivitiesPass = {};
        }
        this.select.daysActivitiesPass = {};
      }, 100);
    },

    getDaysActivitiesPass(init) {
      setTimeout(() => {
        if (this.form.pass_id != undefined) {
          this.loadDaysActivitiesPass = true;
          axios
            .get(
              route("api.plannings", {
                season_id: this.form.season_id,
                pass_id: this.form.pass_id,
                group_day: true,
                organized: true
              })
            )
            .then(response => {
              this.select.daysActivitiesPass = response.data.map(a => {
                return {
                  value: a.day,
                  label: this.dayfr(a.day)
                };
              });
              this.loadDaysActivitiesPass = false;
              this.namePassSelected = $("#f_pass" + this.form.pass_id).text();
            });
          this.passActive = this.iValue(this.select.passes, this.form.pass_id);
        } else {
          this.select.daysActivitiesPass = [];
          this.passActive = {};
        }
      }, 100);
    },
    getPaymentMethods() {
      axios.get(route("api.payments.methods")).then(response => {
        if (response.data) {
          this.select.payment_methods = this.toSelect(response.data, "name");
        }
      });
    },
    showModalCreateAccount() {
      $("#modal-create-customer-account").modal();
    },

    discount_registration_fees() {
      this.form.not_having_paid_registration_fees = false;
    },
    discount_management_fees() {
      this.form.not_having_paid_management_fees = false;
    }
  }
};
</script>
<style scoped lang="scss">
@import "@/Pages/Components/main.scss";

.pass-dispo {
  .btn-warning:not(.active),
  .btn-warning:not(.active):hover {
    background-color: $color-pass;
    border-color: $color-lpdl !important;
    color: $color-lpdl;
  }
}

.btn-warning:not(:disabled):not(.disabled):active,
.btn-warning:not(:disabled):not(.disabled).active,
.show > .btn-warning.dropdown-toggle {
  background-color: $color-lpdl;
  border-color: $color-lpdl;
  color: white;
}

.btn-group {
  min-height: 45px;
}

.choice-disabled {
  opacity: 0.8;
  filter: blur(1px);
}

.no-account {
  font-weight: normal !important;
}
</style>
