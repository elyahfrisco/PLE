<template>
  <jet-modal :id="modalId" maxWidth="md" title="Selectionner l'enseignant">
    <div class="form-group">
      <label class="">Coach</label>
      <Multiselect
        v-model="form.coachs_id"
        placeholder="Saisissez le nom de l'enseignant"
        :options="coachs"
        :searchable="true"
        :minChars="3"
        :resolveOnLoad="false"
        :maxHeight="300"
        mode="tags"
        noresults="Aucun enseignant ne correspond à votre recherche"
        required
      >
        <template v-slot:option="{ option }">
          <profil-photo
            class="character-option-icon"
            :user="option"
            :width="25"
            :rounded="false"
          ></profil-photo>
          <span class="ml-2">{{ option.label }}</span>
        </template>
      </Multiselect>
      <small class="validation-error" v-if="errors.coachs_id">{{
        errors.coachs_id
      }}</small>
    </div>
    <div class="row">
      <inertia-link
        v-if="session.id"
        class="btn btn-success mx-auto"
        method="POST"
        :href="
          route('session.coachs.assign', {
            session_id: session.id,
            coachs_id: form.coachs_id
          })
        "
        preserve-scroll
        :onSuccess="() => $emit('coachAssigned', session.id)"
        >Assigner</inertia-link
      >
    </div>
  </jet-modal>
</template>

<script>
import profilPhoto from "@/Pages/Components/profilPhoto.vue";
export default {
  components: {
    profilPhoto
  },
  props: {
    session: {
      default: {}
    },
    modalId: {
      default: "select-coach",
      type: String
    }
  },
  watch: {
    session: {
      deep: true,
      handler() {
        setTimeout(() => {
          this.form.coachs_id = [];
          if (this.session) {
            for (let coach of this.session.coachs) {
              this.form.coachs_id.push(coach.id);
            }
          }
        }, 100);
      }
    }
  },
  data() {
    return {
      form: {
        coachs_id: null
      },
      query: null,
      coachs: null
    };
  },
  mounted() {
    this.getCoachs();
  },
  methods: {
    setQuery(query) {
      this.query = query;
      this.getCoachs();
    },
    getCoachs() {
      axios
        .get(
          route("api.user.search", {
            role: "coach",
            q: this.query
          })
        )
        .then(response => {
          this.coachs = this.toSelect(response.data.data, "id", "fullname");
        });
    }
  }
};
</script>


