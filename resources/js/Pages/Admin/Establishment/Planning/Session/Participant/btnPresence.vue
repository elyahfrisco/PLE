<template>
  <div class="form-group mb-1">
    <div class="">
      <div class="icheck-success d-inline">
        <input
          type="radio"
          value="1"
          :id="'present' + session.id"
          required
          v-model="session.accomplished"
          @click="setPresence"
        />
        <label :for="'present' + session.id">Present</label>
      </div>
      <div class="icheck-danger d-inline ml-4">
        <input
          type="radio"
          value="0"
          :id="'absent' + session.id"
          required
          v-model="session.accomplished"
          @click="setPresence"
        />
        <label :for="'absent' + session.id">Absent</label>
      </div>
      <div class="icheck-secondary d-inline ml-4">
        <input
          type="radio"
          :value="null"
          :id="'null' + session.id"
          v-model="session.accomplished"
          @click="setPresence"
        />
        <label :for="'null' + session.id"></label>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["user_session"],
  data() {
    return {
      session: null
    };
  },
  beforeMount() {
    this.session = this.user_session;
  },
  methods: {
    setPresence() {
      setTimeout(() => {
        axios
          .post(
            route("user.sessions.presence", {
              session_id: this.session.user_session_id
            }),
            {
              accomplished: this.session.accomplished
            }
          )
          .then(() => {
            var name =
              this.session.first_name.toUpperCase() + " " + this.session.name;
            if (this.session.accomplished == 1) {
              toastr.info(name + ": PRESENT");
            } else if (this.session.accomplished === null) {
              toastr.error("Status presence " + name + ": ANNULÉ");
            } else {
              toastr.warning(name + ": ABSENT");
            }
            this.$emit("refresh");
          });
      }, 200);
    }
  }
};
</script>


