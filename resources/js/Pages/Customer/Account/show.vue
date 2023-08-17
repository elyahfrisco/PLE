<template>
  <app-layout>
    <template #pageTitle
      >Info {{ customer.first_name }}
      {{ customer.name.toUpperCase() }}</template
    >
    <div class="mb-4">
      <div class="mb-4">
        <div class="row">
          <div class="col-md-3 text-center">
            <div class="float-left row">
              <div class="photo">
                <profil-photo :user="customer" :rounded="false"></profil-photo>
              </div>
              <div class="block px-2 mt-1">
                <button
                  v-if="!customer.activated"
                  @click="activate_account"
                  class="btn btn-outline-danger mb-1 btn-sm"
                >
                  Activer le compte
                </button>
                <button
                  v-else
                  @click="disable_account"
                  class="btn btn-outline-danger mb-1 btn-sm"
                >
                  Désactiver le compte
                </button>

                <b-dropdown
                  btn="outline-secondary w-100"
                  sm="true"
                  dropright="true"
                >
                  <template #title> Action </template>

                  <template #content>
                    <jet-dropdown-link
                      :href="route('customers.photo.edit', customer.id)"
                    >
                      Editer photo
                    </jet-dropdown-link>
                    <jet-dropdown-link
                      :href="route('customers.edit', customer.id)"
                    >
                      Editer profil
                    </jet-dropdown-link>

                    <jet-dropdown-link
                      :href="
                        route('customers.medical_certificate.edit', customer.id)
                      "
                    >
                      Editer certificat medicale
                    </jet-dropdown-link>

                    <button @click="delete_customer" class="dropdown-item px-4">
                      Supprimer
                    </button>
                  </template>
                </b-dropdown>
              </div>
            </div>
          </div>
          <div class="col-md-9">
            <div class="row">
              <div class="col-md-6 font-weight-normal">
                <div class="block">
                  <span class="mr-2">Prènoms :</span>
                  <span class="text-capitalize">{{ customer.first_name }}</span>
                </div>
                <div class="block">
                  <span class="mr-2">Nom :</span>
                  <span class="text-uppercase">{{ customer.name }}</span>
                </div>
                <div class="block">
                  <span class="mr-2">Sexe :</span>
                  <span>{{
                    customer.gender == "female" ? "Femme" : "Homme"
                  }}</span>
                </div>
                <div class="block">
                  <span class="mr-2">Adresse :</span>
                  <span>{{ customer.adress }}</span>
                </div>
                <div class="block">
                  <span class="mr-2">Ville :</span>
                  <span>{{ customer.city }}</span>
                </div>
                <div class="block">
                  <span class="mr-2">CP :</span>
                  <span>{{ customer.postal_code }}</span>
                </div>
                <div class="block">
                  <span class="mr-2">Date de naissance :</span>
                  <span>{{ customer.birth_date }}</span>
                </div>
                <div class="block">
                  <span class="mr-2">Email :</span>
                  <a :href="'mailto:' + customer.email">{{ customer.email }}</a>
                </div>
                <div class="d-flex block">
                  <span class="mr-2">Téléphones :</span>
                  <div class="mx-0">
                    <span
                      class="d-block"
                      v-for="phone in customer.phones"
                      :key="phone.id"
                      ><a :href="'tel:' + phone.phone">{{
                        phone.phone
                      }}</a></span
                    >
                  </div>
                </div>
                <div class="d-block">
                  <span class="mr-2">Contact d'origine :</span>
                  <span>{{ customer.contact_profile }}</span>
                </div>
                <div class="d-block">
                  <span class="mr-2">Certificat médical remis :</span>
                  <span v-if="customer.medical_certificate_path"
                    >Oui
                    <a
                      :href="
                        route().t.url +
                        $inertia.page.props.appPathMedicalCertificate +
                        customer.medical_certificate_path
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
              </div>
              <div class="col-md-6">
                <div class="">
                  <h2 class="h4">Centre</h2>
                  <div class="activities_user block">
                    <div class="activity">
                      <h3 class="h5">Nom du centre</h3>
                    </div>
                  </div>
                </div>
                <div class="">
                  <h2 class="h4">Activités</h2>
                  <div class="activities_user block">
                    <div class="activity">
                      <h3 class="h5">Nom activité</h3>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div>
      <div class="comments-list">
        <h2 class="h4">Commentaires</h2>
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
      <comment-form
        :user_id="customer.id"
        v-on:comment-added="refreshCommenList"
      ></comment-form>
    </div>
  </app-layout>
</template>

<script>
import profilPhoto from "@/Pages/Components/profilPhoto.vue";
import BDropdown from "@/Pages/Components/BDropdown.vue";
import JetDropdownLink from "@/Jetstream/DropdownLink";

export default {
  components: {
    profilPhoto,
    BDropdown,
    JetDropdownLink,
  },
  props: ["customer"],
  data() {
    return {
      comments: this.customer.comments,
    };
  },
  methods: {
    async delete_customer() {
      await this.$inertia.delete("/customers/" + this.customer.id, {
        onBefore: this.msgConfirm("Confirmer la suppression"),
      });
    },
    refreshCommenList() {
      axios
        .get(route("customers.comments", this.customer.id))
        .then((response) => {
          this.comments = response.data;
        });
    },
    activate_account() {
      axios
        .put(route("customers.status.change", this.customer.id), {
          activated: true,
        })
        .then((response) => {
          toastr.success(response.data[1]);
          this.customer.activated = true;
        });
    },
    disable_account() {
      axios
        .put(route("customers.status.change", this.customer.id), {
          activated: false,
        })
        .then((response) => {
          alert(response.data[1]);
          this.customer.activated = false;
        });
    },
  },
};
</script>

<style scoped>
.photo {
  position: relative;
}

/* .btn-edit-profil {
  position: absolute;
  bottom: 0;
} */
</style>
