<template>
  <div class="card flat card-mail-template">
    <div class="card-header">
      <h3 class="card-title">{{ email_template.title }}</h3>
      <div class="card-tools">
        <button
          type="button"
          class="btn btn-tool"
          @click.prevent="removeTemplate"
        >
          <i class="fas fa-times"></i>
        </button>
      </div>
    </div>
    <div class="card-body">
      <perfect-scrollbar>
        <div class="w-100 ck-content" v-html="email_template.content"></div>
      </perfect-scrollbar>
    </div>
    <button
      class="btn btn-primary btn-sm flat"
      @click.prevent="$emit('select_template', email_template)"
    >
      Selectionner ce modèle
    </button>
  </div>
</template>

<script>
export default {
  props: ["email_template"],
  methods: {
    removeTemplate() {
      this.$inertia.delete(
        route("mail_template.delete", this.email_template.id),
        {
          onBefore: () => confirm("Supprimer le modèle ?"),
          onSuccess: () => this.$emit("refresh_template_list"),
          preserveScroll: true
        }
      );
    }
  }
};
</script>

<style scoped lang="scss">
.card-mail-template {
  // border-radius: none !important;
}
.ps {
  height: 250px;
}
</style>
