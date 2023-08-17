<template>
  <unauth-layout>
    <jet-authentication-card>
      <template #headerTitle>Réaccédez à votre compte</template>
      <template #headerDescription
        >Forgot your password? No problem. Just let us know your email address
        and we will email you a password reset link that will allow you to
        choose a new one.</template
      >

      <div v-if="status" class="alert alert-success" role="alert">
        {{ status }}
      </div>

      <jet-validation-errors class="mb-2" />

      <form @submit.prevent="submit">
        <div>
          <!-- <jet-label for="email" value="Email" /> -->
          <jet-input
            id="email"
            type="email"
            class="text-center"
            placeholder="Votre email"
            v-model="form.email"
            required
            autofocus
          />
        </div>

        <div class="text-center mt-4">
          <jet-button
            :class="{ 'text-white-50': form.processing }"
            :disabled="form.processing"
          >
            Réinitialisation le mot de passe
          </jet-button>
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
import JetLabel from "@/Jetstream/Label";
import JetValidationErrors from "@/Jetstream/ValidationErrors";

export default {
  components: {
    JetAuthenticationCard,
    JetAuthenticationCardLogo,
    JetButton,
    JetInput,
    JetLabel,
    JetValidationErrors
  },

  props: {
    status: String
  },

  data() {
    return {
      form: this.$inertia.form({
        email: ""
      })
    };
  },

  methods: {
    submit() {
      this.form.post(this.route("password.email"));
    }
  }
};
</script>
