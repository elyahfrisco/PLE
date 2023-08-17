<template>
  <div class="row">
    <div v-if="account_type == 'customer'" class="form-group col-lg-3 col-md-3">
      <label>Centres</label>
      <Multiselect
        v-model="form.establishment_id"
        placeholder="--centres--"
        :options="$page.props.establishments_list"
        label="name"
        trackBy="name"
        valueProp="id"
        @change="getSeasons"
        :searchable="true"
      />
    </div>
    <div v-if="account_type != 'customer'" class="form-group col-lg-3 col-md-3">
      <label>Type</label>
      <Multiselect
        v-bind:v-model="account_type"
        :options="select.type"
        label="name"
        valueProp="value"
      />
    </div>
    <div v-if="account_type != 'customer'" class="form-group col-lg-3 col-md-3">
      <label>Date début</label>
      <input type="date" class="form-control" v-model="form.date_debut" />
    </div>
    <div v-if="account_type != 'customer'" class="form-group col-lg-3 col-md-3">
      <label>Date fin</label>
      <input type="date" class="form-control" v-model="form.date_fin" />
    </div>
    <div class="form-group col-lg-3 col-md-3">
      <label>Activités</label>
      <Multiselect
        v-model="form.activity_id"
        placeholder="--activités--"
        :options="select.activities"
        label="name"
        trackBy="name"
        valueProp="id"
        :searchable="true"
      />
    </div>
    <div v-if="account_type == 'customer'" class="form-group col-lg-3 col-md-3">
      <label>Pass</label>
      <Multiselect
        v-model="form.pass_id"
        placeholder="--pass--"
        :options="select.passes"
        label="name"
        trackBy="name"
        valueProp="id"
        :searchable="true"
      />
    </div>
    <div v-if="account_type == 'customer'" class="form-group col-lg-3 col-md-3">
      <label>Groupe</label>
      <Multiselect
        v-model="form.planning_id"
        :options="
          async function(query) {
            return await getPlannings(query);
          }
        "
        placeholder="--Saisir le nom du groupe--"
        :filterResults="false"
        :resolveOnLoad="false"
        :minChars="2"
        :searchable="true"
        :disabled="disableSelectPlanning"
        :loading="loadPlanning"
        :clearOnSelect="false"
        delay="500"
        trackBy="group_name"
        label="group_name"
        valueProp="id"
        ref="selectPlanning"
        required
      />
    </div>
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
  props: ["account_type"],
  data: function() {
    return {
      form: {
        establishment_id: null,
        activity_id: null,
        pass_id: null,
        planning_id: null,
        date_debut: null,
        date_fin: null
      },
      disableSelectPlanning: false,
      loadPlanning: false,
      select: {},
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
    getPass() {
      axios.get(route("passes.index")).then(response => {
        if (response.data != undefined) {
          this.select.passes = response.data.passes;
        }
      });
    },
    getPlannings(query = "") {
      if (
        (!this.mounted && this.q_.filterBy && this.q_.filterBy.planning_id) ||
        query.length
      ) {
        this.loadPlanning = query ? true : false;
        let params =
          !this.mounted && !query
            ? {
                planning_id: this.q_.filterBy?.planning_id
              }
            : {
                q: query
              };
        return axios.get(route("api.plannings.list", params)).then(response => {
          this.select.plannings = response.data;
          this.loadPlanning = false;
          if (!this.mounted && !query) {
            this.form.planning_id = +this.q_.filterBy.planning_id;
            this.mounted = true;
          }
          return this.select.plannings;
        });
      }
      return [];
    },
    applyFilters() {
      var params = {
        data: {
          q: this.q,
          account_type: this.account_type,
          filterBy: this.form
        },
        preserveScroll: true
      };

      params.page ? delete params.page : 0;

      this.$inertia.visit(this.href, params);
    },
    resetFilters() {
      this.form.establishment_id = this.form.activity_id = this.form.pass_id = null;
      if ("attente" == this.account_type) {
        var params = {
          data: {
            q: this.q,
            account_type: "prospect",
            filterBy: this.form
          },
          preserveScroll: true
        };

        params.page ? delete params.page : 0;

        this.$inertia.visit(this.href, params);
      } else {
        this.applyFilters();
      }
    },
    removeNull(data) {
      var notNull = data.filter(function(el) {
        return el != null;
      });
      return notNull;
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

    if (this.q_.filterBy && this.q_.filterBy.planning_id) {
      this.$refs.selectPlanning.refreshOptions(() => {
        this.$refs.selectPlanning.select(this.form.planning_id);
      });
    }

    if (this.q_.filterBy && this.q_.filterBy.pass_id) {
      this.form.pass_id = this.q_.filterBy.pass_id;
    }

    if (this.q_.filterBy && this.q_.filterBy.date_debut) {
      this.form.date_debut = this.q_.filterBy.date_debut;
    }

    if (this.q_.filterBy && this.q_.filterBy.date_fin) {
      this.form.date_fin = this.q_.filterBy.date_fin;
    }

    this.getActivities();
    this.getPass();
    this.getPlannings();
    this.select.type = [
      { name: "Toute", value: "prospect" },
      { name: "Attente", value: "attente" }
    ];
  }
};
</script>

<style scoped></style>
