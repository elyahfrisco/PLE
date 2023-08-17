<template>
  <form @submit.prevent="submitRelaunch()">
    <div class="row mt-3">
      <div class="col-md-10 offset-md-2">
        <div class="row">
          <div class="icheck-primary icheck-sm col-sm-6">
            <input
              type="checkbox"
              v-model="form.save_like_a_template_only"
              id="save_like_a_template_only"
            />
            <label for="save_like_a_template_only"
              >Enregistrer en tant que modèle seulement?</label
            >
          </div>
          <div class="col-sm-6 text-sm-right">
            <a
              class="btn btn-primary btn-sm"
              data-toggle="collapse"
              href="#mail_template"
              role="button"
              aria-expanded="false"
              aria-controls="mail_template"
              >Afficher les modèles</a
            >
          </div>
        </div>
        <div
          class="row collapse multi-collapse border py-3 mt-2"
          id="mail_template"
        >
          <div
            class="col-md-4"
            v-for="email_template in email_templates"
            :key="email_template.id"
          >
            <mail-template
              :email_template="email_template"
              @select_template="setTemplateContent"
              @refresh_template_list="getMailTemplates"
            />
          </div>
        </div>
      </div>
    </div>
    <div class="row mt-3">
      <div class="col-lg-10">
        <input
          type="text"
          class="form-control mb-3"
          placeholder="Objet"
          v-model="form.subject"
          required
        />

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
          v-if="form.content == null || form.content.length == 0"
          >* Le contenu est requis</small
        >
      </div>

      <div class="col-lg-2 order-md-first">
        <div class="form-group">
          <div class="icheck-primary icheck-sm d-inline">
            <input
              type="checkbox"
              v-model="form.sendDirectly"
              id="sendDirectly"
              :class="{
                'disabled muted': form.save_like_a_template_only
              }"
              :disabled="form.save_like_a_template_only"
            />
            <label for="sendDirectly">Envoyer aprés l'enregistrement</label>
          </div>
        </div>
        <div class="form-group" v-if="form.sendDirectly !== true">
          <label class="ml-3">ou</label>
          <label class="d-block">Date/heure d'envoi</label>
          <div class="row">
            <div class="col-sm-7">
              <input
                type="date"
                class="form-control"
                placeholder=""
                v-model="form.date_relaunch"
                required
                :class="{
                  'disabled muted': form.save_like_a_template_only
                }"
                :disabled="form.save_like_a_template_only"
              />
            </div>
            <div class="col-sm-5">
              <input
                type="time"
                class="form-control"
                placeholder=""
                v-model="form.time_relaunch"
                required
                :class="{
                  'disabled muted': form.save_like_a_template_only
                }"
                :disabled="form.save_like_a_template_only"
              />
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-10 offset-md-2 my-2">
        <label class="d-block">Type d'utilisateur</label>
        <div class="form-group">
          <div class="icheck-primary icheck-sm d-inline mr-2">
            <input
              type="checkbox"
              value="prospect"
              v-model="form.user_type"
              id="t_prospect"
              :disabled="
                (form.users_id && form.users_id.length != 0) ||
                  form.save_like_a_template_only
              "
              :class="{
                disabled:
                  (form.users_id && form.users_id.length != 0) ||
                  form.save_like_a_template_only
              }"
            />
            <label for="t_prospect">Prospect</label>
          </div>

          <div class="icheck-primary icheck-sm d-inline mr-2">
            <input
              type="checkbox"
              value="customer"
              v-model="form.user_type"
              id="t_customer"
              :disabled="
                (form.users_id && form.users_id.length != 0) ||
                  form.save_like_a_template_only
              "
              :class="{
                disabled:
                  (form.users_id && form.users_id.length != 0) ||
                  form.save_like_a_template_only
              }"
            />
            <label for="t_customer">Client</label>
          </div>

          <div class="icheck-primary icheck-sm d-inline mr-2">
            <input
              type="checkbox"
              value="coach"
              v-model="form.user_type"
              id="t_coach"
              :disabled="
                (form.users_id && form.users_id.length != 0) ||
                  form.save_like_a_template_only
              "
              :class="{
                disabled:
                  (form.users_id && form.users_id.length != 0) ||
                  form.save_like_a_template_only
              }"
            />
            <label for="t_coach">Enseignant</label>
          </div>
        </div>
        <div class="form-group row">
          <button
            class="btn btn-sm btn-success px-2"
            data-toggle="tooltip"
            title="Selectionner tout"
            @click.prevent="selectAll"
            :disabled="
              !(form.users_id && users && form.users_id.length != users.length)
            "
            :class="{
              disabled: !(
                form.users_id &&
                users &&
                form.users_id.length != users.length
              )
            }"
          >
            <i class="fa fa-check-double"></i>
          </button>
          <Multiselect
            v-model="form.users_id"
            class="col px-1"
            placeholder="Sélectionnez les destinataires"
            :options="
              async function(query) {
                return await getUsers(query);
              }
            "
            :searchable="true"
            :filterResults="false"
            :resolveOnLoad="false"
            delay="0"
            :minChars="3"
            :loading="loadUsers"
            mode="tags"
            trackBy="full_name"
            label="full_name"
            valueProp="id"
            ref="selectUser"
            noresults="Aucun utilisateur ne correspond à votre recherche"
            :required="!form.save_like_a_template_only"
            :disabled="form.save_like_a_template_only"
            :class="{
              disabled: form.save_like_a_template_only
            }"
          >
            <template v-slot:option="{ option }">
              <profil-photo
                class="character-option-icon"
                :user="option"
                :width="25"
                :rounded="false"
              ></profil-photo>
              <span class="ml-2">{{ option.full_name }}</span>
            </template>
          </Multiselect>
        </div>
      </div>
      <div class="col-md-10 offset-md-2 mt-2 row">
        <div class="icheck-primary icheck-sm col px-md-0 px-3">
          <input
            type="checkbox"
            v-model="form.save_like_a_template"
            id="save_like_a_template"
            :class="{
              'disabled muted': form.save_like_a_template_only
            }"
            :disabled="form.save_like_a_template_only"
          />
          <label for="save_like_a_template"
            >Enregistrer en tant que modèle aussi?</label
          >
        </div>
      </div>
      <div class="col-12 text-center mt-2">
        <button
          class="btn btn-success mx-auto"
          :class="{ disabled: !canSubmit }"
          :disabled="!canSubmit"
        >
          Enregistrer
        </button>
      </div>
    </div>
  </form>
</template>

<script>
import profilPhoto from "@/Pages/Components/profilPhoto.vue";
import MailTemplate from "./MailTemplate.vue";

export default {
  components: {
    profilPhoto,
    MailTemplate
  },
  data() {
    return {
      form: {
        subject: "",
        content: "",
        sendDirectly: false,
        date_relaunch: null,
        users_id: null,
        user_type: ["customer"],
        save_like_a_template: false,
        save_like_a_template_only: false
      },
      users: [],
      email_templates: [],
      loadUsers: false,
      loadingMailTemplate: false
    };
  },
  methods: {
    submitRelaunch() {
      this.$inertia.post(route("relaunchs.store"), this.form, {
        onSuccess: () => {
          this.iReload();
        }
      });
    },
    selectAll() {
      var ids = [];
      for (var user of this.users) {
        ids.push(user.id);
      }
      this.form.users_id = ids;
      this.clearTooltip();
    },
    getUsers(q = null) {
      let attr = ["name", "first_name", "email"];

      if (!q) {
        return this.users;
      }

      this.loadUsers = true;

      return axios
        .get(route("api.user.search"), {
          params: {
            q: q,
            attr: attr,
            role: this.form.user_type
          }
        })
        .then(response => {
          this.users = response.data.data;
          this.loadUsers = false;
          return this.users;
        });
    },

    getMailTemplates() {
      this.loadingMailTemplate = true;
      this.users_id = null;
      axios.get(route("api.mail.templates.list")).then(response => {
        this.email_templates = response.data.data;
        this.loadingMailTemplate = false;
      });
    },

    setTemplateContent(email_template) {
      this.form.content = email_template.content;
      this.form.subject = email_template.title;
      $("#mail_template").collapse("hide");
    }
  },
  watch: {
    "form.user_type": {
      handler() {
        setTimeout(() => {
          if (this.form.user_type.length == 0) {
            this.form.user_type = ["customer"];
          }
        }, 100);
      },
      deep: true
    }
  },
  mounted() {
    this.getMailTemplates();
    this.initTooltipe();
  },
  computed: {
    canSubmit() {
      return (
        this.form.content &&
        (this.form.users_id || this.form.save_like_a_template_only) &&
        this.form.subject
      );
    }
  }
};
</script>


