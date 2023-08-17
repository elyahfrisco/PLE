<template>
  <unauth-layout>
    <jet-authentication-card :large="true">
      <template #headerTitle>Inscrivez-vous</template>
      <template #headerDescription
        >Lorem ipsum dolor sit amet consectetur adipisicing elit. Corrupti
        delectus reprehend</template
      >

      <template #NavTabButtons>
        <inertia-link :href="route('login')" class="mt-1 flex-fill">
          Connexion
        </inertia-link>
        <inertia-link :href="route('signup')" class="mt-1 flex-fill active">
          S'inscrire
        </inertia-link>
      </template>

      <form @submit.prevent="submit" class="py-12">
        <div class="mt-8 max-w-md">
          <div class="">
            <tempate v-show="step == 1">
              <div class="step1">
                <div class="row mx-0">
                  <div class="form-group col-md-6 pl-0">
                    <label>Prénoms</label>
                    <input
                      placeholder="Prénoms"
                      type="text"
                      class="form-control"
                      v-model="form.first_name"
                    />
                    <small class="validation-error" v-if="errors.first_name">{{
                      errors.first_name
                    }}</small>
                  </div>
                  <div class="form-group col-md-6 pr-0">
                    <label>Nom</label>
                    <input
                      placeholder="Nom"
                      type="text"
                      class="form-control"
                      v-model="form.name"
                    />
                    <small class="validation-error" v-if="errors.name">{{
                      errors.name
                    }}</small>
                  </div>
                </div>
                <div class="form-group">
                  <div class="form-group clearfix">
                    <label>Sexe</label>
                    <div class="icheck-success d-inline ml-4">
                      <input
                        type="radio"
                        value="male"
                        id="male"
                        v-model="form.gender"
                      />
                      <label for="male">Homme</label>
                    </div>
                    <div class="icheck-success d-inline ml-4">
                      <input
                        type="radio"
                        value="female"
                        id="female"
                        v-model="form.gender"
                      />
                      <label for="female">Femme</label>
                    </div>
                  </div>
                  <small class="validation-error" v-if="errors.gender">{{
                    errors.gender
                  }}</small>
                </div>
                <div class="form-group">
                  <label>Date de naissance</label>
                  <input
                    placeholder="Date de naissance"
                    type="date"
                    class="form-control"
                    v-model="form.birth_date"
                  />
                  <small class="validation-error" v-if="errors.birth_date">{{
                    errors.birth_date
                  }}</small>
                </div>
                <div class="form-group">
                  <label>Adresse</label>
                  <input
                    placeholder="Adresse"
                    type="text"
                    class="form-control"
                    v-model="form.address"
                  />
                  <small class="validation-error" v-if="errors.address">{{
                    errors.address
                  }}</small>
                </div>
                <div class="row mx-0">
                  <div class="form-group col-md-6 pl-0">
                    <label>Code postal</label>
                    <input
                      placeholder="Code postal"
                      type="number"
                      class="form-control"
                      v-model="form.postal_code"
                      @change="getCity"
                      @input="getPostalCodeSuggestionData"
                      list="postal_code_sugg"
                      autocomplete="off"
                    />
                    <datalist id="postal_code_sugg">
                      <option v-for="sug in postal_code_suggestion" :key="sug">
                        {{ sug }}
                      </option>
                    </datalist>
                    <small class="validation-error" v-if="errors.postal_code">{{
                      errors.postal_code
                    }}</small>
                  </div>
                  <div class="form-group col-md-6 pr-0">
                    <label>Ville</label>
                    <input
                      placeholder="Ville"
                      type="text"
                      class="form-control"
                      v-model="form.city"
                    />
                    <small class="validation-error" v-if="errors.city">{{
                      errors.city
                    }}</small>
                  </div>
                </div>
                <div class="form-group">
                  <label>Email</label>
                  <input
                    placeholder="Email"
                    type="email"
                    class="form-control"
                    v-model="form.email"
                    required
                  />
                  <small class="validation-error" v-if="errors.email">{{
                    errors.email
                  }}</small>
                </div>
                <div class="form-group">
                  <label>Téléphone</label>
                  <input
                    placeholder="Téléphone"
                    type="text"
                    class="form-control"
                    v-model="form.phone"
                    pattern="[0-9 ()\-]+"
                    required
                  />
                  <small class="validation-error" v-if="errors.phone">{{
                    errors.phone
                  }}</small>
                </div>
                <div class="row mx-0">
                  <div class="form-group col-md-6 pl-0">
                    <label>Mot de passe</label>
                    <input
                      placeholder="Mot de passe"
                      type="password"
                      class="form-control"
                      v-model="form.password"
                    />
                    <small class="validation-error" v-if="errors.password">{{
                      errors.password
                    }}</small>
                  </div>
                  <div class="form-group col-md-6 pr-0">
                    <label>Confirmer Mot de passe</label>
                    <input
                      placeholder="Confirmer Mot de passe"
                      type="password"
                      class="form-control"
                      v-model="form.password_confirm"
                    />
                    <small
                      class="validation-error"
                      v-if="errors.password_confirm"
                      >{{ errors.password_confirm }}</small
                    >
                  </div>
                </div>
              </div>
            </tempate>
            <tempate v-show="step == 2">
              <div class="step2">
                <div class="form-group">
                  <label>Contact origin</label>

                  <Multiselect
                    v-model="form.contact_origin"
                    placeholder="--Contact origin--"
                    :options="select.contact_origins"
                    required
                  />

                  <small
                    class="validation-error"
                    v-if="errors.contact_origin"
                    >{{ errors.contact_origin }}</small
                  >

                  <textarea
                    class="form-control flat"
                    rows="3"
                    v-model="form.precision_contact_origin"
                   
                    placeholder="Préciser l'origine de contact..."
                  ></textarea>
                </div>

                <div class="form-group">
                  <label>Piscine souhaitée</label>
                  <Multiselect
                    v-model="form.establishment_id"
                    placeholder="--Piscine souhaitée--"
                    :options="select.establishments"
                    required
                  />
                  <small
                    class="validation-error"
                    v-if="errors.establishment_id"
                    >{{ errors.establishment_id }}</small
                  >
                </div>

                <div class="form-group">
                  <label>Activité</label>

                  <Multiselect
                    v-model="form.activity_id"
                    placeholder="--Activité--"
                    :options="select.activities"
                    required
                  />

                  <small class="validation-error" v-if="errors.activity">{{
                    errors.activity
                  }}</small>
                </div>

                <div class="form-group">
                  <label>Information suplementaire (...)</label>
                  <textarea
                    placeholder="Information suplementaire "
                    class="form-control flat"
                    rows="3"
                    v-model="form.additional_information"
                  ></textarea>
                  <!-- minlength="20" -->
                  <small
                    class="validation-error"
                    v-if="errors.additional_information"
                    >{{ errors.additional_information }}</small
                  >
                </div>
              </div>
            </tempate>
          </div>
          <tempate v-show="step == 1">
            <div class="text-center">
              <button
                type="button"
                @click.prevent="next_step"
                class="btn btn-step"
                :disabled="processing"
              >
                <span
                  v-if="processing"
                  class="spinner-border spinner-border-sm"
                  role="status"
                  aria-hidden="true"
                ></span>
                Suivant
              </button>
            </div>
          </tempate>
          <tempate v-show="step == 2">
            <div class="text-center">
              <button
                type="button"
                @click.prevent="prev_step"
                class="btn mx-1 btn-step"
                :disabled="processing"
              >
                Retour
              </button>
              <button type="submit" :disabled="processing" class="btn mx-1">
                <span
                  v-if="processing"
                  class="spinner-border spinner-border-sm"
                  role="status"
                  aria-hidden="true"
                ></span>
                S'inscrire
              </button>
            </div>
          </tempate>
        </div>

        <p class="my-1 text-right">
          <inertia-link :href="route('login')" class="underline"
            >Se connecter à un compte existant</inertia-link
          >
        </p>
      </form>
    </jet-authentication-card>
  </unauth-layout>
</template>

<script>
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard";
import Multiselect from "@vueform/multiselect";
export default {
  props: ["errors", "contact_origins", "establishments", "activities"],
  components: {
    JetAuthenticationCard,
    Multiselect
  },
  data() {
    return {
      form: {
        name: null,
        first_name: null,
        birth_date: null,
        gender: null,
        address: null,
        postal_code: null,
        city: null,
        activity_id: null,
        contact_origin: null,
        precision_contact_origin: null,
        email: null,
        phone: null,
        password: null,
        password_confirm: null,
        additional_information: null,
        is_signup: true
      },
      postal_code_suggestion: [],
      step: 1,
      select: Object,
      processing: false
    };
  },
  beforeMount() {
    this.select.establishments = this.establishments.map(a => {
      return { value: a.id, label: a.name };
    });

    this.select.establishments.push({ value: 0, label: "Les deux" });

    this.select.contact_origins = this.contact_origins.map(a => {
      return { value: a.designation, label: a.designation };
    });
    this.select.activities = this.activities.map(a => {
      return { value: a.id, label: a.name };
    });
  },
  methods: {
    next_step() {
      this.processing = true;
      this.$inertia.post("/customers", this.form, {
        onFinish: response => {
          var errors_ = Object.entries(_.cloneDeep(this.errors));
          if (
            (errors_.length == 2 &&
              errors_[1][0] == "additional_information" &&
              errors_[0][0] == "contact_origin") ||
            (errors_.length == 1 &&
              (errors_[0][0] == "additional_information" ||
                errors_[0][0] == "contact_origin"))
          ) {
            this.step = 2;
          } else {
            this.step = 1;
          }
          this.processing = false;
        }
      });
    },
    prev_step() {
      this.step--;
    },
    submit() {
      this.processing = true;
      this.$inertia.post(route("account.store"), this.form, {
        preserveState: false,
        onFinish: () => {
          this.processing = false;
        }
      });
    },
    getCity() {
      const postal_code = this.form.postal_code;
      if (postal_code.length >= 5) {
        axios
          .get("https://vicopo.selfbuild.fr/cherche/" + postal_code)
          .then(response => {
            this.form.city = response.data.cities[0].city;
          });
      }
    },
    getPostalCodeSuggestionData() {
      const postal_code = this.form.postal_code;
      if (postal_code.length > 1) {
        axios
          .get("https://vicopo.selfbuild.fr/cherche/" + postal_code)
          .then(response => {
            const postal_code = [];
            response.data.cities.map(c => {
              postal_code.push(c.code);
            });
            this.postal_code_suggestion = [...new Set(postal_code)];
          });
      }
    }
  }
};
</script>


