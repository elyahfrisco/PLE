<template>
  <form class="py-12">
    <div class="mt-8">
      <div class="row">
        <div class="col-md-6">
          <div class="row">
            <div class="form-group col-md-6">
              <label class="">Prénoms</label>
              <input
                type="text"
                class="form-control"
                placeholder=""
                required
                v-model="form.first_name"
              />
              <small class="validation-error" v-if="errors.first_name">{{
                errors.first_name
              }}</small>
            </div>
            <div class="form-group col-md-6">
              <label class="">Nom</label>
              <input
                type="text"
                class="form-control"
                placeholder=""
                required
                v-model="form.name"
              />
              <small class="validation-error" v-if="errors.name">{{
                errors.name
              }}</small>
            </div>
          </div>
          <div class="form-group">
            <label class="">Date de naissance</label>
            <input
              type="date"
              class="form-control"
              placeholder=""
              required
              v-model="form.birth_date"
            />
            <small class="validation-error" v-if="errors.birth_date">{{
              errors.birth_date
            }}</small>
          </div>

          <div
            class="form-group"
            v-if="user.role == 'prospect' || user.role == 'customer'"
          >
            <div class="icheck-primary icheck-sm">
              <input type="checkbox" v-model="form.is_child" id="is_child" />
              <label for="is_child">Enfant?</label>
            </div>
          </div>

          <div class="form-group">
            <label>Sexe</label>
            <div class="">
              <div class="icheck-success d-inline">
                <input
                  type="radio"
                  value="male"
                  id="male"
                  required
                  v-model="form.gender"
                />
                <label for="male" v-if="form.is_child">Garçon</label>
                <label for="male" v-else>Homme</label>
              </div>
              <div class="icheck-success d-inline ml-4">
                <input
                  type="radio"
                  value="female"
                  id="female"
                  required
                  v-model="form.gender"
                />
                <label for="female" v-if="form.is_child">Fille</label>
                <label for="female" v-else>Femme</label>
              </div>
            </div>
            <small class="validation-error block" v-if="errors.gender">{{
              errors.gender
            }}</small>
          </div>

          <div class="form-group">
            <label class="">Adresse</label>
            <input
              type="text"
              class="form-control"
              placeholder=""
              required
              v-model="form.address"
            />
            <small class="validation-error" v-if="errors.address">{{
              errors.address
            }}</small>
          </div>
          <div class="row">
            <div class="form-group col-md-6">
              <label class=""
                >Code postal
                <loadings :processing="loadingPostalCode" />
              </label>
              <input
                type="number"
                class="form-control"
                placeholder=""
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
            <div class="form-group col-md-6">
              <label class=""
                >Ville
                <loadings :processing="loadingCity" />
              </label>
              <input
                type="text"
                class="form-control"
                placeholder=""
                v-model="form.city"
                list="cities_sugg"
              />
              <datalist id="cities_sugg">
                <option v-for="sug in cities_suggestion" :key="sug">
                  {{ sug }}
                </option>
              </datalist>
              <small class="validation-error" v-if="errors.city">{{
                errors.city
              }}</small>
            </div>
          </div>
          <div class="form-group" v-if="!form.is_child">
            <label class="">E-mail</label>
            <input
              type="email"
              class="form-control"
              :readonly="form.is_child"
              placeholder=""
              v-model="form.email"
              required
            />
            <small class="validation-error" v-if="errors.email">{{
              errors.email
            }}</small>
          </div>
          <div class="form-group" v-if="form.is_child">
            <label class="">E-mail parent</label>
            <input
              type="email"
              class="form-control"
              placeholder="email parent 1"
              v-model="form.mail1"
              required
            />
            <small class="validation-error" v-if="errors.mail1">{{
              errors.mail1
            }}</small>
            <input
              type="email"
              class="form-control mt-2"
              placeholder="email parent 2"
              v-model="form.mail2"
              required
            />
            <small class="validation-error" v-if="errors.mail2">{{
              errors.mail2
            }}</small>
          </div>
          <div class="form-group" v-if="!form.id">
            <label class="">Téléphone</label>
            <input
              type="text"
              class="form-control"
              placeholder="Telephone 1"
              v-model="form.phone"
              pattern="[0-9 ()\-]+"
              required
            />
            <small class="validation-error" v-if="errors.phone">{{
              errors.phone
            }}</small>
            <div class="my-1">
              <div class="icheck-info icheck-sm d-inline">
                <input
                  type="radio"
                  value="mme"
                  v-model="form.type"
                  :id="'mme'"
                />
                <label :for="'mme'"> MME </label>
              </div>
              <div class="icheck-info icheck-sm d-inline ml-2">
                <input type="radio" value="mr" v-model="form.type" :id="'mr'" />
                <label :for="'mr'"> MR </label>
              </div>
              <div class="icheck-info icheck-sm d-inline ml-2">
                <input
                  type="radio"
                  value="autre"
                  v-model="form.type"
                  :id="'autre'"
                />
                <label :for="'autre'"> Autre </label>
              </div>
            </div>
            <input
              type="text"
              class="form-control mt-2"
              placeholder="Telephone 2"
              v-model="form.phone2"
              pattern="[0-9 ()\-]+"
              required
            />
            <small class="validation-error" v-if="errors.phone2">{{
              errors.phone2
            }}</small>
            <div class="my-1">
              <div class="icheck-info icheck-sm d-inline">
                <input
                  type="radio"
                  value="mme"
                  v-model="form.type2"
                  :id="'mme2'"
                />
                <label :for="'mme2'"> MME </label>
              </div>
              <div class="icheck-info icheck-sm d-inline ml-2">
                <input
                  type="radio"
                  value="mr"
                  v-model="form.type2"
                  :id="'mr2'"
                />
                <label :for="'mr2'"> MR </label>
              </div>
              <div class="icheck-info icheck-sm d-inline ml-2">
                <input
                  type="radio"
                  value="autre"
                  v-model="form.type2"
                  :id="'autre2'"
                />
                <label :for="'autre2'"> Autre </label>
              </div>
            </div>
          </div>
          <div class="form-group" v-else>
            <label class="block">Téléphone</label>
            <div v-for="(phone, i_phone) in form.phones" v-bind:key="phone.id">
              <div class="flex items-center">
                <div class="w-full">
                  <div class="input-group">
                    <div
                      class="input-group-prepend"
                      v-if="form.phones.length > 1"
                    >
                      <button
                        type="button"
                        class="btn btn-outline-danger border-muted btn-sm"
                        @click="deletePhone(i_phone)"
                      >
                        <i class="fa fa-trash"></i>
                      </button>
                    </div>
                    <input
                      type="text"
                      class="form-control"
                      placeholder=""
                      v-model="form.phones[i_phone].phone"
                      pattern="[0-9 ()\-.+]+"
                      required
                    />
                  </div>
                  <div class="my-1">
                    <div class="icheck-info icheck-sm d-inline">
                      <input
                        type="radio"
                        value="mme"
                        v-model="phone.type"
                        :id="'mme' + i_phone"
                      />
                      <label :for="'mme' + i_phone"> MME </label>
                    </div>
                    <div class="icheck-info icheck-sm d-inline ml-2">
                      <input
                        type="radio"
                        value="mr"
                        v-model="phone.type"
                        :id="'mr' + i_phone"
                      />
                      <label :for="'mr' + i_phone"> MR </label>
                    </div>
                    <div class="icheck-info icheck-sm d-inline ml-2">
                      <input
                        type="radio"
                        value="autre"
                        v-model="phone.type"
                        :id="'autre' + i_phone"
                      />
                      <label :for="'autre' + i_phone"> Autre </label>
                    </div>
                  </div>
                </div>
              </div>
              <small class="validation-error" v-if="errors['phones.']">{{
                errors["phones." + i_phone + ".phone"]
              }}</small>
            </div>
            <button
              type="button"
              class="btn btn-sm btn-outline-success d-block"
              @click="addPhone"
            >
              Ajouter un numero de Téléphone
            </button>
          </div>
        </div>

        <div class="col-md-6">
          <template v-if="user.role == 'prospect'">
            <div class="row">
              <div class="form-group col-md-12">
                <label
                  >Activité(s) souhaité(s)
                  <a
                    href="#"
                    @click="addWish"
                    class="btn btn-outline-success py-0"
                    ><i class="fa fa-plus"></i></a
                ></label>
                <user-wishes
                  :wishesData="form.wishes"
                  v-model:wishes="form.wishes"
                />
                <small class="validation-error" v-if="error_activities">{{
                  error_activities
                }}</small>
              </div>
            </div>
          </template>
          <div
            class="form-group"
            v-if="user.role == 'prospect' || user.role == 'customer'"
          >
            <label class="">Contact origin</label>
            <Multiselect
              v-model="form.contact_origin"
              placeholder="--Contact origin--"
              :options="select.contact_origins"
              required
            />
            <small class="validation-error" v-if="errors.contact_origin">{{
              errors.contact_origin
            }}</small>
            <textarea
              class="form-control mt-2"
              rows="3"
              v-model="form.precision_contact_origin"
              
              placeholder="Préciser l'origine de contact..."
            ></textarea>
          </div>

          <div v-if="form.id" class="form-group">
            <div class="icheck-primary icheck-sm">
              <input
                type="checkbox"
                v-model="edit_password"
                :value="true"
                id="edit_password"
              />
              <label for="edit_password">Modifier le mot de passe</label>
            </div>
          </div>

          <template v-if="form.id && edit_password">
            <div class="form-group">
              <label>Mot de passe</label>
              <input
                type="password"
                class="form-control"
                placeholder=""
                v-model="form.password"
              />
              <small class="validation-error" v-if="errors.password">{{
                errors.password
              }}</small>
            </div>
            <div class="block" v-if="password_not_empty">
              <label>Confirmer Mot de passe</label>
              <input
                type="password"
                class="form-control"
                placeholder=""
                v-model="form.password_confirm"
              />
              <small class="validation-error" v-if="errors.password_confirm">{{
                errors.password_confirm
              }}</small>
            </div>
          </template>

          <div class="form-group">
            <span class="">Information suplementaire</span>
            <textarea
              class="form-control"
              rows="3"
              v-model="form.additional_information"
              minlength="20"
            ></textarea>
            <small
              class="validation-error"
              v-if="errors.additional_information"
              >{{ errors.additional_information }}</small
            >
          </div>
          <button
            type="button"
            @click.prevent="submit"
            class="btn btn-success"
            v-if="!form.id"
          >
            Enregistrer
          </button>

          <template v-else>
            <button
              type="button"
              @click.prevent="update"
              class="btn btn-success"
            >
              Modifier
            </button>
            <a
              class="py-2 px-3 my-2 bg-gray-400 text-white rounded"
              :href="route('customers.show', this.form.id)"
            >
              Annuler
            </a>
          </template>
        </div>
      </div>
    </div>
  </form>
</template>

<script>
import formTimeGroup from "./formTimeGroup.vue";
import UserWishes from "./UserWishes.vue";

export default {
  props: {
    user: {
      type: Object,
      default: {}
    }
  },
  components: {
    formTimeGroup,
    UserWishes
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
        contact_origin: null,
        precision_contact_origin: null,
        email: null,
        phone: null,
        phone2: null,
        type: null,
        type2: null,
        maternity_name: "",
        additional_information: null,
        password: null,
        account_type: this.$page.props.account_type,
        role_id: null,
        is_child: false,
        create_for_subscription: this.$page.props.create_for_subscription
      },
      edit_password: false,
      select: Object,
      postal_code_suggestion: [],
      cities_suggestion: [],
      contact_origins: this.$page.props.contact_origins,
      establishments: this.$page.props.establishments,
      activities: this.$page.props.activities,
      days: this.days(),
      account_type: this.$page.props.account_type,
      loadingCity: false,
      loadingPostalCode: false,
      error_activities: null
    };
  },
  beforeMount() {
    if (this.establishments) {
      this.select.establishments = [
        ...this.establishments.map(a => {
          return { value: a.id, label: a.name };
        }),
        { value: 0, label: "Les deux" }
      ];
    }

    if (this.contact_origins) {
      this.select.contact_origins = this.contact_origins.map(a => {
        return { value: a.designation, label: a.designation };
      });
    }

    if (this.activities) {
      this.select.activities = this.activities.map(a => {
        return { value: a.id, label: a.name };
      });
    }

    if (this.user.id != undefined) {
      this.form = this.user;
      this.form.is_child = this.form.is_child ? true : false;
    } else if (this.$page.props.account_type == "prospect") {
      this.user.role = "prospect";
    } else if (this.$page.props.account_type == "admin") {
      this.user.role = "admin";
    } else if (this.$page.props.account_type == "coach") {
      this.user.role = "coach";
    } else if (this.$page.props.account_type == "intervenant") {
      this.user.role = "intervenant";
    } else if (this.$page.props.account_type == "assistant") {
      this.user.role = "assistant";
    } else {
      this.user.role = "customer";
    }

    if (this.$page.props.user_to_duplicate) {
      let user_to_duplicate = this.$page.props.user_to_duplicate;
      this.form.name = user_to_duplicate.name;
      this.form.first_name = user_to_duplicate.first_name;
      this.form.gender = user_to_duplicate.gender;
      this.form.mail1 = user_to_duplicate.mail1;
      this.form.mail2 = user_to_duplicate.mail2;
      this.form.precision_contact_origin =
        user_to_duplicate.precision_contact_origin;
      this.form.postal_code = user_to_duplicate.postal_code;
      this.form.postal_code = user_to_duplicate.postal_code;
      this.form.is_child = user_to_duplicate.is_child ? true : false;
      this.form.contact_origin = user_to_duplicate.contact_origin;
      this.form.additional_information =
        user_to_duplicate.additional_information;
      this.form.address = user_to_duplicate.address;
      this.form.city = user_to_duplicate.city;

      if (user_to_duplicate.phones.length) {
        this.form.phone = user_to_duplicate.phones[0].phone;
        this.form.phone2 = user_to_duplicate.phones[1]
          ? user_to_duplicate.phones[1].phone
          : null;
      }
    }
  },
  mounted() {
    if (!this.$page.props.contact_origins) {
      axios.get(route("api.subscription.form_data")).then(response => {
        response = response.data;
        this.contact_origins = response.contact_origins;
        this.establishments = response.establishments;
        this.activities = response.activities;
        this.account_type = response.account_type;
      });
    }
  },
  computed: {
    password_not_empty() {
      if (this.form != undefined && this.form.password != null) {
        return (this.form.password + "").length > 0;
      }
      return false;
    }
  },
  methods: {
    submit() {
      if (
        (this.form.wishes == undefined || this.form.wishes == null) &&
        this.$page.props.account_type == "prospect"
      ) {
        this.error_activities = "L' activité souhaité est obligatoire";
      } else {
        this.$inertia.post(route("account.store"), this.form, {
          onSuccess: () => {
            this.iReload(this);
          },
          preserveScroll: true
        });
      }
    },
    update() {
      this.$inertia.put(
        route("account.update", { user_id: this.user.id }),
        this.form,
        {
          onSuccess: () => {
            this.iReload(this);
          }
        }
      );
    },
    deletePhone(index) {
      this.form.phones.splice(index, 1);
    },
    addPhone() {
      this.form.phones.push({
        id: null,
        phone: null,
        owner: null,
        type: "portable",
        user_id: this.user.id
      });
    },
    addWish() {
      $("#AddWish").modal();
    },
    getCity() {
      const postal_code = this.form.postal_code;
      this.form.city = null;
      if (postal_code.toString().length == 5) {
        this.loadingCity = true;
        axios
          .get("https://vicopo.selfbuild.fr/cherche/" + postal_code)
          .then(response => {
            const cities = [];
            response.data.cities.map(c => {
              cities.push(c.city);
            });
            this.cities_suggestion = [...new Set(cities)];
            this.loadingCity = false;
            $("[list=cities_sugg]")[0].focus();
            $("[list=cities_sugg]")[0].click();
          });
      }
    },
    getPostalCodeSuggestionData() {
      const postal_code = this.form.postal_code;
      if (
        postal_code.toString().length > 1 &&
        postal_code.toString().length < 5
      ) {
        this.loadingPostalCode = true;
        axios
          .get("https://vicopo.selfbuild.fr/cherche/" + postal_code)
          .then(response => {
            const postal_code = [];
            response.data.cities.map(c => {
              postal_code.push(c.code);
            });
            this.postal_code_suggestion = [...new Set(postal_code)];
            this.loadingPostalCode = false;
          });
      }
    }
  }
};
</script>
