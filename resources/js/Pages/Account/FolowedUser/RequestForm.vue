<template>
  <div class="btn-request-wrapper mb-4 text-right" v-if="!showRequestForm">
    <button class="btn btn-primary" @click="showRequestForm = true">
      Envoyer une demande de suivi
    </button>
    <loading
      :active="true"
      :can-cancel="false"
      :is-full-page="true"
      loader="dots"
      color="#1d9cc4"
      blur="50px"
    />
  </div>
  <div v-else class="request-form">
    <div class="container-fluid container-limited">
      <div class="form-group">
        <label> Nom ou email du client * </label>
        <div class="user-search--input">
          <input
            type="text"
            class="form-control"
            v-model="q"
            @keyup="onSearchKeyUp"
          />
          <div class="result-search--container" v-if="resultSearch">
            <div
              class="user-item"
              v-for="(user, index) in resultSearch"
              :key="'user-' + index"
              @click="selectUser(user)"
            >
              <div class="user-infos">
                <strong class="mr-2">{{ user.name }}</strong>
                <small
                  >( <i class="fa fa-envelope"></i> {{ user.email }} )</small
                >
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label>Info supplémentaire</label>
        <textarea name="info-sup" class="form-control" v-model="infos" />
      </div>
      <div class="form-group">
        <button class="btn btn-primary mr-2" @click="hideRequestForm()">
          Annuler
        </button>
        <button class="btn btn-primary" @click="onSubmitRequest()">
          Soumettre la demande
        </button>
      </div>
    </div>
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "RequestForm",
  data: function() {
    return {
      q: "",
      resultSearch: false,
      selectedUser: null,
      infos: null,
      showRequestForm: false
    };
  },
  methods: {
    hideRequestForm() {
      this.selectedUser = false;
      this.q = "";
      this.infos = null;
      this.showRequestForm = false;
    },
    selectUser(user) {
      this.selectedUser = user;
      this.q = user.name;
      this.resultSearch = false;
    },
    onSearchKeyUp() {
      if (this.q.length < 1) {
        this.resultSearch = false;
        return;
      }
      let that = this;
      axios
        .get(route("api.user.search"), {
          params: {
            q: this.q,
            not_followed_by_user_id: this.auth_user.id
          }
        })
        .then(response => {
          that.resultSearch = response.data.data;
        });
    },
    onSubmitRequest() {
      /*** check selected user */
      if (!this.selectedUser) {
        toastr.warning("Utilisateur invalid");
        return false;
      }

      axios
        .post(route("following.store"), {
          user_follower_id: this.auth_user.id,
          user_following_id: this.selectedUser.id,
          infos: this.infos
        })
        .then(response => {
          toastr.success("Demande envoyée avec success");
          this.hideRequestForm();
          this.iReload(this);
        });
    }
  },
  watch: {}
};
</script>

<style scoped>
.request-form {
  padding: 2rem;
  border: solid 1px #dee2e6;
  border-radius: 5px;
}
.user-search--input {
  position: relative;
}
.result-search--container {
  position: absolute;
  background: white;
  width: 100%;
  background-color: #fff;
  background-clip: padding-box;
  border: 1px solid #ced4da;
  padding: 1rem;
  border-top: none;
  margin-top: -1px;
}
.user-item {
  border-top: 1px solid #ced4da;
  padding: 10px 0;
  cursor: pointer;
}
.user-item:hover {
  background: #e6e6e6;
  padding-left: 5px;
}
</style>
