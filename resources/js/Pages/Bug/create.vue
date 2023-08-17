<template>
  <app-layout :hideTitle="true">
    <div class="bug-create-page">
      <h1>Contactez Nous !</h1>
      <div class="bug-create-page__form w-100">
        <div class="bug-create-page__form-item w-100">
          <span class="bug-create-page__form-item__label">Objet</span>
          <InputText
            placeholder="Signaler un bug sur [...]"
            class="w-100"
            v-model="value.title"
          />
        </div>
        <div class="bug-create-page__form-item__area">
          <span class="bug-create-page__form-item__label">Description</span>
          <quill-editor
            v-model:value="value.content"
            :options="{
              placeholder: 'Details du bug...',
              height: 200,
              modules: {
                toolbar: [
                  ['bold', 'italic'],
                  [{ list: 'ordered' }, { list: 'bullet' }],
                  [{ color: [] }, { background: [] }],
                  [{ font: [] }],
                  [{ align: [] }]
                ]
              }
            }"
            required
          />
        </div>
        <div class="bug-create-page__form-item">
          <button
            class="btn bug-create-page__form-item__button"
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
    this.value.user_id = this.auth_user.id;
    this.value.page = this.$page.props.req.source;
  },
  data: () => ({
    centres: [],
    value: {
      title: "",
      content: "",
      user_id: null,
      page: null
    },
    isDisabled: false
  }),
  methods: {
    sendMessage: function() {
      this.$inertia.post(route("bugs.store"), this.value, {
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
        this.isDisabled = !this.value.title || !this.value.content;
      }
    }
  }
};
</script>

<style lang="scss" scoped>
@import "@/Pages/Components/main.scss";

.bug-create-page {
  .bug-create-page__form {
    @include desktopBreakpoint {
      width: 75%;
    }

    .bug-create-page__form-item__label {
      font-weight: bold;
      color: $color-lpdl;
    }

    .bug-create-page__form-item {
      width: 48%;
      margin-bottom: 1rem;

      @include mobileBreakpoint {
        width: 100%;
      }
    }

    .bug-create-page__form-item__button {
      background-color: $color-lpdl;
      color: white;
      margin-top: 1rem;

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
