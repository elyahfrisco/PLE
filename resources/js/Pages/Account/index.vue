<template>
  <app-layout>
    <!-- <template #pageTitle
      >Info {{ user.first_name }} {{ user.name.toUpperCase() }}</template
    > -->
    <div class="row">
      <div class="col-xl-10">
        <div class="mb-4">
          <div class="mb-4 content_">
            <div class="row">
              <div class="col-lg-2 col-md-4 text-center">
                <div class="">
                  <div class="photo">
                    <profil-photo :user="user" :rounded="false" />
                  </div>
                  <div class="block px-2 mt-1 actions-profil">
                    <button
                      v-if="
                        (user.role == 'prospect' ||
                          user.role == 'attente' ||
                          user.status == 'old_customer') &&
                        auth_user.role_name == 'admin'
                      "
                      @click="activate_account"
                      class="btn btn-outline-primary btn-sm w-100"
                      data-toggle="tooltip"
                      title="Activer le compte"
                    >
                      Activer
                    </button>
                    <button
                      v-else-if="
                        user.role == 'customer' &&
                        auth_user.role_name == 'admin'
                      "
                      @click="disable_account"
                      class="btn btn-outline-danger btn-sm w-100"
                      data-toggle="tooltip"
                      title="Désactiver le compte"
                    >
                      Désactiver le client
                    </button>

                    <inertia-link
                      v-if="
                        (user.role == 'prospect' ||
                          user.role == 'attente' ||
                          user.role == 'customer') &&
                        auth_user.role_name == 'admin'
                      "
                      :href="route('customers.create')"
                      :data="{
                        duplicat_user_id: user.id,
                        account_type: 'prospect',
                      }"
                      class="btn btn-outline-secondary btn-sm w-100"
                      data-toggle="tooltip"
                      title="Dupliquer le fiche client"
                    >
                      Dupliquer le fiche client
                    </inertia-link>

                    <inertia-link
                      v-if="auth_user.role_name != 'coach'"
                      class="btn btn-outline-secondary btn-sm w-100"
                      :href="route('account.edit', { user_id: user.id })"
                    >
                      <i class="fa fa-edit float-left"></i> Modifier Profil
                    </inertia-link>
                    <inertia-link
                      v-if="auth_user.role_name != 'coach'"
                      class="btn btn-outline-secondary btn-sm w-100"
                      :href="route('account.photo.edit', user.id)"
                    >
                      <i class="fa fa-edit float-left"></i> Modifier Photo
                    </inertia-link>
                    <inertia-link
                      class="btn btn-outline-secondary btn-sm w-100"
                      v-if="
                        user.role == 'customer' ||
                        user.role == 'prospect' ||
                        user.role == 'attente'
                      "
                      :href="
                        route('account.medical_certificate.edit', {
                          user_id: user.id,
                        })
                      "
                    >
                      <i class="fa fa-edit float-left"></i> Modifier Certificat
                      Medicale
                    </inertia-link>
                    <button
                      @click="delete_customer"
                      class="btn btn-outline-danger btn-sm w-100 mb-4"
                      v-if="
                        user.role == 'customer' ||
                        (auth_user.role_name == 'admin' &&
                          auth_user.id != user.id)
                      "
                    >
                      Supprimer
                    </button>
                  </div>
                </div>
              </div>
              <div class="col-lg-10 col-md-8">
                <div class="row">
                  <div class="col-md-6 font-weight-normal">
                    <div class="block">
                      <label class="mr-2">Date de création :</label>
                      <span class="text-capitalize">{{
                        dateFr(user.created_at)
                      }}</span>
                    </div>
                    <div class="block space-after">
                      <label class="mr-2">Numéro d’ordre :</label>
                      <span class="text-capitalize">{{ user.id }}</span>
                    </div>
                    <div class="block">
                      <label class="mr-2">Prènoms :</label>
                      <span class="text-capitalize">{{ user.first_name }}</span>
                    </div>
                    <div class="block">
                      <label class="mr-2">Nom :</label>
                      <span class="text-uppercase">{{ user.name }}</span>
                    </div>
                    <div class="block">
                      <label class="mr-2">Genre :</label>
                      <span v-if="user.is_child">{{
                        user.gender == "female" ? "Fille" : "Garçon"
                      }}</span>
                      <span v-else>{{
                        user.gender == "female" ? "Femme" : "Homme"
                      }}</span>
                    </div>
                    <div class="block space-after">
                      <label class="mr-2">Date de naissance :</label>
                      <span>{{ dateFr(user.birth_date) }}</span>
                    </div>
                    <div class="block">
                      <label class="mr-2">Habite à :</label>
                      <span>{{ user.address }}</span>
                    </div>
                    <div class="block">
                      <label class="mr-2">Ville :</label>
                      <span>{{ user.city }}</span>
                    </div>
                    <div class="block">
                      <label class="mr-2">CP :</label>
                      <span>{{ user.postal_code }}</span>
                    </div>
                    <div class="block">
                      <template v-if="user.is_child">
                        <label class="mr-2"
                          >E-mail pour accéder au compte :</label
                        >
                        <a href="#">{{ user.email }}</a>
                      </template>
                      <template v-else>
                        <label class="mr-2">Email :</label>
                        <a :href="'mailto:' + user.email">{{ user.email }}</a>
                      </template>
                    </div>
                    <div class="block" v-if="user.is_child">
                      <label class="mr-2">Email parent :</label>
                      <a :href="'mailto:' + user.mail1">{{ user.mail1 }}</a
                      >,
                      <a class="ml-1" :href="'mailto:' + user.mail2">{{
                        user.mail2
                      }}</a>
                    </div>
                    <div class="d-flex block space-after">
                      <label class="mr-2">Téléphones :</label>
                      <div class="mx-0">
                        <span
                          class="d-block"
                          v-for="phone in user.phones"
                          :key="phone.id"
                          ><span class="text-capitalize">{{ phone.type }}</span>
                          <a :href="'tel:' + phone.phone">{{
                            phone.phone.split(/(\d{2})/).join(" ")
                          }}</a></span
                        >
                      </div>
                    </div>
                    <div
                      class="d-block"
                      v-if="
                        user.role == 'prospect' ||
                        user.role == 'attente' ||
                        user.role == 'customer'
                      "
                    >
                      <label class="mr-2">Fiche d’inscription signée :</label>
                      <span>Non</span>
                    </div>
                    <div
                      class="d-block"
                      v-if="
                        user.role == 'prospect' ||
                        user.role == 'attente' ||
                        user.role == 'customer'
                      "
                    >
                      <label class="mr-2">Certificat médical remis :</label>
                      <span v-if="user.medical_certificate_path"
                        >Oui
                        <a
                          :href="
                            route().t.url +
                            $inertia.page.props.appPathMedicalCertificate +
                            user.medical_certificate_path
                          "
                          target="_blank"
                          class="text-info"
                          data-toggle="tooltip"
                          title="Consulter le certificat médical"
                        >
                          <i class="fa fa-eye mr-1"></i> </a
                      ></span>
                      <span v-else>Non</span>
                    </div>

                    <div
                      class="d-block"
                      v-if="
                        user.role == 'prospect' ||
                        user.role == 'attente' ||
                        user.role == 'customer'
                      "
                    >
                      <label class="mr-2">+ Information :</label>
                      <span>{{ user.additional_information }}</span>
                    </div>
                  </div>
                  <div class="col-md-6">
                    <div
                      class="d-block"
                      v-if="
                        user.role == 'prospect' ||
                        user.role == 'attente' ||
                        user.role == 'customer'
                      "
                    >
                      <label class="mr-2">Origine du contact :</label>
                      <span
                        >{{ user.contact_origin }},
                        {{ user.precision_contact_origin }}</span
                      >
                    </div>
                    <div
                      class="w-100 space-after"
                      v-if="
                        user.role == 'prospect' ||
                        user.role == 'attente' ||
                        user.role == 'customer'
                      "
                    >
                      <label>Activités souhaitées</label>
                      <ul v-if="user.wishes.length" class="pl-2">
                        <li
                          class="
                            activity-wishe
                            d-block
                            mb-0
                            ml-1
                            pl-1
                            border-left
                          "
                          v-for="wishe in user.wishes"
                          :key="wishe.id"
                        >
                          <span class="text-uppercase d-block"
                            >{{ wishe.activity.name }}
                            {{
                              wishe.establishment
                                ? wishe.establishment.sigle
                                : ""
                            }}</span
                          >
                          <span class="d-block">
                            <span class="font-weight-bold text-capitalize">{{
                              wishe.dayFr
                            }}</span>
                            <span v-if="wishe.time_start || wishe.time_end">
                              :
                              {{ wishe.time_start ? H(wishe.time_start) : "" }}
                              à
                              {{ wishe.time_end ? H(wishe.time_end) : "" }}
                            </span>
                          </span>
                          <span
                            class="text-uppercase d-block"
                            v-if="wishe.date_start"
                            >A partir de : {{ dateFr(wishe.date_start) }}</span
                          >
                          <div class="d-block">
                            <inertia-link
                              v-if="wishe.establishment_id"
                              :href="
                                route(
                                  'establishments.plannings.sessions.index',
                                  {
                                    establishment: wishe.establishment_id,
                                    activity_id: wishe.activity_id,
                                  }
                                )
                              "
                              class="btn btn-info btn-sm mr-1"
                              title="Afficher le planning de l'activité"
                              data-toggle="tooltip"
                              ><i class="fa fa-calendar-day"></i
                            ></inertia-link>

                            <inertia-link
                              v-if="user.role == 'customer'"
                              :href="
                                route('subscriptions.create', {
                                  user_id: user.id,
                                  establishment_id: wishe.establishment_id,
                                  activity_id: wishe.activity_id,
                                })
                              "
                              class="btn btn-success btn-sm mr-1"
                              title="Souscrire à cette activité"
                              data-toggle="tooltip"
                              ><i class="fa fa-clipboard-check"></i
                            ></inertia-link>
                          </div>
                        </li>
                      </ul>
                      <div v-else class="pl-2">
                        ( aucune activité souhaitée )
                      </div>
                    </div>
                    <div class="w-100" v-if="user.role == 'customer'">
                      <div class="block space-after">
                        <label class="font-weight-bold">Profil contact</label>
                        <div class="pl-2">
                          <div class="">
                            <span class="font-weight-bold">Centre(s)</span>
                            <div
                              class="profil_contact establishment_user block"
                            >
                              <template v-if="user_establishments.length > 0">
                                <p
                                  class="establishment mb-0 ml-1"
                                  v-for="establishment in user_establishments"
                                  :key="establishment.id"
                                >
                                  -
                                  <span class="text-uppercase">{{
                                    establishment.name
                                  }}</span>
                                  (
                                  {{
                                    $moment(establishment.time_end_max).isAfter(
                                      $moment()
                                    )
                                      ? "client en cours"
                                      : "ancien client"
                                  }}
                                  )
                                </p>
                              </template>
                              <span v-else class="badge badge-info"
                                >Aucune element à afficher</span
                              >
                            </div>
                          </div>
                          <div class="">
                            <span class="font-weight-bold">Activité(s)</span>
                            <div class="profil_contact activities_user block">
                              <template v-if="user_activities.length > 0">
                                <p
                                  class="activity mb-0 ml-1"
                                  v-for="activity in user_activities"
                                  :key="activity.id"
                                >
                                  -
                                  <span class="text-uppercase">{{
                                    activity.name
                                  }}</span>
                                  <a
                                    target="_blank"
                                    :href="
                                      route(
                                        'establishments.plannings.sessions.participants',
                                        {
                                          establishment:
                                            activity.establishment_id,
                                          activity_session:
                                            activity.activity_session_id,
                                        }
                                      )
                                    "
                                    data-toggle="tooltip"
                                    title="Liste des participants"
                                  >
                                    ({{ activity.group_name }})
                                  </a>
                                </p>
                              </template>
                              <span v-else class="badge badge-info"
                                >Aucune element à afficher</span
                              >
                            </div>
                          </div>
                          <div class="">
                            <span class="font-weight-bold"
                              >Type de souscription</span
                            >
                            <div class="profil_contact pass_user block">
                              <template v-if="user_activities.length > 0">
                                <p
                                  class="establishment mb-0 ml-1"
                                  v-for="pass in user_pass"
                                  :key="pass.id"
                                >
                                  -
                                  <span class="text-uppercase">{{
                                    pass.name
                                  }}</span>
                                  {{ pass.establishment.sigle }}
                                </p>
                              </template>
                              <span v-else class="badge badge-info"
                                >Aucune element à afficher</span
                              >
                            </div>
                          </div>
                        </div>
                      </div>
                      <div class="block">
                        <label class="mr-2">Date de la 1ère prestation :</label>
                        <span v-if="user.first_session">{{
                          dateHFr(user.first_session.time_start)
                        }}</span>
                        <span v-else
                          >Pas encore inscrit à une séance d'activité</span
                        >
                      </div>
                      <div class="block">
                        <label class="mr-2">Frais d'inscription :</label>
                        <span v-if="user_fees_paid" class="badge badge-success"
                          >régle</span
                        >
                        <span class="badge badge-danger" v-else>non réglé</span>
                      </div>
                      <div class="block">
                        <label class="mr-2"
                          >Nombre de scéance à récuperer :</label
                        >
                        <span class="badge badge-success">{{ nbrRecup }}</span>
                      </div>
                      <div class="block" v-if="predictable_payment_date">
                        <label class="mr-2"
                          >Date prévisible de règlement :</label
                        >
                        <span>{{ dateFr(predictable_payment_date) }}</span>
                      </div>
                    </div>
                    <div
                      class="w-100"
                      v-else-if="
                        user.role == 'prospect' || user.role == 'attente'
                      "
                    >
                      <label>Profil contact</label>
                      <div class="pl-2">
                        <p>
                          {{
                            user.role == "prospect"
                              ? "Prospect"
                              : "Liste d'attente"
                          }}
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div v-if="user.role == 'customer'" class="col-xl-2 mt-3 mt-xl-0">
        <subscription-list :user_id="user.id" />
      </div>
      <div
        class="col-12"
        v-if="
          (user.role == 'customer' ||
            user.role == 'attente' ||
            user.role == 'prospect') &&
          auth_user.role_name == 'admin'
        "
      >
        <div class="comments-list">
          <h2 class="h4">Commentaires</h2>
          <comment-form :user_id="user.id"></comment-form>
          <div class="col-md-10 offset-md-1">
            <template v-if="comments.length != 0">
              <comment
                v-for="comment in comments"
                :comment="comment"
                v-bind:key="comment.id"
              ></comment>
            </template>
            <template v-else>
              <p>Aucun commentaire</p>
            </template>
          </div>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import profilPhoto from "@/Pages/Components/profilPhoto.vue";
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";
import comment from "./comment.vue";
import commentForm from "./commentForm.vue";
import SubscriptionList from "./SubscriptionList.vue";

export default {
  components: {
    profilPhoto,
    commentForm,
    comment,
    BDropdown,
    JetDropdownLink,
    SubscriptionList,
  },
  props: ["user"],
  data() {
    return {
      comments: this.user.comments,
      user_establishments: [],
      user_pass: [],
      user_activities: [],
      user_fees_paid: false,
      predictable_payment_date: null,
      nbrRecup: 0,
    };
  },
  mounted() {
    if (this.user.role == "customer" || this.user.role == "coach") {
      this.get_user_establishments();
      this.get_user_pass();
      this.get_user_activities();
      this.chek_user_fees();
      this.countRecuperation(this.user.id);
    }
  },
  methods: {
    countRecuperation(user_id = null) {
      axios
        .get(
          route("api.subscription.count_recuperation", {
            user_id: user_id,
            and_with: [
              "activities.absence_prevention",
              "activities.absence_prevention.queue",
              "activities.absence_prevention.queue.recuperation_request",
            ],
            _query: {
              per_page: 100,
              page: 1,
            },
          })
        )
        .then((response) => {
          if (response.data) {
            this.nbrRecup = response.data;
          }
        });
    },
    get_user_activities() {
      axios
        .get(route("api.user.activities"), {
          params: {
            user_id: this.user.id,
          },
        })
        .then((response) => {
          this.user_activities =
            response.data != undefined ? response.data : [];
        });
    },
    get_user_establishments() {
      axios
        .get(route("api.user.establishments"), {
          params: {
            user_id: this.user.id,
          },
        })
        .then((response) => {
          this.user_establishments =
            response.data != undefined ? response.data : [];
        });
    },
    get_user_pass() {
      axios
        .get(route("api.user.passes"), {
          params: {
            user_id: this.user.id,
          },
        })
        .then((response) => {
          this.user_pass = response.data != undefined ? response.data : [];
        });
    },
    delete_customer() {
      this.$inertia.delete(
        route("account.destroy", { user_id: this.user.id }),
        {
          onBefore: this.msgConfirm("Confirmer la suppression"),
        }
      );
    },
    activate_account() {
      axios
        .put(route("customers.status.change", this.user.id), {
          activated: true,
          wishes: this.user.wishes,
        })
        .then((response) => {
          toastr.success(response.data[1]);
          this.user.activated = true;
          this.iReloadPartial("user");
        });
    },
    disable_account() {
      axios
        .put(route("customers.status.change", this.user.id), {
          activated: false,
        })
        .then((response) => {
          toastr.success(response.data[1]);
          this.user.activated = false;
          this.user.status = "old_customer";
          this.iReloadPartial("user");
        });
    },
    chek_user_fees() {
      axios
        .get(
          route("api.user.fees", {
            user_id: this.user.id,
            type: "registration",
            check_paid: true,
            check: true,
          })
        )
        .then((response) => {
          this.user_fees_paid = response.data;
          if (!response.data) {
            this.get_predictable_payment_date();
          }
        });
    },
    get_predictable_payment_date() {
      axios
        .get(
          route("api.user.fees", {
            user_id: this.user.id,
            type: "registration",
            with_bill: true,
          })
        )
        .then((response) => {
          if (
            response.data[0] &&
            response.data[0].bill &&
            response.data[0].bill.payment == null
          ) {
            this.predictable_payment_date =
              response.data[0].bill.predictable_payment_date;
          }
        });
    },
  },
};
</script>

<style lang="scss" scoped>
.content_ label {
  color: #6d6d6d;
  font-weight: 600;
}

.actions-profil {
  & > * {
    margin-top: 5px;
  }
}

.photo {
  position: relative;

  img {
    max-width: 100%;
  }
}

.profil_contact {
  .badge {
    color: white;
  }
}

/* .btn-edit-profil {
  position: absolute;
  bottom: 0;
} */

.space-after {
  margin-bottom: 25px;
}
</style>
