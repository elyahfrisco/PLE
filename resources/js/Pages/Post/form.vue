<template>
  <form @submit.prevent="submit">
    <div class="row">
      <div class="col-md-10">
        <div class="form-group">
          <label>Titre</label>
          <input
            type="text"
            class="form-control"
            placeholder=""
            v-model="form.title"
          />
          <small class="validation-error" v-if="errors.title">{{
            errors.title
          }}</small>
        </div>
      </div>
      <div class="col-md-2">
        <div class="form-group">
          <label class="">Categorie</label>
          <Multiselect
            v-model="form.post_category_id"
            placeholder="--Categorie d'article--"
            :options="$page.props.post_category"
            label="name"
            valueProp="id"
            :canDeselect="false"
            required
          />
          <small class="validation-error" v-if="errors.post_category_id">{{
            errors.post_category_id
          }}</small>
        </div>
      </div>
      <!-- <div class="col-md-2">
        <div class="form-group">
          <label class="">Image</label>
          <div class="input-group">
            <div class="custom-file">
              <input
                type="file"
                class="custom-file-input"
                accept="image/*"
                @change="getFile"
                id="inputPhoto"
                required
              />
              <label class="custom-file-label" for="inputPhoto">
                <span v-if="!form.cover_photo"> Selectionnez une photo </span>
                <span v-else v-text="form.cover_photo.name" />
              </label>
            </div>
          </div>
        </div>
      </div> -->
    </div>

    <div class="form-group">
      <quill-editor
        v-model:value="form.content"
        :options="{
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
      <small
        class="validation-error"
        v-if="
          form.content == null || form.content.length == 0 || errors.content
        "
        >* Le contenu est requis</small
      >
    </div>

    <button
      type="submit"
      @click.prevent="submit"
      v-if="!form.id"
      class="btn btn-success"
      :disabled="!can_submit"
      :class="{ disabled: !can_submit }"
    >
      Enregistrer
    </button>
    <button
      type="submit"
      @click.prevent="update"
      v-else
      class="btn btn-success"
    >
      Modifier
    </button>
  </form>
  <!-- {{ form }} -->
</template>

<script>
export default {
  props: ["post"],
  data() {
    return {
      form: this.$inertia.form({
        title: "",
        content: "",
        post_category_id: null,
        cover_photo: null
      })
    };
  },
  beforeMount() {
    if (this.post) {
      this.form = this.$inertia.form({
        id: this.post.id,
        title: this.post.title,
        content: this.post.content,
        cover_photo: null,
        cover_photo_path: this.post.cover_photo_path,
        post_category_id: this.post.post_category_id
      });
    } else {
      this.form.post_category_id = this.$page.props.post_category[0]?.id;
    }
  },
  methods: {
    getFile(e) {
      this.form.cover_photo = e.target.files[0]
        ? e.target.files[0]
        : this.form.cover_photo;
    },
    submit() {
      this.form.id ? this.update() : this.store();
    },
    store() {
      this.form.post(this.route("posts.store"), {
        onSuccess: () => this.form.reset()
      });
    },
    update() {
      this.form.put(this.route("posts.update", this.form.id), {
        replace: true
      });
    }
  },
  computed: {
    can_submit() {
      return this.form?.title != "" && this.form?.content != "";
    }
  }
};
</script>
