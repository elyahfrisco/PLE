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
                <label for="male">Homme</label>
              </div>
              <div class="icheck-success d-inline ml-4">
                <input
                  type="radio"
                  value="female"
                  id="female"
                  required
                  v-model="form.gender"
                />
                <label for="female">Femme</label>
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
              <label class="">Code postale</label>
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
              <label class="">Ville</label>
              <input
                type="text"
                class="form-control"
                placeholder=""
                v-model="form.city"
              />
              <small class="validation-error" v-if="errors.city">{{
                errors.city
              }}</small>
            </div>
          </div>
          <div class="form-group">
            <label class="">Email</label>
            <input
              type="email"
              class="form-control"
              placeholder=""
              v-model="form.email"
              required
            />
            <small class="validation-error" v-if="errors.email">{{
              errors.email
            }}</small>
          </div>
          <div class="form-group" v-if="!form.id">
            <label class="">Téléphone</label>
            <input
              type="text"
              class="form-control"
              placeholder=""
              v-model="form.phone"
              pattern="[0-9 ()\-]+"
              required
            />
            <small class="validation-error" v-if="errors.phone">{{
              errors.phone
            }}</small>
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
                        value="portable"
                        v-model="phone.type"
                        :id="'portable' + i_phone"
                      />
                      <label :for="'portable' + i_phone"> Portable </label>
                    </div>
                    <div class="icheck-info icheck-sm d-inline ml-2">
                      <input
                        type="radio"
                        value="fixe"
                        v-model="phone.type"
                        :id="'fixe' + i_phone"
                      />
                      <label :for="'fixe' + i_phone"> Fixe </label>
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
          <template v-if="account_type == 'prospect'">
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
              </div>
            </div>
            <div class="form-group">
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
          </template>

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
  props: ["customer"],
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
        additional_information: null,
        password: null,
        formP: this.$page.props.prospectForm
      },
      edit_password: true,
      select: Object,
      postal_code_suggestion: [],
      contact_origins: this.$page.props.contact_origins,
      establishments: this.$page.props.establishments,
      activities: this.$page.props.activities,
      days: this.days(),
      formP: this.$page.props.prospectForm
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

    if (this.customer) {
      this.form = this.customer;
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
      this.$inertia.post("/customers", this.form, {
        onSuccess: () => {
          this.iReload(this);
        }
      });
    },
    update() {
      this.$inertia.put(
        route("customers.update", { customer: this.customer.id }),
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
        user_id: this.customer.id
      });
    },
    addWish() {
      $("#AddWish").modal();
    },
    getCity() {
      const postal_code = this.form.postal_code;
      this.loadingCity = true;
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
