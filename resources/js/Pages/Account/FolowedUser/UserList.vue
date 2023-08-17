<template>
  <div class="user-list" v-if="users.length > 0">
    <div
      class="list-item"
      v-for="(user, index) in users"
      :key="'item-' + index"
    >
      <div class="item--left">
        <div class="user-avatar mr-5">
          <profil-photo :user="user" width="40" :rounded="false" />
        </div>
        <div class="user-infos">
          <div class="mb-2">
            <span class="fs-20 pr-16">{{
              user.name + " " + user.first_name
            }}</span>
          </div>
          <small class="fs-16 fw-300 ls-1">{{ user.email }}</small>
          <div class="user-info-meta">
            <template v-if="user.pivot.accepted && user.pivot.acceptation_date">
              <strong>Suivi depuis : </strong>
              <span>{{ dateDayHFr(user.pivot.acceptation_date) }}</span>
            </template>
            <template
              v-else-if="!user.pivot.accepted && !user.pivot.acceptation_date"
            >
              <strong>Demande envoyer le : </strong>
              <span>{{ dateDayHFr(user.pivot.created_at) }}</span>
            </template>
            <template
              v-else-if="!user.pivot.accepted && user.pivot.acceptation_date"
            >
              <strong>Date de confirmation : </strong>
              <span>{{ dateDayHFr(user.pivot.acceptation_date) }}</span>
            </template>
          </div>
        </div>
      </div>
      <div class="item--right">
        <div class="actions-btn">
          <template v-if="user.pivot.accepted">
            <a
              href="#"
              @click.prevent="switchToUserFollowedAccount(user.pivot.id)"
              type="button"
              class="btn btn-primary btn-block"
            >
              <i class="fa fa-calendar mr-1"></i>
              Voir planning
            </a>
            <a
              href="#"
              @click.prevent="deleteRequest(user.pivot.id)"
              type="button"
              class="btn btn-warning btn-block"
            >
              <i class="fa fa-times mr-2"></i>
              Ne plus suivre
            </a>
          </template>
          <template v-else>
            <a
              href="#"
              type="button"
              class="btn btn-info btn-block disabled"
              disabled
            >
              <i class="fa fa-spinner mr-2"></i>
              Non accepté
            </a>
            <a
              href="#"
              @click.prevent="deleteRequest(user.pivot.id)"
              type="button"
              class="btn btn-danger btn-block"
            >
              <i class="fa fa-times mr-2"></i>
              Supprimer la demande
            </a>
          </template>
        </div>
      </div>
    </div>
  </div>
  <div class="user-list" v-else>
    <p class="text-center p-5 m-5">
      <b>Vous n'avez aucun utilisateur suivi.</b>
    </p>
  </div>
</template>
<script>
import profilPhoto from "@/Pages/Components/profilPhoto.vue";
export default {
  name: "UserList",
  components: {
    profilPhoto
  },
  props: {
    usersFollowings: {
      default: null,
      type: Array
    }
  },
  data: function() {
    return {
      users: []
    };
  },
  mounted: function() {
    this.getUserFollowings();
  },
  methods: {
    getUserFollowings() {
      var params = {};
      params.user_id = this.auth_user.id;
      axios
        .get(route("api.user.followings"), {
          params: params
        })
        .then(response => {
          if (response.data.data != undefined) this.users = response.data.data;
        });
    },
    deleteRequest(request_id) {
      this.$inertia.delete(
        route("following.destroy", {
          following: request_id
        }),
        {
          onBefore: () => confirm("Annuler la demande de suivi?"),
          onSuccess: () => {
            this.getUserFollowings();
          }
        }
      );
    },
    switchToUserFollowedAccount(followed_user_id) {
      axios
        .get(
          route("following.show", {
            following: followed_user_id
          })
        )
        .then(() => {
          window.location = route("dashboard");
        })
        .catch(error => {
          if (error.response.status) {
            toastr.info("Vous n'êtes pas autorisé à consulter son planning");
          }
        });
    }
  }
};
</script>

<style lang="scss" scoped>
.user-list {
  background: #fcfcfc;
}
.list-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  word-wrap: break-word;
  background-color: #fff;
  background-clip: border-box;
  border: 1px solid rgba(0, 0, 0, 0.125);
  border-radius: 0.25rem;
  padding: 2rem;
  margin-top: 2rem;
}
.item--left {
  display: flex;
  align-items: center;
}
</style>
