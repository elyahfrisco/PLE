<template>
  <div class="user-list" v-if="requests.length > 0">
    <div
      class="list-item row"
      v-for="(request, index) in requests"
      :key="'item-' + index"
    >
      <div class="item--left row col-md-8 col-lg-10">
        <div class="user-avatar col-md-2">
          <profil-photo :user="request.follower" width="40" :rounded="false" />
        </div>
        <div class="user-infos col-md-5">
          <p class="mb-1"><u>Le client :</u></p>
          <div class="mb-2">
            <span class="fs-20 pr-16">{{
              request.follower.name + " " + request.follower.first_name
            }}</span>
          </div>
        </div>
        <div class="user-infos col-md-5">
          <p class="mb-1"><u>A demandé de suivre :</u></p>
          <div class="mb-2">
            <span class="fs-20 pr-16">{{
              request.following.name + " " + request.following.first_name
            }}</span>
          </div>
          <small class="fs-16 fw-300 ls-1">{{ request.following.email }}</small>
          <div class="user-info-meta">
            <template v-if="!request.accepted">
              <strong>Demade envoyer le : </strong>
              <span>{{ dateHFr(request.created_at) }}</span>
            </template>
            <template v-if="request.acceptation_date">
              <strong>Date de confirmation : </strong>
              <span>{{ dateHFr(request.acceptation_date) }}</span>
            </template>
          </div>
        </div>
      </div>
      <div class="item--right col-md-4 col-lg-2">
        <div class="actions-btn">
          <template v-if="!request.accepted">
            <a
              v-if="request.acceptation_date"
              href="#"
              type="button"
              class="btn btn-info btn-block disabled"
            >
              <i class="fa fa-times mr-2"></i>
              Refusé
            </a>
            <a
              href="#"
              @click.prevent="confirmRequest(request.id, true)"
              type="button"
              class="btn btn-primary btn-block"
            >
              <i class="fa fa-calendar mr-1"></i>
              Accepter
            </a>
            <a
              v-if="!request.acceptation_date"
              href="#"
              type="button"
              @click.prevent="confirmRequest(request.id, false)"
              class="btn btn-danger btn-block"
            >
              <i class="fa fa-times mr-2"></i>
              Refuser
            </a>
          </template>
          <template v-else>
            <a
              href="#"
              type="button"
              @click.prevent="confirmRequest(request.id, false)"
              class="btn btn-warning btn-block"
            >
              <i class="fa fa-times mr-2"></i>
              Annuler le suivi
            </a>
            <a
              href="#"
              type="button"
              @click.prevent="deleteRequest(request.id)"
              class="btn btn-primary btn-block"
            >
              <i class="fa fa-trash mr-1"></i>
              Supprimer
            </a>
          </template>
        </div>
      </div>
    </div>
    <paginator :data="paginatorData" />
  </div>
  <div class="user-list" v-else>
    <p class="text-center p-5 m-5">
      <b>Aucune demande de suivi</b>
    </p>
  </div>
</template>
<script>
import profilPhoto from "@/Pages/Components/profilPhoto.vue";
import Paginator from "@/Pages/Components/Paginator.vue";
export default {
  name: "UserList",
  components: {
    profilPhoto,
    Paginator
  },
  props: {
    usersFollowings: {
      default: null,
      type: Array
    }
  },
  data: function() {
    return {
      requests: [],
      paginatorData: []
    };
  },
  mounted: function() {
    this.getUserFollowings();
  },
  methods: {
    getUserFollowings() {
      var params = {};
      params.requests_list = true;

      axios
        .get(route("api.user.followings"), {
          params: params
        })
        .then(response => {
          if (response.data.data != undefined)
            this.requests = response.data.data;
          this.paginatorData = response.data.meta;
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
    confirmRequest(request_id, status) {
      this.$inertia.put(
        route("following.update", {
          following: request_id,
          accepte: status
        }),
        {},
        {
          onBefore: () => {
            if (status == 1) {
              return confirm("Accepter la demande de suivi?");
            } else {
              return confirm("Refuser la demande de suivi?");
            }
          },
          onSuccess: () => {
            this.getUserFollowings();
          }
        }
      );
    }
  }
};
</script>

<style lang="scss" scoped>
.user-avatar {
  overflow-x: hidden;
}
.user-list {
  background: #fcfcfc;
}
.list-item {
  word-wrap: break-word;
  background-color: #fff;
  background-clip: border-box;
  border: 1px solid rgba(0, 0, 0, 0.125);
  border-radius: 0.25rem;
  padding: 2rem;
  margin-top: 2rem;
}
</style>
