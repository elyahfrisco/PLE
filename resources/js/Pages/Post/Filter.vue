<template>
  <div class="row">
    <div class="form-group col-lg-3 col-md-4">
      <label>Categorie</label>
      <Multiselect
        v-model="form.post_category_id"
        placeholder="--Categorie d'article--"
        :options="$page.props.post_category"
        label="name"
        valueProp="id"
        :canDeselect="false"
        required
      />
    </div>
    <div class="form-group col-md">
      <label class="transparent d-block">_</label>
      <button @click.prevent="applyFilters" class="btn btn-success">
        <i class="fa fa-filter mr-2"></i> Appliquer le filtre
      </button>
      <button @click.prevent="resetFilters" class="btn btn-primary ml-2">
        <i class="fa fa-trash mr-2"></i> Retirer le filtre
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      form: {
        post_category_id: null
      },
      mounted: false
    };
  },
  methods: {
    applyFilters() {
      var params = {
        data: {
          q: this.q,
          account_type: this.account_type,
          filterBy: this.filterEmpty(this.form)
        },
        preserveScroll: true
      };

      params.page ? delete params.page : 0;
      this.$emitter.emit("PageLoading");
      this.$inertia.visit(this.href, params);
    },
    resetFilters() {
      this.form.post_category_id = null;
      this.applyFilters();
    }
  },
  mounted: function() {
    this.href = this.url ? this.url : this.current_url;
    if (this.q_.filterBy && this.q_.filterBy.post_category_id) {
      this.form.post_category_id = this.q_.filterBy.post_category_id;
    }
  }
};
</script>

<style scoped></style>
