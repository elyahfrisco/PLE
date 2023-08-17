<template>
  <form action="" class="p-3 row">
    <div class="col-md-6">
      <div class="row">
        <div class="form-group col-md-10">
          <label>Nom</label>
          <input
            type="text"
            class="form-control"
            placeholder=""
            v-model="form.name"
          />
          <small class="validation-error" v-if="errors.name">{{
            errors.name
          }}</small>
        </div>

        <div class="form-group col-md-2">
          <label>Sigle</label>
          <input
            type="text"
            class="form-control"
            placeholder=""
            v-model="form.sigle"
          />
          <small class="validation-error" v-if="errors.sigle">{{
            errors.sigle
          }}</small>
        </div>
      </div>
      <div class="row">
        <div class="form-group col-md-4">
          <div class="icheck-primary icheck-sm d-inline">
            <input
              type="checkbox"
              checked=""
              value="care"
              v-model="form.care"
              id="care"
            />
            <label for="care">Soin</label>
          </div>
          <small class="validation-error" v-if="errors.care">{{
            errors.care
          }}</small>
        </div>
        <div class="form-group col-md-4">
          <div class="icheck-primary icheck-sm d-inline">
            <input
              type="checkbox"
              checked=""
              value="for_kid"
              v-model="form.for_kid"
              id="for_kid"
            />
            <label for="for_kid">Pour Enfant</label>
          </div>
          <small class="validation-error" v-if="errors.for_kid">{{
            errors.for_kid
          }}</small>
        </div>
      </div>
      <div class="form-group">
        <label>Description (résumé)</label>
        <textarea
          class="form-control"
          placeholder=""
          v-model="form.description"
          minlength="10"
          rows="5"
        ></textarea>
        <small class="validation-error" v-if="errors.description">{{
          errors.description
        }}</small>
      </div>
      <div class="form-group">
        <label>Description (full)</label>
        <textarea
          class="form-control"
          placeholder=""
          v-model="form.full_description"
          minlength="10"
          rows="9"
          required
        ></textarea>
        <small class="validation-error" v-if="errors.full_description">{{
          errors.full_description
        }}</small>
      </div>
      <div class="form-group">
        <label>Condition</label>
        <textarea
          class="form-control"
          placeholder=""
          v-model="form.condition"
          minlength="10"
          rows="10"
          required
        ></textarea>
        <small class="validation-error" v-if="errors.condition">{{
          errors.condition
        }}</small>
      </div>
      <div class="form-group">
        <label>Durée par défaut d'une séance</label>
        <input
          type="time"
          class="form-control"
          placeholder=""
          v-model="form.duration"
        />
        <small class="validation-error" v-if="errors.duration">{{
          errors.duration
        }}</small>
      </div>
      <div class="form-group">
        <label>Photo</label>
        <input type="file" class="form-control mb-3" @change="onFileChange" />
        <img
          v-bind:src="imagePreview"
          width="100"
          height="100"
          v-show="showPreview"
        />
      </div>
    </div>

    <div class="col-md-6">
      <label>Affichage sur le planning</label>
      <hr />
      <div class="row">
        <div class="form-group col-md-6">
          <label>Couleur de fond</label>
          <input
            type="color"
            class="form-control p-0 rounded-0"
            placeholder=""
            v-model="form.background_color"
          />
          <small class="validation-error" v-if="errors.background_color">{{
            errors.background_color
          }}</small>
        </div>
        <div class="form-group col-md-6">
          <label>Couleure de text</label>
          <input
            type="color"
            class="form-control p-0 rounded-0"
            placeholder=""
            v-model="form.font_color"
          />
          <small class="validation-error" v-if="errors.font_color">{{
            errors.font_color
          }}</small>
        </div>
      </div>
      <div class="form-group">
        <label>Aperçu</label>
        <div
          class="p-5 text-center text-uppercase h4"
          :style="{
            'background-color': form.background_color,
            color: form.font_color
          }"
        >
          {{ form.name }}
        </div>
      </div>
      <div class="form-group">
        <label class="">Activité(s) de récupération</label>
        <Multiselect
          v-model="form.activities_for_recuperation_id"
          placeholder="Selectionner les participants"
          :filterResults="false"
          :resolveOnLoad="false"
          :options="
            async function(query) {
              return await getActivities(query);
            }
          "
          :searchable="true"
          :minChars="3"
          :maxHeight="300"
          mode="tags"
          :loading="loadActivities"
          delay="0"
          trackBy="name"
          label="name"
          valueProp="id"
          ref="SelectActivities"
          required
        >
          <template v-slot:option="{ option }">
            <span class="ml-2">{{ option.name }}</span>
          </template>
        </Multiselect>
      </div>
    </div>
    <div class="col-12">
      <button
        type="submit"
        @click.prevent="submit"
        v-if="!form.id"
        class="btn btn-success"
      >
        Enregistrer
      </button>
      <button @click.prevent="update" v-else class="btn btn-success">
        Modifier
      </button>
    </div>
  </form>
  <!-- {{ form }} -->
</template>

<script>
export default {
  props: ["establishments", "activity", "errors"],
  data() {
    return {
      form: {
        name: null,
        sigle: null,
        description: null,
        full_description: null,
        condition: null,
        duration: null,
        photo: null,
        photo_preview: null,
        activity_category_id: null,
        care: false,
        for_kid: false,
        background_color: "#0c5d89",
        font_color: "#FFFFFF",
        activities_for_recuperation_id: []
      },
      imagePreview: null,
      showPreview: false,
      select: {},
      __q: null,
      form_mounted: false
    };
  },
  beforeMount() {
    if (this.activity) {
      var _split = this.activity.duration.split(":");
      this.activity.duration = _split[0] + ":" + _split[1];
      this.form = this.activity;
      this.form.care = this.form.care ? true : false;
      this.imagePreview = this.activity.image
        ? "/" + this.activity.image
        : null;
      this.showPreview = this.activity.image ? true : false;
      this.form.for_kid = this.form.for_kid ? true : false;
    }
  },
  mounted() {
    this.$refs.SelectActivities.refreshOptions(() => {
      if (this.activity.id && this.activity.activities_for_recuperation) {
        this.activity.activities_for_recuperation.map(a => {
          this.$refs.SelectActivities.select(a);
        });
      }
      this.form_mounted = true;
    });
  },
  methods: {
    resetForm() {
      this.form.name = null;
      this.form.sigle = null;
      this.form.description = null;
      this.form.full_description = null;
      this.form.condition = null;
      this.form.duration = null;
      this.form.photo = null;
      this.form.photo_preview = null;
      this.form.activity_category_id = null;
      this.form.care = false;
      this.form.for_kid = false;
      this.form.background_color = "#0c5d89";
      this.form.font_color = "#FFFFFF";
      this.form.activities_for_recuperation_id = "#FFFFFF";
    },
    submit() {
      /*this.$inertia.post(route("activities.store"), this.form, {
        onSuccess: () => {
          this.resetForm();
        },
      });*/
      console.log(this.form);
      this.$inertia.post(
        route("activities.store"),
        {
          name: this.form.name,
          sigle: this.form.sigle,
          description: this.form.description,
          full_description: this.form.full_description,
          condition: this.form.condition,
          duration: this.form.duration,
          photo: this.form.photo,
          activity_category_id: this.form.activity_category_id,
          care: this.form.care,
          for_kid: this.form.for_kid,
          background_color: this.form.background_color,
          font_color: this.form.font_color,
          activities_for_recuperation_id: this.form
            .activities_for_recuperation_id
        },
        {
          forceFormData: true,
          onSuccess: () => {
            this.resetForm();
          }
        }
      );
    },
    update() {
      this.$inertia.post(
        route("activities.update", this.form.id),
        {
          name: this.form.name,
          sigle: this.form.sigle,
          description: this.form.description,
          full_description: this.form.full_description,
          condition: this.form.condition,
          duration: this.form.duration,
          photo: this.form.photo,
          activity_category_id: this.form.activity_category_id,
          care: this.form.care,
          for_kid: this.form.for_kid,
          background_color: this.form.background_color,
          font_color: this.form.font_color,
          activities_for_recuperation_id: this.form
            .activities_for_recuperation_id
        },
        {
          forceFormData: true
        }
      );
    },
    onFileChange(event) {
      this.form.photo = event.target.files[0];
      /*
    Initialize a File Reader object
    */
      let reader = new FileReader();

      /*
          Add an event listener to the reader that when the file
          has been loaded, we flag the show preview as true and set the
          image to be what was read from the reader.
          */
      reader.addEventListener(
        "load",
        function() {
          this.showPreview = true;
          this.imagePreview = reader.result;
        }.bind(this),
        false
      );

      /*
          Check to see if the file is not empty.
          */
      if (this.form.photo) {
        /*
                  Ensure the file is an image file.
              */
        if (/\.(jpe?g|png|gif)$/i.test(this.form.photo.name)) {
          console.log("here");
          /*
                  Fire the readAsDataURL method which will read the file in and
                  upon completion fire a 'load' event which we will listen to and
                  display the image in the preview.
                  */
          reader.readAsDataURL(this.form.photo);
        }
      }
    },
    async getActivities(q) {
      let attr = ["name", "id"];

      if (!q && this.form_mounted) {
        return this.select.activities;
      }

      this.loadActivities = true;
      this.__q = q;

      return axios
        .get(route("api.activities"), {
          params: {
            q: q,
            attr: attr,
            not_id: this.activity?.id
          }
        })
        .then(async response => {
          this.select.activities = response.data.activities;
          this.loadActivities = false;
          return this.select.activities;
        });
    }
  }
};
</script>


