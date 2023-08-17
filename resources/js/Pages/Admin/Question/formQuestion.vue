<template>
  <form class="p-5">
    <div class="row">
      <div class="col-md-6">
        <div class="form-group">
          <label>Question</label>
          <input
            type="text"
            class="form-control"
            placeholder=""
            v-model.lazy="form.content"
          />
          <small class="validation-error" v-if="errors.content">{{
            errors.content
          }}</small>
        </div>

        <div class="form-group">
          <label>Réponse</label>
          <div class="">
            <div class="icheck-success d-inline">
              <input
                type="radio"
                value="single"
                id="single"
                required
                v-model="form.type"
              />
              <label for="single">Unique</label>
            </div>
            <div class="icheck-success d-inline ml-4">
              <input
                type="radio"
                value="multiple"
                id="multiple"
                required
                v-model="form.type"
              />
              <label for="multiple">Multiple</label>
            </div>
          </div>
          <small class="validation-error block" v-if="errors.type">{{
            errors.type
          }}</small>
        </div>

        <div class="form-group">
          <div class="icheck-primary icheck-sm d-inline">
            <input
              type="checkbox"
              v-model="form.other_response"
              id="other_response"
            />
            <label for="other_response">Autoriser d'autre réponse</label>
          </div>
          <small class="validation-error" v-if="errors.other_response">{{
            errors.other_response
          }}</small>
        </div>

        <div v-if="form.other_response" class="form-group">
          <label>Texte d'indice pour l'autre réponse</label>
          <input
            type="text"
            class="form-control"
            placeholder=""
            v-model.lazy="form.other_response_placeholder"
          />
          <small
            class="validation-error"
            v-if="errors.other_response_placeholder"
            >{{ errors.other_response_placeholder }}</small
          >
        </div>
      </div>
      <div class="col-md-6">
        <h4>Réponses</h4>

        <div class="mb-2">
          <div
            v-if="form.answers.length"
            v-for="(answer, i_answer) in form.answers"
            :key="i_answer"
          >
            <form-answer
              v-if="!answer.deleted"
              v-model:content="form.answers[i_answer].content"
              :error="errors['answers.' + i_answer + '.content']"
              @delete-answer="() => deleteAnswer(i_answer)"
            />
          </div>
          <p v-else class="alert alert-info">
            Ajoutez des réponses possibles pour la question, si aucune réponse
            n'est ajoutée, vous devez autoriser une autre réponse en cochant la
            case à cocher
          </p>
          <small
            class="validation-error"
            v-if="!form.answers.length && errors.answers"
            >{{ errors.answers }}</small
          >
          <div class="text-right">
            <button
              type="button"
              @click.prevent="addAnswer"
              class="btn btn-warning btn-sm"
            >
              <i class="fa fa-plus"></i> Réponse
            </button>
          </div>
        </div>

        <button
          type="submit"
          @click.prevent="update"
          v-if="form.id"
          class="btn btn-success"
        >
          Enregistrer la Modification
        </button>
        <button
          type="submit"
          @click.prevent="submit"
          v-else
          class="btn btn-success"
        >
          Enregistrer
        </button>
      </div>
    </div>
  </form>
  <div class="rounded p-3 border col-md-6">
    <h3>Aperçu</h3>
    <p class="bolder">{{ form.content }}</p>
    <ul class="list-group list-group-flush">
      <li
        v-for="(answer, i_answer_preview) in form.answers"
        :key="i_answer_preview"
        class="list-group-item py-1 px-2"
      >
        <div
          class="custom-control"
          :class="{
            'custom-checkbox': form.type === 'multiple',
            'custom-radio': form.type === 'single'
          }"
        >
          <input
            :type="form.type === 'multiple' ? 'checkbox' : 'radio'"
            :name="
              form.type === 'multiple' ? 'answer_' + i_answer_preview : 'answer'
            "
            class="custom-control-input"
            :id="'answer_' + i_answer_preview"
          />
          <label
            class="custom-control-label"
            :for="'answer_' + i_answer_preview"
            >{{ answer.content }}</label
          >
        </div>
      </li>
    </ul>

    <input
      v-if="form.other_response"
      type="text"
      class="form-control mt-3"
      :placeholder="form.other_response_placeholder"
    />
  </div>
</template>

<script>
import formAnswer from "./formAnswer.vue";
export default {
  components: {
    formAnswer
  },
  props: ["question"],
  data() {
    return {
      form: {
        content: "",
        type: "single",
        other_response: false,
        other_response_placeholder: null,
        answers: []
      }
    };
  },
  mounted() {
    if (this.question?.id) {
      this.form = this.question;
      this.form.other_response = this.form.other_response ? true : false;
    }
  },
  methods: {
    submit() {
      this.$inertia.post(route("questions.store"), this.form, {
        onSuccess: () => {
          this.form = {
            content: "",
            type: "single",
            other_response: false,
            other_response_placeholder: null,
            answers: []
          };
        },
        onError: () => {
          if (this.errors.other_response_placeholder)
            this.form.other_response = true;
        }
      });
    },
    update() {
      this.$inertia.put(route("questions.update", this.form.id), this.form, {
        onSuccess: () => {
          this.iReload();
        },
        onError: () => {
          if (this.errors.other_response_placeholder)
            this.form.other_response = true;
        }
      });
    },
    addAnswer() {
      this.form.answers.push({});
    },
    deleteAnswer(i_answer) {
      if (
        this.form.answers[i_answer].id &&
        !confirm("Voulez-vous vraiment supprimer cette réponse ?")
      )
        return;
      this.form.answers[i_answer].id
        ? (this.form.answers[i_answer].deleted = true)
        : this.form.answers.splice(i_answer, 1);
    }
  }
};
</script>


