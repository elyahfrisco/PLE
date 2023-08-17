<template>
  <div class="row mx-0" v-if="pass_type == 'trimester'">
    <div v-if="empty_session_for_renewal" class="alert alert-danger">
      Aucune séance trouvée pour le renouvellement. Assurez-vous que les
      plannings sont bien planifiés pour la saison et le trimestre de
      renouvellement.
    </div>
    <div class="form-group w-50">
      <label>N° Trimestre</label>
      <Multiselect
        v-model="form.num_trimester"
        placeholder="--trimestre--"
        :options="select.trimesters"
        :searchable="true"
        class="text-capitalize"
        :loading="loadTrimesters"
        :disabled="isRenewal"
      />
      <small class="validation-error" v-if="errors.trimester_num">{{
        errors.trimester_num
      }}</small>
    </div>
  </div>
  <div class="row mx-0" v-if="pass_type == 'other' || pass_type == 'decouvert'">
    Vous pouvez choisir {{ pass.number_sessions }} séances entre :
    <span
      class="mr-1"
      v-for="(activity, i) in pass.activities"
      :key="activity.id"
      ><u>{{ activity.name }}</u
      ><i v-if="pass.activities.length - 2 == i"> et </i
      ><i v-else-if="pass.activities.length - 1 != i">,</i></span
    >
    . Ou au moins 1 séance, et pour les restes vous pouvez prendre rendez-vous
    au fur et à mesure
  </div>
  <div class="row mx-0">
    <button
      type="button"
      @click.prevent="showPlanningCalendar"
      class="btn btn-choice-session text-dark"
      :class="{
        'btn-outline-success':
          (pass.id != undefined && pass_type != 'trimester') ||
          (pass_type == 'trimester' && form.num_trimester),
        'btn-secondary':
          pass.id == undefined ||
          (pass_type == 'trimester' && !form.num_trimester),
        disabled: isRenewal
      }"
      :disabled="
        pass.id == undefined ||
          (pass_type == 'trimester' && !form.num_trimester) ||
          isRenewal
      "
    >
      <span v-if="pass_type == 'trimester'"
        >Choisissez l'activité avec la date de début et de fin des séances</span
      >
      <span v-else-if="pass_type == 'one_session'"
        >Choisissez l'activité avec la date de la séance</span
      >
      <span v-else
        >Choisissez les activités avec les dates et heures des séances</span
      >
    </button>
  </div>
  <table class="table min mt-1">
    <thead>
      <tr>
        <th>
          Activité
          <span v-if="form.sessions.length"
            >( {{ form.sessions.length }} )</span
          >
        </th>
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
              :disabled="!form.sessions.length"
            >
              <i class="fab fa-creative-commons-nc-eu"></i> Gratuit
            </button>
          </template>
        </th>
      </tr>
    </thead>
    <tbody>
      <template v-if="form.sessions.length > 0">
        <tr v-for="(session, indexSession) in form.sessions" :key="session.id">
          <td>
            {{ session.display.activity }}
          </td>
          <td>
            {{ session.display.day }}
          </td>
          <td>
            {{ session.display.date }}
          </td>
          <td>
            {{ session.display.planning }}
          </td>
          <td v-if="pass_type != 'other' && pass_type != 'decouvert'">
            <span v-if="!isNaN(session.price.price)"
              >{{ session.price.price }}€</span
            >
            <span v-else>( non paramètré )</span>
            <template v-if="auth_user.role_name != 'customer'">
              <button
                v-if="!session.price.price_discounted"
                type="button"
                @click.prevent="discountPrice(indexSession)"
                class="ml-2 float-right btn btn-sm border-0 btn-outline-danger"
                data-toggle="tooltip"
                title="Remise de prix"
              >
                <i class="fab fa-creative-commons-nc-eu"></i>
              </button>
              <button
                v-else
                type="button"
                @click.prevent="resetPrice(indexSession)"
                class="ml-2 float-right btn btn-sm border-0 btn-outline-success"
                data-toggle="tooltip"
                title="Réinitialiser le prix"
              >
                <i class="fa fa-undo"></i>
              </button>
            </template>
          </td>
          <td v-else>PASS</td>
        </tr>
      </template>
      <template v-else>
        <tr>
          <td colspan="4" class="text-center">Choisissez des séances</td>
        </tr>
      </template>
    </tbody>
  </table>

  <button
    type="button"
    @click.prevent="addToCart"
    class="btn btn-sm btn-success float-right"
    :disabled="form.sessions.length == 0"
  >
    Ajouter au panier
  </button>
</template>

<script>
export default {
  props: [
    "season",
    "sessionsInfo",
    "pass",
    "pass_type",
    "type_of_fees",
    "reduction",
    "id_"
  ],
  data() {
    return {
      form: {
        pass_id: null,
        season_id: null,
        id_: null,
        sessions: [],
        num_trimester: null
      },
      display: {},
      select: {},
      SessionsForRenewalObtained: false,
      empty_session_for_renewal: false
    };
  },
  watch: {
    pass: {
      deep: true,
      handler() {
        this.form.pass_id = null;
        this.form.season_id = null;
        this.display.pass = "_";

        if (this.SessionsForRenewalObtained || !this.$page.props.renewal_id) {
          this.form.sessions = [];
        }

        if (this.pass) {
          this.form.season_id = this.season.id;
          this.form.pass_id = this.pass.id;
          this.display.pass = this.pass.name;
          this.form.pass_type = this.pass_type;
          this.form.type_of_fees = this.type_of_fees
            ? this.type_of_fees
            : "normal";
          if (this.pass_type == "trimester") {
            this.getUnfinishedTrimesters();
          } else {
            this.num_trimester = null;
          }
        }
      }
    },
    sessionsInfo: {
      deep: true,
      handler() {
        if (this.id_ == this.sessionsInfo.id_) {
          this.form.sessions = this.sessionsInfo.sessions;
          setTimeout(() => {
            this.initTooltipe();
          }, 200);
        }
      }
    },
    type_of_fees: {
      deep: true,
      handler() {
        this.form.type_of_fees = this.type_of_fees;
      }
    },
    reduction: {
      deep: true,
      handler() {
        this.getSessionsForRenewal();
      }
    }
  },
  beforeMount() {
    this.form.id_ = this.id_;
    if (this.$page.props.num_trimester)
      this.form.num_trimester = this.$page.props.num_trimester;
    if (this.$page.props.activity_id)
      this.form.activity_id = this.$page.props.activity_id;

    this.getSessionsForRenewal();
  },
  methods: {
    getUnfinishedTrimesters() {
      if (!this.loadTrimester) {
        this.loadTrimester = true;
        axios
          .get(
            route("api.seasons.trimesters", {
              season: this.form.season_id,
              unfinished: true,
              group: true
            })
          )
          .then(response => {
            this.select.trimesters = this.toSelect(
              response.data,
              "num_trimester",
              "subscription_trimester"
            );
            this.loadTrimester = false;
          });
      }
    },
    showPlanningCalendar() {
      this.form.pass_id = this.pass.id;
      this.$emit("showSessionCalendar", this.form);
    },
    addToCart() {
      if (this.form.sessions.length > 0) {
        var dataPassSubscription = {
          data: cp(this.form),
          display: this.display
        };
        if (this.pass_type == "other" || this.pass_type == "decouvert") {
          dataPassSubscription.data.amount = this.pass.price.price;
        } else {
          dataPassSubscription.data.amount = 0;
          for (var session of dataPassSubscription.data.sessions) {
            dataPassSubscription.data.amount +=
              (session.price ? session.price.price : 0) * 1;
          }
        }

        this.form.num_trimester = null;
        // this.select.trimesters = [];
        this.$emit("addSessionsActivityToCart", dataPassSubscription);
      }
    },
    discountPriceAll() {
      for (let indexSession in this.form.sessions) {
        this.discountPrice(indexSession);
      }
    },
    discountPrice(index) {
      this.form.sessions[index].price.price_ = this.form.sessions[
        index
      ].price.price;
      this.form.sessions[index].price.price = 0;
      this.form.sessions[index].price.price_discounted = true;
    },
    resetPrice(index) {
      this.form.sessions[index].price.price = this.form.sessions[
        index
      ].price.price_;
      this.form.sessions[index].price.price_discounted = false;
    },
    getSessionsForRenewal() {
      if (!this.$page.props.renewal_id) return;

      var params = {
        establishment_id: this.$page.props.establishment_id,
        not_session_user_id: this.$page.props.user_id,
        minDate: this.dateAng(),
        pass_id: this.$page.props.pass_id,
        with_price: true,
        view: "day",
        pass_type: "trimester",
        season_id: this.$page.props.season_id,
        type_of_fees: this.reduction,
        reduction: this.reduction,
        num_trimester: this.$page.props.num_trimester,
        renewal_for_subscription_id: this.$page.props.subscription_id,
        renewal_id: this.$page.props.renewal_id
      };

      axios
        .get(route("api.plannings.sessions"), {
          params: params
        })
        .then(response => {
          this.form.sessions = cpp(response.data.data);
          this.empty_session_for_renewal = this.form.sessions.length == 0;
          setTimeout(() => {
            this.setSessionForRenewalObtained();
          }, 1000);
        });
    },
    setSessionForRenewalObtained(call_n = 0) {
      if (this.pass.id) {
        this.SessionsForRenewalObtained = true;
      } else if (call_n < 20) {
        setTimeout(() => {
          this.setSessionForRenewalObtained(call_n + 1);
        }, 1000 * (call_n + 1));
      }
    }
  },
  computed: {
    // typeTable() {
    //   return this.pass_type == null || this.pass_type == "one_session" ? 1 : 2;
    // },
    isRenewal() {
      return this.$page.props.renewal_id ? true : false;
    }
  }
};
</script>

<style lang="scss" scoped>
.btn-choice-session {
  font-size: 15px;
}
</style>
