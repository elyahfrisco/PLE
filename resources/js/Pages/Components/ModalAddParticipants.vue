<template>
  <jet-modal
    :id="modalId"
    maxWidth="xl"
    title="Ajouter des participants au groupe"
  >
    <div v-if="session.id" class="session-description row">
      <div class="col-md">
        <p>
          Activité : <span class="text-uppercase">{{ session.activity }}</span>
        </p>
        <p>
          Effectif : {{ session.participants_count }}/{{
            session.max_effective
          }}
        </p>
        <p>N surpassé max : {{ session.super_pass }}</p>
        <p>Reste : {{ session.real_max_effective }}</p>
        <!-- ( officieux {{ session.real_max_effective }} ) -->
      </div>
      <div class="col-md">
        <p>Saison : {{ season_planning.season_txt }}</p>
        <p>Trimestre : {{ form.num_trimester }}</p>
        <p>Date : {{ dateFr(session.date) }} ({{ session.elapseTime }})</p>
        <p>Horaire : {{ session.time_start }} à {{ session.time_end }}</p>
      </div>
    </div>

    <form>
      <div class="form-group pass-category-dispo mb-1">
        <div class="row mx-0">
          <div class="btn-group w-100" role="group" aria-label="Basic example">
            <label
              for="one_session"
              class="btn btn-outline-success"
              :class="{
                active: form.pass_type == 'one_session',
                disabled:
                  !passCategories.oneSession ||
                  (loadPasses && form.pass_type != 'one_session')
              }"
            >
              <input
                type="radio"
                value="one_session"
                id="one_session"
                :disabled="
                  !passCategories.oneSession ||
                    (loadPasses && form.pass_type != 'one_session')
                "
                class="d-none"
                v-model="form.pass_type"
              />
              <loadings :processing="loadPassesCategories" />
              Séance à l’unité
            </label>
            <label
              for="trimester"
              class="btn btn-outline-success"
              :class="{
                active: form.pass_type == 'trimester',
                disabled:
                  !passCategories.trimester ||
                  (loadPasses && form.pass_type != 'trimester')
              }"
            >
              <input
                type="radio"
                value="trimester"
                id="trimester"
                class="d-none"
                :disabled="
                  !passCategories.trimester ||
                    (loadPasses && form.pass_type != 'trimester')
                "
                v-model="form.pass_type"
              />
              <loadings :processing="loadPassesCategories" />
              Pass trimestriel</label
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
                active: form.pass_id == pass.id
              }"
            >
              <input
                type="radio"
                :value="pass.id"
                :id="'pass' + pass.value"
                class="d-none"
                v-model="form.pass_id"
              />
              {{ pass.label }}
            </label>
          </template>
        </div>
        <small class="validation-error" v-if="errors.pass_id">{{
          errors.pass_id
        }}</small>
      </div>

      <template v-if="form.pass_type == 'trimester'">
        <div class="form-group">
          <label class="mr-2">Date debut de l'activité : </label>
          <span>{{ dateFr(session.date) }} ({{ session.elapseTime }})</span>
        </div>
        <div class="form-group">
          <label class="mr-2">Date de fin : </label>
          <Multiselect
            v-model="form.date_end"
            placeholder="date de fin"
            :options="form.sessions"
            :required="true"
            :canDeselect="false"
            label="dateFr"
            valueProp="date"
          />
        </div>
      </template>

      <div class="form-group">
        <label class="mr-2">Prix de l'activité : </label>
        <loadings v-if="loadPrice" :processing="loadPrice" />
        <template v-else-if="form.sessions[0] && form.sessions[0].price">
          <span v-if="form.sessions[0].price.price != undefined">
            {{ form.sessions[0].price.price }} €
          </span>
          <span v-else> (non configuré)</span>
        </template>
        <span v-else>_</span>
      </div>
      <div class="form-group">
        <label>Type d'inscription</label>
        <div class="">
          <div class="icheck-success d-inline">
            <input
              type="radio"
              value="adult"
              id="adult"
              v-model="form.subscription_type"
            />
            <label for="adult">Adulte</label>
          </div>
          <div class="icheck-success d-inline ml-4">
            <input
              type="radio"
              value="child"
              id="child"
              v-model="form.subscription_type"
            />
            <label for="child">Enfant</label>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label class="">Clients ( max : {{ max_effectif }})</label>
        <Multiselect
          v-model="form.customers_id"
          placeholder="Selectionner les participants"
          :filterResults="false"
          :resolveOnLoad="false"
          :options="
            async function(query) {
              return await getUsers(query);
            }
          "
          :searchable="true"
          :minChars="3"
          :maxHeight="300"
          mode="tags"
          :loading="loadUsers"
          delay="0"
          trackBy="full_name"
          label="full_name"
          valueProp="id"
          ref="selectUser"
          @deselect="deselectUser"
          required
        >
          <template v-slot:option="{ option }">
            <profil-photo
              class="character-option-icon"
              :user="option"
              :width="25"
              :rounded="false"
            ></profil-photo>
            <span class="ml-2"
              >{{ option.full_name }} ({{ option.email }})</span
            >
          </template>
        </Multiselect>
        <small class="validation-error" v-if="errors.customers_id">{{
          errors.customers_id
        }}</small>
      </div>

      <table class="table table-bordered" v-if="$refs.selectUser">
        <thead>
          <tr>
            <th>Client</th>
            <th class="text-center">Frais d'inscription</th>
            <th class="text-center">Frais de gestion</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in $refs.selectUser.internalValue" :key="user.id">
            <td>{{ user.full_name }}</td>
            <td>
              <template
                v-if="
                  form.fees_check[user.id] &&
                    form.fees_check[user.id]
                      .not_having_paid_registration_fees == true
                "
              >
                <div class="">
                  <loadings :processing="loadsRegistrationFees[user.id]" />
                  <template v-if="!loadsRegistrationFees[user.id]">
                    <div class="icheck-success d-inline">
                      <input
                        type="radio"
                        value="normal"
                        :id="'registration-fees-normal' + user.id"
                        v-model="
                          form.fees_check[user.id].type_registration_fees
                        "
                      />
                      <label :for="'registration-fees-normal' + user.id"
                        >N
                      </label>
                    </div>
                    <div class="icheck-success d-inline ml-4">
                      <input
                        type="radio"
                        value="reduced"
                        :id="'registration-fees-reduced' + user.id"
                        v-model="
                          form.fees_check[user.id].type_registration_fees
                        "
                      />
                      <label :for="'registration-fees-reduced' + user.id"
                        >R
                      </label>
                    </div>
                    <div class="icheck-success d-inline ml-4">
                      <input
                        type="radio"
                        value="offered"
                        :id="'registration-fees-offered' + user.id"
                        v-model="
                          form.fees_check[user.id].type_registration_fees
                        "
                      />
                      <label :for="'registration-fees-offered' + user.id"
                        >O</label
                      >
                    </div>

                    <span class="float-right">
                      <template
                        v-if="
                          form.fees_check[user.id].type_registration_fees ==
                            'normal'
                        "
                        >{{
                          form.fees_check[user.id].season_registration_fees
                            .price
                        }}€</template
                      >
                      <template
                        v-else-if="
                          form.fees_check[user.id].type_registration_fees ==
                            'reduced'
                        "
                        >{{
                          form.fees_check[user.id].season_registration_fees
                            .reduced_price
                        }}€</template
                      >
                      <template v-else>{{ 0 }}€</template>
                    </span>
                  </template>
                </div>
              </template>
              <span v-else class="text-center">_</span>
            </td>
            <td class="text-center">
              <loadings
                :processing="loadsManagementFees[user.id]"
                class="pointer"
                title="Actualiser"
                @click="getSeasonManagementFees(user.id)"
              />
              <template v-if="!loadsManagementFees[user.id]">
                <template
                  v-if="
                    form.fees_check[user.id] &&
                      form.fees_check[user.id]
                        .not_having_paid_management_fees == true
                  "
                >
                  <div class="">
                    <span
                      v-if="
                        !isNaN(
                          form.fees_check[user.id].season_management_fees.price
                        )
                      "
                      >{{
                        form.fees_check[user.id].season_management_fees.price
                      }}€</span
                    ><span v-else>( non paramètré )</span>
                  </div>
                </template>
                <span v-else>_</span>
              </template>
            </td>
          </tr>
        </tbody>
      </table>

      <div class="row">
        <button
          class="btn btn-success mx-auto"
          @click.prevent="saveSubscriptions"
          :disabled="cant_submit"
          :class="{ disabled: cant_submit }"
        >
          Souscrire
        </button>
      </div>
    </form>
  </jet-modal>
</template>

<script>
import profilPhoto from "@/Pages/Components/profilPhoto.vue";

export default {
  props: {
    session: Object,
    modalId: {
      default: "add-participants",
      type: String
    }
  },
  components: {
    profilPhoto
  },
  data() {
    return {
      form: {
        activity_session_id: null,
        pass_id: null,
        num_trimester: null,
        subscription_type: "adult",
        type_of_fees: "normal",
        customers_id: [],
        fees_check: {},
        sessions: []
      },
      obj_fees_check: {
        type_registration_fees: "normal",
        type_management_fees: "normal",
        amount_registration_fees: 0,
        amount_management_fees: 0,
        season_registration_fees: {},
        season_management_fees: {},
        not_having_paid_registration_fees: null,
        not_having_paid_management_fees: null
      },
      pass_type: null,
      max_effectif: 0,
      loadSubmit: false,
      loadUsers: false,
      loadPassesCategories: false,
      loadPasses: false,
      loadsRegistrationFees: {},
      loadsManagementFees: {},
      loadPrice: false,
      season_fees: {},
      select: {
        type_of_regestration_fees: [
          {
            label: "Normal",
            value: "normal"
          },
          {
            label: "Reduit",
            value: "reduced"
          },
          {
            label: "Offert",
            value: "offered"
          }
        ]
      },
      season_planning: {},
      passCategories: {
        trimester: false,
        other: false,
        oneSession: false
      }
    };
  },
  beforeMount() {},
  watch: {
    session: {
      deep: true,
      handler() {
        this.$refs.selectUser.clear();
        this.select.customers = [];
        this.form.fees_check = {};
        this.season_management_fees = {};
        this.loadsRegistrationFees = {};
        this.season_fees = {};

        setTimeout(() => {
          this.form.activity_session_id = this.session.id;
          this.form.activity_id = this.session.activity_id;
          this.form.establishment_id = this.session.establishment_id;
          this.form.planning_id = this.session.planning_id;
          this.max_effectif = this.session.real_max_effective;

          this.form.date_start = this.session.date;
          this.form.date_end = this.$moment(this.session.date)
            .add("days", 1)
            .format("DD-MM-YYYY");
          this.getTrimesterNum();
        }, 100);

        this.getPassesCategories();
        this.getSeasonOfPlanning();
      }
    },

    "form.date_end": {
      deep: true,
      handler() {
        setTimeout(() => {
          this.refreshMaxEffectif();
        }, 200);
      }
    },
    "form.pass_type": {
      deep: true,
      handler: _.debounce(function() {
        if (this.form.pass_type == "one_session") this.form.date_end = null;
        this.getPasses();
      }, 500)
    },

    "form.customers_id": {
      deep: true,
      handler() {
        this.verifyFees();
      }
    },

    "form.subscription_type": {
      deep: true,
      handler() {
        this.season_fees = {};
        setTimeout(() => {
          this.verifyFees(true);
          this.getSessions();
        }, 200);
      }
    },

    "form.pass_id": {
      deep: true,
      handler: _.debounce(function() {
        this.getSessions();
      }, 200)
    }
  },
  methods: {
    refreshMaxEffectif() {
      if (this.form.date_end) {
        if (this.form.sessions[0]) {
          this.max_effectif = this.form.sessions[0].real_max_effective;
        }

        for (let session of this.form.sessions) {
          if (session.date > this.form.date_end) {
            break;
          }
          if (this.max_effectif > session.real_max_effective) {
            this.max_effectif = session.real_max_effective;
          }
        }
      }
    },
    getSessions() {
      if (this.form.pass_id) {
        this.loadPrice = true;
        var params = {
          establishment_id: this.form.establishment_id,
          not_session_user_id: this.form.customers_id,
          minDate: this.dateAng(this.form.date_start),
          // maxDate: this.dateAng(this.form.date_end),
          pass_id: this.form.pass_id,
          min_id: this.form.activity_session_id,
          with_price: true,
          view: "day",
          pass_type: this.form.pass_type,
          season_id: this.form.season_id,
          type_of_fees: this.form.type_of_fees,
          only_planning_id: this.form.planning_id
        };

        if (this.form.pass_type == "one_session") {
          params.max_id = this.form.activity_session_id;
        } else if (this.form.pass_type == "trimester") {
          params.num_trimester = this.form.num_trimester;
        }

        axios
          .get(route("api.plannings.sessions"), {
            params: params
          })
          .then(response => {
            if (response.data.data != undefined) {
              this.form.sessions = cpp(response.data.data);
              setTimeout(() => {
                if (this.form.pass_type == "trimester") {
                  this.form.date_end = this.form.sessions[
                    this.form.sessions.length - 1
                  ].date;
                }
              }, 100);
            } else {
              this.form.sessions = [];
            }
            this.loadPrice = false;
          });
      }
    },

    saveSubscriptions() {
      this.loadSubmit = true;
      var form = { ...this.form };

      form.customers_id.forEach(user__id => {
        var user_info = form.fees_check[user__id];

        if (user_info.not_having_paid_registration_fees) {
          switch (user_info.type_registration_fees) {
            case "offered":
              user_info.amount_registration_fees = 0;
              break;
            case "reduced":
              user_info.amount_registration_fees = user_info
                .season_registration_fees.reduced_price
                ? user_info.season_registration_fees.reduced_price
                : 0;
              break;
            default:
              user_info.amount_registration_fees = user_info
                .season_registration_fees.price
                ? user_info.season_registration_fees.price
                : 0;
              break;
          }
        }

        if (user_info.not_having_paid_management_fees) {
          user_info.amount_management_fees = user_info.season_management_fees
            .price
            ? user_info.season_management_fees.price
            : 0;
        }

        form.fees_check[user__id] = user_info;
      });

      if (form.pass_type == "trimester") {
        var sessions = [];
        for (let session of form.sessions) {
          if (session.date <= form.date_end) {
            sessions.push(session);
          } else {
            break;
          }
        }
        form.sessions = sessions;
      } else if (form.pass_type == "one_session") {
        form.sessions = [form.sessions[0]];
      }

      this.$inertia.post(route("subscriptions.store.multiple"), form, {
        preserveState: true,
        onBefore: () => {
          if (this.form.fees_check.length != 0) {
            return confirm("Confirmer les souscriptions");
          }
          return true;
        },
        onSuccess: () => {
          $("#add-participants").modal("hide");
          this.form.fees_check = {};
          this.form.customers_id = [];
          this.form.loadsManagementFees = {};
          this.form.loadsRegistrationFees = {};
          this.form.season_fees = {};
          this.select.customers = [];
          this.select.passes = [];

          this.$emit("participantsAdded");
          // this.iReload();
        },
        onFinish: () => {
          this.loadSubmit = false;
        }
      });
    },
    verifyFees(refresh = false) {
      setTimeout(() => {
        if (this.form.customers_id.length > 0) {
          for (let user_id of this.form.customers_id) {
            this.verifyRegistrationFees(user_id, refresh);
          }
        }
      }, 200);
    },
    deselectUser(user_id) {
      delete this.form.fees_check[user_id];
    },
    getUsers(q) {
      let attr = ["name", "first_name"];

      if (!q) {
        return this.select.customers;
      }

      this.loadUsers = true;

      return axios
        .get(route("api.user.search"), {
          params: {
            q: q,
            attr: attr,
            not_participant_in_session: this.session.id
          }
        })
        .then(response => {
          this.select.customers = response.data.data;
          this.loadUsers = false;
          return this.select.customers;
        });
    },

    getTrimesterNum() {
      setTimeout(() => {
        axios
          .get(route("api.plannings.session.trimester"), {
            params: {
              activity_session_id: this.session.id
            }
          })
          .then(response => {
            this.form.num_trimester = response.data;
          });
      }, 100);
    },

    getSeasonOfPlanning() {
      if (this.session.planning_id) {
        setTimeout(() => {
          axios
            .get(
              route("api.plannings.season", {
                planning_id: this.session.planning_id
              })
            )
            .then(response => {
              this.season_planning = response.data;
              this.form.season_id = response.data.id;

              this.initSeasonRegistrationFees();
            });
        }, 100);
      }
    },

    getPassesCategories() {
      setTimeout(() => {
        this.form.pass_id = null;
        this.loadPassesCategories = true;
        axios
          .get(
            route("api.plannings.passes.categories", {
              planning_id: this.session.planning_id
            })
          )
          .then(response => {
            this.passCategories = response.data;
            this.loadPassesCategories = false;

            if (
              this.form.pass_type == null &&
              this.passCategories.oneSession == true
            ) {
              this.form.pass_type = "one_session";
            }
          });
      }, 100);
    },

    getPasses() {
      setTimeout(() => {
        if (this.form.pass_type == null) {
          return null;
        }

        this.form.pass_id = null;
        this.passActive = {};
        this.select.passes = {};
        this.loadPasses = true;

        axios
          .get(route("api.plannings.passes"), {
            params: {
              pass_type: this.form.pass_type,
              planning_id: this.session.planning_id
            }
          })
          .then(response => {
            this.select.passes = this.toSelect(response.data);
            if (
              this.form.pass_type == "trimester" &&
              this.select.passes.length == 1
            ) {
              setTimeout(() => {
                $("#f_pass" + this.select.passes[0].id).click();
              }, 100);
            }
            this.loadPasses = false;
          });
      }, 100);
    },
    verifyRegistrationFees(user_id, refresh = false) {
      if (user_id && (this.form.fees_check[user_id] == undefined || refresh)) {
        if (!refresh || this.form.fees_check[user_id] == undefined) {
          this.form.fees_check[user_id] = { ...this.obj_fees_check };
        }
        this.loadsRegistrationFees[user_id] = true;
        setTimeout(() => {
          axios
            .get(
              route("api.user.fees", {
                user_id: user_id,
                type: "registration",
                check: true
              })
            )
            .then(response => {
              this.form.fees_check[
                user_id
              ].not_having_paid_registration_fees = response.data
                ? false
                : true;
              if (
                this.form.fees_check[user_id].not_having_paid_registration_fees
              ) {
                this.getSeasonRegistrationFees(user_id);
              } else {
                this.loadsRegistrationFees[user_id] = false;
                this.verifyManagementFees(user_id);
              }
            });
        }, 100);
      }
    },
    verifyManagementFees(user_id) {
      if (user_id) {
        this.loadsManagementFees[user_id] = true;
        setTimeout(() => {
          axios
            .get(
              route("api.user.fees", {
                season_id: this.season_planning.id,
                user_id: user_id,
                type: "management",
                check: true
              })
            )
            .then(response => {
              this.form.fees_check[
                user_id
              ].not_having_paid_management_fees = response.data ? false : true;

              if (
                this.form.fees_check[user_id].not_having_paid_management_fees
              ) {
                this.getSeasonManagementFees(user_id);
              } else {
                this.loadsManagementFees[user_id] = false;
              }
            });
        }, 100);
      }
    },
    getSeasonRegistrationFees(user_id) {
      setTimeout(() => {
        if (this.form.fees_check[user_id].not_having_paid_registration_fees) {
          if (this.season_fees.registration) {
            this.loadsRegistrationFees[user_id] = false;
            this.form.fees_check[
              user_id
            ].season_registration_fees = this.season_fees.registration;
          } else {
            axios
              .get(
                route("api.seasons.fees", {
                  season: this.season_planning.id,
                  category: this.form.subscription_type,
                  type: "registration",
                  first: true
                })
              )
              .then(response => {
                if (response.data) {
                  this.form.fees_check[user_id].season_registration_fees =
                    response.data;
                } else {
                  this.form.fees_check[user_id].season_registration_fees = {};
                }
                this.season_fees.registration = this.form.fees_check[
                  user_id
                ].season_registration_fees;
                this.loadsRegistrationFees[user_id] = false;
              });
          }
        } else {
          this.form.fees_check[user_id].season_registration_fees = {};
        }
      }, 100);
    },
    initSeasonRegistrationFees() {
      setTimeout(() => {
        if (!this.season_fees.registration) {
          axios
            .get(
              route("api.seasons.fees", {
                season: this.season_planning.id,
                category: this.form.subscription_type,
                type: "registration",
                first: true
              })
            )
            .then(response => {
              if (response.data) {
                this.season_fees.registration = response.data;
              } else {
                this.season_fees.registration = {};
              }
              this.initSeasonManagementFees();
            });
        }
      }, 100);
    },
    getSeasonManagementFees(user_id) {
      setTimeout(() => {
        if (this.form.fees_check[user_id].not_having_paid_management_fees) {
          if (this.season_fees.management) {
            this.loadsManagementFees[user_id] = false;
            this.form.fees_check[
              user_id
            ].season_management_fees = this.season_fees.management;
          } else {
            axios
              .get(
                route("api.seasons.fees", {
                  season: this.season_planning.id,
                  category: this.form.subscription_type,
                  type: "management",
                  first: true
                })
              )
              .then(response => {
                if (response.data) {
                  this.form.fees_check[user_id].season_management_fees =
                    response.data;
                } else {
                  this.form.fees_check[user_id].season_management_fees = {};
                }
                this.season_fees.management = this.form.fees_check[
                  user_id
                ].season_management_fees;
                this.loadsManagementFees[user_id] = false;
              });
          }
        } else {
          this.form.fees_check[user_id].season_management_fees = {};
        }
      }, 100);
    },
    initSeasonManagementFees() {
      setTimeout(() => {
        if (!this.season_fees.management) {
          axios
            .get(
              route("api.seasons.fees", {
                season: this.season_planning.id,
                category: this.form.subscription_type,
                type: "management",
                first: true
              })
            )
            .then(response => {
              if (response.data) {
                this.season_fees.management = response.data;
              } else {
                this.season_fees.management = {};
              }
              this.verifyFees();
            });
        }
      }, 100);
    },
    price_user_registration_fees(user_id) {
      var price = 0;
      setTimeout(() => {
        if (
          this.form.fees_check[user_id].season_registration_fees &&
          this.form.fees_check[user_id].season_registration_fees.id
        ) {
          switch (this.form.fees_check[user_id].type_registration_fees) {
            case "offered":
              price = 0;
              break;
            case "reduced":
              price = this.form.fees_check[user_id].season_registration_fees
                .reduced_price;
              break;
            default:
              price = this.form.fees_check[user_id].season_registration_fees
                .price;
              break;
          }
        }
        return !isNaN(price) ? price : 0;
      }, 100);
    }
  },
  computed: {
    cant_submit() {
      var ret = false;
      if (
        this.loadSubmit ||
        (this.pass_type == "trimester" && !this.form.date_end) ||
        !this.form.customers_id.length
      ) {
        return true;
      }

      if (this.loadsManagementFees) {
        Object.keys(this.loadsManagementFees).forEach(key => {
          if (!this.$refs.selectUser.textValue.includes(key)) {
            delete this.loadsManagementFees[key];
          } else if (this.loadsManagementFees[key]) {
            ret = true;
          }
        });
      }

      if (this.loadsRegistrationFees) {
        Object.keys(this.loadsRegistrationFees).forEach(key => {
          if (!this.$refs.selectUser.textValue.includes(key)) {
            delete this.loadsRegistrationFees[key];
          } else if (this.loadsRegistrationFees[key]) {
            ret = true;
          }
        });
      }

      return ret;
    },
    pass_list_title() {
      switch (this.form.pass_type) {
        case "trimester":
          return ": Pass trimestriel";
          break;
        case "one_session":
          return ": Séance à l’unité";
          break;
      }
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

.multiselect.is-searchable .multiselect-input,
.multiselect .multiselect-input {
  height: auto !important;
}
</style>
