use App\Models\Planning;
<template>
  <app-layout>
    <template #pageTitle>Import client</template>

    <form @submit.prevent="submit">
      <small
        class="validation-error"
        v-if="errors && errors.customers_imported"
        >{{ errors.customers_imported }}</small
      >

      <div class="form-group text-center">
        <label>Type de fichier</label>
        <div class="">
          <div class="icheck-success d-inline">
            <input
              type="radio"
              :value="1"
              id="type1"
              required
              v-model="form.type"
            />
            <label for="type1">Client</label>
          </div>
          <div class="icheck-success d-inline ml-4">
            <input
              type="radio"
              :value="2"
              id="type2"
              required
              v-model="form.type"
            />
            <label for="type2">Souscription</label>
          </div>
        </div>
      </div>

      <div class="form-group col-lg-6 offset-lg-3">
        <div class="input-group">
          <div
            class="custom-file d-flex align-items-center"
            @dragover="dragover"
            @dragleave="dragleave"
            @drop="drop"
          >
            <input
              v-if="form.type == 1"
              type="file"
              class="custom-file-input"
              id="file_exported"
              :accept="'.xls,.xlsx,.csv' + other_file_extension_can_accepted"
              multiple="multiple"
              @change="getFile"
            />
            <input
              v-if="form.type == 2"
              type="file"
              class="custom-file-input"
              id="file_exported"
              accept=".xls,.xlsx,.csv"
              @change="getFile"
            />
            <label
              class="custom-file-label file_exported__label text-center"
              for="file_exported"
              :class="{
                has_file: form.customers_imported
              }"
            >
              <template v-if="form.customers_imported">
                <i class="fa fa-file mr-2"></i>
                <template v-for="(file, i) in form.customers_imported" :key="i">
                  {{ file.name }} ,
                </template>
              </template>
              <span v-else
                >Selectionner le fichier
                <small class="muted" v-if="form.type == 1"
                  >.xls,.xlsx,.csv ( max {{ max_file_upload }})</small
                >
                <small class="muted" v-else>.xls,.xlsx,.csv</small>
              </span>
            </label>
          </div>
        </div>
      </div>

      <div v-if="form.type == 1" class="row">
        <div class="form-group col-md-3 offset-md-3 text-center mt-4">
          <label>Type de client</label>
          <Multiselect
            v-model="form.type_user"
            placeholder="--type de client--"
            :options="select.user_types"
            :required="true"
            :canDeselect="false"
          />
        </div>
        <div class="form-group col-md-3 text-center mt-4">
          <label>Centre</label>
          <Multiselect
            v-model="form.establishment_id"
            placeholder="--centre--"
            :options="$page.props.establishments_list"
            :searchable="true"
            :required="true"
            :canDeselect="false"
            label="name"
            valueProp="id"
          />
        </div>
      </div>

      <div v-if="form.type == 2" class="row justify-content-center mt-4">
        <div class="form-group mr-1">
          <label>Centre</label>
          <Multiselect
            v-model="form.establishment_id"
            placeholder="--centre--"
            :options="$page.props.establishments_list"
            :searchable="true"
            :required="true"
            :canDeselect="false"
            label="name"
            valueProp="id"
            @change="getSeason"
          />
        </div>
        <div class="form-group mr-1">
          <label>Saison</label>
          <Multiselect
            v-model="form.season_id"
            placeholder="--seasons--"
            :options="select.seasons"
            :searchable="true"
            :required="true"
            :canDeselect="false"
            :loading="loadSeason"
            @change="getTrimester"
          />
        </div>
        <div class="form-group mr-1">
          <label>Trimestre</label>

          <Multiselect
            v-model="form.num_trimester"
            placeholder="--Trimestres--"
            :options="select.trimesters"
            :loading="loadTrimesters"
            :canDeselect="false"
            :searchable="true"
          />
        </div>
        <div class="form-group mr-1">
          <label>Activité</label>
          <Multiselect
            class="text-uppercase"
            v-model="form.activity_id"
            placeholder="--Activités--"
            :options="select.activities"
            :loading="loadActivities"
            :searchable="true"
          />
        </div>
      </div>

      <div class="row">
        <button
          type="submit"
          class="btn btn-success mx-auto mt-1"
          :class="{
            disabled: !canSubmit
          }"
        >
          Importer
        </button>
      </div>
    </form>
  </app-layout>
</template>

<script>
export default {
  props: ["max_file_upload"],
  data() {
    return {
      form: {
        date: null,
        customers_imported: null,
        type: 1,
        establishment_id: null,
        season_id: null,
        num_trimester: null,
        activity_id: null
      },
      select: {
        user_types: [
          { label: "Clients", value: "customer" },
          { label: "Ancien clients", value: "old_customer" },
          { label: "Prospects", value: "prospect" },
          { label: "List d'attente", value: "waiting_customer" }
        ]
      },
      loadSeason: false
    };
  },
  methods: {
    submit() {
      let data = this.$inertia.post(route("customer.import.store"), this.form, {
        onSuccess: () => {
          setTimeout(() => {
            // axios
            //   .get(route("work.in.one", "customer_import"))
            //   .then(function () {
            //     axios.get(route("work.in", "subscribe_a_customer_to_pass"));
            //   })
            //   .catch(function () {
            //     axios.get(route("work.in", "subscribe_a_customer_to_pass"));
            //   });
          }, 500);
          this.iReload();
        }
      });
    },
    getFile(e) {
      if (e.target.files.length > this.max_file_upload) {
        alert("Vous ne pouvez télécharger qu'un maximum de 8 fichiers");
        e.target.files = [];
      } else {
        this.form.customers_imported = e.target.files;
      }
    },
    getTrimester() {
      setTimeout(() => {
        this.getActivities();
        if (this.form.season_id) {
          this.loadTrimesters = true;
          axios
            .get(
              route("api.seasons.trimesters", {
                season: this.form.season_id,
                group: true
              })
            )
            .then(response => {
              this.select.trimesters = response.data.map(a => {
                return {
                  value: a.num_trimester,
                  label:
                    "T " +
                    a.num_trimester +
                    " (" +
                    this.dateFrMin(a.date_start) +
                    " - " +
                    this.dateFrMin(a.date_end) +
                    ")",
                  ...a
                };
              });
              this.loadTrimesters = false;
            });
        } else {
          this.select.trimesters = [];
        }
      }, 100);
    },
    getSeason() {
      setTimeout(() => {
        this.form.season_id = null;
        this.form.num_trimester = null;
        this.form.activity_id = null;
        this.select.trimesters = [];
        this.select.activities = [];

        if (this.form.establishment_id) {
          this.loadSeason = true;
          axios
            .get(
              route("api.establishments.seasons", this.form.establishment_id)
            )
            .then(response => {
              this.select.seasons = response.data.map(a => {
                return {
                  value: a.id,
                  label: a.year_start + " - " + a.year_end
                };
              });
              this.loadSeason = false;
            });
        } else {
          this.select.seasons = {};
        }
      }, 100);
    },
    getActivities() {
      setTimeout(() => {
        if (this.form.establishment_id) {
          this.loadActivities = true;
          axios
            .get(route("api.seasons.activities", this.form.season_id))
            .then(response => {
              this.select.activities = this.toSelect(response.data);
              this.loadActivities = false;
            });
        } else {
          this.select.activities = {};
        }
      }, 100);
    }
  },
  computed: {
    canSubmit() {
      return this.form.customers_imported &&
        ((this.form.type == 2 &&
          this.form.season_id &&
          this.form.establishment_id &&
          this.form.num_trimester &&
          this.form.activity_id) ||
          this.form.type == 1)
        ? true
        : false;
    },
    other_file_extension_can_accepted() {
      var str = "";
      for (let index = 0; index < 1000; index++) {
        str += ",." + ("000" + index).substr(-3);
      }
      return str;
    }
  }
};
</script>

<style scoped lang="scss">
.custom-file-input,
.file_exported__label {
  height: 50px !important;
}

.file_exported__label {
  font-size: 18px;
  font-weight: 400;
  line-height: 35px;

  &::after {
    display: none !important;
  }

  &.has_file {
    background-color: #12bfbf;
    color: white;
  }
}
</style>
