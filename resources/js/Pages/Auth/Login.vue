<template>
  <unauth-layout>
    <jet-authentication-card>
      <template #headerTitle>Connectez à votre compte</template>
      <template #headerDescription
        >Lorem ipsum dolor sit amet consectetur adipisicing elit. Corrupti
        delectus reprehenderit consequatur?</template
      >

      <template #NavTabButtons>
        <inertia-link
          :href="route('login')"
          class="mt-1 flex-fill active"
          preserve-scroll
        >
          Connexion
        </inertia-link>
        <inertia-link
          :href="route('signup')"
          class="mt-1 flex-fill"
          preserve-scroll
        >
          S'inscrire
        </inertia-link>
      </template>

      <jet-validation-errors class="mb-3" />

      <div
        v-if="status"
        class="alert alert-success mb-3 rounded-0"
        role="alert"
      >
        {{ status }}
      </div>

      <form @submit.prevent="submit">
        <div class="form-group">
          <!-- <jet-label for="email" value="Email" /> -->
          <jet-input
            id="email"
            type="email"
            placeholder="email"
            v-model="form.email"
            class="text-center"
            required
            autofocus
            autocomplete="off"
          />
        </div>

        <div class="form-group">
          <!-- <jet-label for="password" value="Password" /> -->
          <jet-input
            id="password"
            :type="see_password ? 'text' : 'password'"
            placeholder="Mot de pass"
            v-model="form.password"
            class="text-center"
            required
            autocomplete="current-password"
          />

          <button
            type="button"
            id="btn_see_password"
            @click.prevent="toggle_password_view"
          >
            <i class="fa fa-eye-slash" v-if="see_password"></i>
            <i class="fa fa-eye" v-else></i>
          </button>
        </div>

        <!-- <div class="form-group">
          <div class="custom-control custom-checkbox">
            <jet-checkbox
              id="remember_me"
              name="remember"
              v-model:checked="form.remember"
            />

            <label class="custom-control-label" for="remember_me">
              Remember Me
            </label>
          </div>
        </div> -->

        <div class="mb-0">
          <div class="text-center">
            <jet-button
              :class="{ 'text-white-50': form.processing }"
              :disabled="form.processing"
            >
              Se connecter
            </jet-button>
            <br />
            <div class="py-2 text-right">
              <inertia-link
                v-if="canResetPassword"
                :href="route('password.request')"
                class="mt-3"
              >
                Mot de pass oublié ?
              </inertia-link>
            </div>

            <div class="px-4 col-md-7 mx-auto" v-if="isLocal()">
              <div class="bg-white rounded text-center row socials-login py-2">
                <p class="font-italic col-12 font-weight-bold small mb-1">
                  Se connecter avec
                </p>
                <a
                  href="#"
                  @click.prevent="social_login('faceboook')"
                  class="col-sm-4 my-2"
                >
                  <img
                    :src="route('home') + '/images/social_logo/faceboook.png'"
                    alt=""
                  />
                  <!-- <label for="">Facebook</label> -->
                </a>
                <a
                  href="#"
                  @click.prevent="social_login('google')"
                  class="col-sm-4 my-2"
                >
                  <img
                    :src="route('home') + '/images/social_logo/google.png'"
                    alt=""
                  />
                  <!-- <label for="">Google</label> -->
                </a>
                <a
                  href="#"
                  @click.prevent="social_login('apple')"
                  class="col-sm-4 my-2"
                >
                  <img
                    :src="route('home') + '/images/social_logo/apple.png'"
                    alt=""
                  />
                  <!-- <label for="">Apple</label> -->
                </a>
              </div>
            </div>
          </div>
        </div>
      </form>
    </jet-authentication-card>
  </unauth-layout>
</template>

<script>
import JetAuthenticationCard from "@/Jetstream/AuthenticationCard";
import JetAuthenticationCardLogo from "@/Jetstream/AuthenticationCardLogo";
import JetButton from "@/Jetstream/Button";
import JetInput from "@/Jetstream/Input";
import JetCheckbox from "@/Jetstream/Checkbox";
import JetLabel from "@/Jetstream/Label";
import JetValidationErrors from "@/Jetstream/ValidationErrors";

export default {
  components: {
    JetAuthenticationCard,
    JetAuthenticationCardLogo,
    JetButton,
    JetInput,
    JetCheckbox,
    JetLabel,
    JetValidationErrors
  },

  props: {
    canResetPassword: Boolean,
    status: String
  },

  data() {
    return {
      form: this.$inertia.form({
        email: "",
        password: "",
        remember: true
      }),
      see_password: false
    };
  },

  beforeMount() {
    if (this.isLocal()) {
      this.form.email = "admin@admin.com";
      this.form.password = "password";
    }
    // console.log(axios.baseURL);
  },

  methods: {
    submit() {
      this.form
        .transform(data => ({
          ...data,
          remember: this.form.remember ? "on" : ""
        }))
        .post(this.route("login"), {
          onFinish: response => {
            this.form.reset("password");
          },
          onSuccess: response => {
            this.reload();
          }
        });
    },
    social_login(social_name) {
      toastr.options.positionClass = "toast-bottom-right";
      toastr.error("test");
    },
    toggle_password_view() {
      this.see_password = !this.see_password;
    }
  }
};
</script>
<style scoped lang="scss">
#btn_see_password {
  color: white;
  float: right;
  background-color: rgba(#fff, 0.1);
  margin-right: 10px;
  margin-top: -35px;
  min-width: 30px;
  text-align: center;
  z-index: 5;
}
</style>
