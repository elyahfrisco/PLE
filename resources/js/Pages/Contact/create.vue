<template>
  <app-layout :hideTitle="true">
    <div class="contact-create-page">
      <h1>Contactez Nous !</h1>
      <div class="contact-create-page__form w-100">
        <div class="contact-create-page__form-item">
          <span class="contact-create-page__form-item__label">Centre</span>
          <Multiselect
            placeholder="--Centres--"
            :options="centres"
            v-model="value.establishment_id"
          />
        </div>
        <div class="contact-create-page__form-item">
          <span class="contact-create-page__form-item__label">Objet</span>
          <InputText
            placeholder="Demande d'information sur [...]"
            v-model="value.object"
          />
        </div>
        <div class="contact-create-page__form-item__area">
          <span class="contact-create-page__form-item__label"
            >Message/Avis</span
          >
          <InputTextArea placeholder="Votre message" v-model="value.content" />
        </div>
        <div class="contact-create-page__form-item">
          <button
            class="btn contact-create-page__form-item__button"
            :disabled="isDisabled"
            @click="sendMessage"
          >
            <i class="fa fa-paper-plane mr-2"></i>
            Envoyer
          </button>
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import InputText from "@/Pages/Components/Inputs/InputText.vue";
import InputTextArea from "@/Pages/Components/Inputs/InputTextArea.vue";

export default {
  props: ["establishments", "succes"],
  components: {
    InputText,
    InputTextArea
  },
  mounted() {
    this.centres = this.establishments.map(e => ({
      value: e.id,
      label: e.name
    }));
    this.value.user_id = this.auth_user.id;
  },
  data: () => ({
    centres: [],
    value: {
      establishment_id: null,
      object: "",
      content: "",
      user_id: null
    },
    isDisabled: false
  }),
  methods: {
    sendMessage: function() {
      this.$inertia.post(route("contact.store"), this.value, {
        onSuccess: () => {
          this.iReload(this);
        }
      });
    }
  },
  watch: {
    value: {
      deep: true,
      handler() {
        this.isDisabled =
          !this.value.establishment_id ||
          !this.value.object ||
          !this.value.content;
      }
    }
  }
};
</script>

<style lang="scss" scoped>
@import "@/Pages/Components/main.scss";

.contact-create-page {
  .contact-create-page__form {
    display: flex;
    justify-content: space-between;
    width: 100%;
    flex-wrap: wrap;

    @include desktopBreakpoint {
      width: 75%;
    }

    .contact-create-page__form-item__label {
      font-weight: bold;
      color: $color-lpdl;
    }

    .contact-create-page__form-item {
      width: 48%;
      margin-bottom: 1rem;

      @include mobileBreakpoint {
        width: 100%;
      }
    }

    .contact-create-page__form-item__area {
      width: 100%;
    }

    .contact-create-page__form-item__button {
      background-color: $color-lpdl;
      color: white;
      margin-top: 1rem;
      box-sizing: border-box;

      &:disabled {
        background-color: $color-cyan;
        opacity: 0.5;
      }

      &:active {
        opacity: 0.8;
      }
    }
  }
}
</style>
