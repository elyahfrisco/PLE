<template>
  <div class="row">
    <div class="form-group col-lg-3 col-md-4">
      <label>Centres</label>
      <Multiselect
        v-model="form.establishment_id"
        placeholder="--centres--"
        :options="$page.props.establishments_list"
        label="name"
        valueProp="id"
      />
    </div>
    <div class="form-group col-lg-3 col-md-4">
      <label>Activités</label>
      <Multiselect
        v-model="form.activity_id"
        placeholder="--activités--"
        :options="select.activities"
        :searchable="true"
        label="name"
        valueProp="id"
      />
    </div>
    <div class="form-group col-lg-3 col-md-4">
      <label>Status</label>
      <Multiselect
        v-model="form.renewal_status"
        placeholder="--statut--"
        :options="select.renewal_status"
        label="label"
        trackBy="label"
        valueProp="value"
      />
    </div>
  </div>
  <div class="row">
    <div class="form-group col-md">
      <label class="transparent d-block">_</label>
      <button @click.prevent="applyFilters" class="btn btn-success mb-1">
        <i class="fa fa-filter mr-2"></i> Appliquer les filtres
      </button>
      <button @click.prevent="resetFilters" class="btn btn-primary ml-2">
        <i class="fa fa-trash mr-2"></i> Retirer les filtres
      </button>
    </div>
  </div>
</template>

<script>
export default {
  data: function() {
    return {
      form: {
        establishment_id: null,
        activity_id: null
      },
      select: {
        renewal_status: []
      },
      mounted: false
    };
  },
  methods: {
    getActivities() {
      axios.get(route("activities.index")).then(response => {
        if (response.data != undefined) {
          this.select.activities = response.data.activities;
        }
      });
    },
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
      this.form.establishment_id = this.form.activity_id = this.form.renewal_status = null;

      this.applyFilters();
    }
  },
  mounted: function() {
    this.href = this.url ? this.url : this.current_url;

    if (this.$page.props.default_establishment_id) {
      this.form.establishment_id = this.$page.props.default_establishment_id;
    } else if (this.q_.filterBy && this.q_.filterBy.establishment_id) {
      this.form.establishment_id = this.q_.filterBy.establishment_id;
    }

    if (this.q_.filterBy && this.q_.filterBy.activity_id) {
      this.form.activity_id = this.q_.filterBy.activity_id;
    }

    axios.get(route("api.renewals.status")).then(response => {
      this.select.renewal_status = response.data.filter(
        status =>
          status.value !== "not_informed" &&
          status.value !== "continue" &&
          status.value !== "stop"
      );

      setTimeout(() => {
        if (this.q_.filterBy && this.q_.filterBy.renewal_status) {
          this.form.renewal_status = this.q_.filterBy.renewal_status;
        }
      }, 100);
    });

    this.getActivities();
  }
};
</script>

<style scoped></style>
