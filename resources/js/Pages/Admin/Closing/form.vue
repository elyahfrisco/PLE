<template>
  <form>
    <div class="form-group">
      <input type="checkbox" class="form-checkbox" v-model="oneDay" />
      <span class="ml-2">Fermé en une journée</span>
    </div>

    <div class="form-group">
      <span> Date de fermeture </span>

      <datepicker v-model="form.date_start" class="form-control" />

      <small class="validation-error" v-if="errors.date_start">{{
        errors.date_start
      }}</small>
    </div>

    <div class="form-group" v-if="!oneDay">
      <span>Date de réouverture</span>

      <datepicker v-model="form.date_end" class="form-control" />

      <small class="validation-error" v-if="errors.date_end">{{
        errors.date_end
      }}</small>
    </div>

    <div class="form-group">
      <span>Motif</span>
      <textarea
        type="date"
        class="form-control"
        placeholder=""
        v-model="form.reason"
      ></textarea>
      <small class="validation-error" v-if="errors.reason">{{
        errors.reason
      }}</small>
    </div>

    <button class="btn btn-success" v-if="form.id" @click.prevent="update">
      Metre à jour
    </button>
    <button v-else class="btn btn-success" @click.prevent="store">
      Enregistrer
    </button>
  </form>
</template>

<script>
export default {
  props: ["closing", "establishment_id", "establishments_list"],
  data() {
    return {
      form: {
        date_start: null,
        date_end: null,
        reason: null
      },
      oneDay: true
    };
  },
  watch: {
    closing: {
      deep: true,
      handler() {
        console.log("call");
        this.initForm();
      }
    }
  },
  beforeMount() {
    this.initForm();
  },
  computed: {
    establishments() {
      return this.$page.props.establishments_list;
    }
  },
  methods: {
    initForm() {
      if (this.closing) {
        this.form = { ...this.closing };
        this.form.date_start = dt(this.form.date_start);
        this.form.date_end = dt(this.form.date_end);
        this.oneDay =
          this.dateAng(this.form.date_start) ===
          this.dateAng(this.form.date_end);
        console.log("ee");
      }
    },
    cancelEdit() {
      this.form = { ...this.closing };
      $("#addClosing").modal("toggle");
    },
    update() {
      this.$inertia.put(
        route("closings.update", this.form.id),
        {
          date_start: this.dateAng(this.form.date_start),
          date_end: this.dateAng(this.form.date_end),
          reason: this.form.reason,
          oneDay: this.oneDay
        },
        {
          onSuccess: () => {
            $("#editClosing").modal("toggle");
          }
        }
      );
    },
    store() {
      this.$inertia.post(
        route("closings.store"),
        {
          date_start: this.dateAng(this.form.date_start),
          date_end: this.dateAng(this.form.date_end),
          reason: this.form.reason,
          oneDay: this.oneDay,
          establishment_id: this.establishment_id
        },
        {
          onSuccess: () => {
            this.closingDataAdded();
          }
        }
      );
    },
    closingDataAdded() {
      this.form.date_start = null;
      this.form.date_end = null;
      this.form.reason = null;
      $("#addClosing").modal("toggle");
    }
  }
};
</script>


