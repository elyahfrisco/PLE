<template>
  <jet-modal
    :id="'modal-detail-subscription' + subscriptionData.id"
    maxWidth="xl"
    title="Detail de la souscriptions"
  >
    <div class="row">
      <div class="ml-auto">
        <button
          href="#"
          class="btn btn-danger btn-sm btn-action"
          data-toggle="tooltip"
          title="Annuler la souscription"
          @click.prevent="deleteSubscription(subscriptionData)"
        >
          <i class="fa fa-trash"></i>
        </button>
      </div>
    </div>
    <div class="row">
      <div class="col-md-4">
        <p>Client : {{ subscriptionData.customer.full_name }}</p>
        <p>Centre : {{ subscriptionData.establishment.name }}</p>
        <p>
          Saison :
          <template v-if="subscriptionData.season">
            {{ subscriptionData.season.year_start }} -
            {{ subscriptionData.season.year_end }}
          </template>
        </p>
        <p v-if="subscriptionData.num_trimester">
          Trimestre : {{ subscriptionData.num_trimester }}
        </p>
      </div>
      <div class="col-md-4">
        <p>Pass : {{ subscriptionData.pass.name }}</p>
        <p>
          Date de souscription :
          {{ dateFr(subscriptionData.created_at) }}
        </p>
        <p>
          Debut activité :
          {{ dateHFr(subscriptionData.date?.start) }} (
          {{ subscriptionData.date?.elapsetime_start }} )
        </p>
        <p>
          Fin activité : {{ dateHFr(subscriptionData.date?.end) }} (
          {{ subscriptionData.date?.elapsetime_end }} )
        </p>
        <p v-if="!!subscriptionData.expired_at">
          Date fin :
          {{ dateFr(subscriptionData.expired_at) }}
        </p>
      </div>
      <div class="col-md-4">
        <template v-if="subscriptionData.pass.pass_trimester">
          <p>
            Rénouvellement :
            <span
              v-if="subscriptionData.renewal?.renewal_status"
              class="badge mr-1 text-left"
              :class="{
                'badge-danger':
                  subscriptionData.renewal?.renewal_status === 'stop',
                'badge-success':
                  subscriptionData.renewal?.renewal_status !== 'stop'
              }"
              >{{ subscriptionData.renewal?.status_fr }}
              <template v-if="subscriptionData.renewal_subscription_id">
                <br />, souscription effectué
              </template>
            </span>

            <template
              v-if="
                subscriptionData && !subscriptionData.renewal_subscription_id
              "
            >
              <btn-renewal
                :subscription="subscriptionData"
                @renewal_saved="refresh"
              />
              <inertia-link
                v-if="
                  subscriptionData.customer.status !== 'old_customer' &&
                    $can('create_subscription_fro_renewal') &&
                    subscriptionData.renewal &&
                    subscriptionData.renewal.renewal_status === 'continue'
                "
                :href="
                  route('subscriptions.create', {
                    user_id: subscriptionData.user_id,
                    renewal_id: subscriptionData.renewal?.id,
                    season_id: subscriptionData.renewal?.season_id,
                    establishment_id:
                      subscriptionData.renewal?.establishment_id,
                    num_trimester: subscriptionData.renewal?.num_trimester,
                    pass_id: subscriptionData.pass_id,
                    pass_type: 'trimester',
                    subscription_id: subscriptionData.id
                  })
                "
                class="btn btn-outline-success btn-action btn-sm py-0 px-1 ml-1"
                :title="
                  'Nouvelle souscription pour le renouvellement de ' +
                    subscriptionData.customer.full_name
                "
                data-toggle="tooltip"
                ><i class="fa fa-plus"></i
              ></inertia-link>
            </template>
          </p>
        </template>
        <p>
          Réglement :
          <span class="badge badge-success ml-2" v-if="subscriptionData.payment"
            >réglé</span
          >
          <span class="badge badge-danger ml-2" v-else>non réglé</span>
        </p>
        <p>Status : <status-badge :status="subscriptionData.status" /></p>
        <p v-if="subscriptionData.pass.PassCategory == 'other'">
          Séances reservés :
          <span
            :class="{
              'badge badge-info':
                subscriptionData.activities.length <
                subscriptionData.number_of_sessions
            }"
          >
            {{ subscriptionData.activities.length }} /
            {{ subscriptionData.number_of_sessions }}
          </span>
        </p>
        <p>
          Nombre d'absences :
          {{ subscriptionData.number_absence_prevention }}
        </p>
        <p>
          Nombre de récupérations :
          {{ subscriptionData.number_recuperation }}
        </p>
      </div>
    </div>

    <div class="row">
      <div :class="[$can('comment_subscription', 'col-md-7', 'col-12')]">
        <detail-subscription-table
          :subscription="subscriptionData"
          @event2="getComments"
        />
      </div>
      <div v-if="$can('comment_subscription')" class="col-md-5">
        <subscription-comment-form
          :user_subscription_id="subscriptionData.id"
        />
        <div
          v-if="!loadComments && comments.length > 0"
          class="subscription-comments"
        >
          <template v-for="(comment, iSC) in comments" :key="comment.id">
            <div class="subscription-comment mt-2 p-1">
              <template v-if="commentIdToEdit != comment.id">
                <div v-html="comment.content" class="ck-content"></div>
                <div
                  v-if="comment.id"
                  class="d-flex justify-content-between align-items-end"
                >
                  <small>{{ comment.created_at_fr }}</small>
                  <btn-edit
                    type="button"
                    @click.prevent="commentIdToEdit = comment.id"
                    >Modifier</btn-edit
                  >
                </div>
              </template>
              <subscription-comment-form
                v-else
                :comment="comment"
                v-model:comment="comments[iSC]"
                :user_subscription_id="subscriptionData.id"
              />
            </div>
          </template>
        </div>

        <div
          v-show="loadComments"
          class="skeleton skeleton-line w-100"
          style="--lines: 2; --l-h: 50px; --l-gap: 8px"
        ></div>
      </div>
    </div>
  </jet-modal>
</template>

<script>
import DetailSubscriptionTable from "@/Pages/Components/Subscription/DetailSubscriptionTable";
import SubscriptionCommentForm from "./SubscriptionCommentForm.vue";
import BtnRenewal from "@/Pages/Components/Renewal/BtnRenewal.vue";
export default {
  props: ["subscription"],
  components: {
    DetailSubscriptionTable,
    SubscriptionCommentForm,
    BtnRenewal
  },
  beforeMount() {
    this.subscriptionData = this.subscription;
  },
  data() {
    return {
      comments: [],
      commentIdToEdit: null,
      loadComments: true,
      loadSubscriptionDetail: false,
      subscriptionData: {}
    };
  },
  mounted() {
    this.initDraggable();
    this.$emitter.on(
      "get-subscription-comments" + this.subscriptionData.id,
      this.getComments
    );
    this.$emitter.on(
      "cancel-subscription-comment-edit",
      () => (this.commentIdToEdit = null)
    );
    this.$emitter.on(
      "refresh-detail-subscription-" + this.subscriptionData.id,
      () => this.refresh()
    );
  },
  methods: {
    getComments() {
      axios
        .get(route("api.subscription.comments", this.subscriptionData.id))
        .then(response => {
          if (response.data.length) {
            this.comments = response.data;
          } else {
            this.comments = [
              {
                content:
                  "<p class='text-muted'>les commentaires s'affichent ici...</p>",
                created_at: new Date(),
                created_at_fr: this.dateFr(new Date()),
                id: 0,
                user_id: 0,
                user_subscription_id: 0
              }
            ];
          }
          this.loadComments = false;
        });
    },
    refresh() {
      this.loadSubscriptionDetail = true;
      axios
        .get(
          route("api.subscription.detail", {
            id: this.subscriptionData.id,
            and_with: [
              "activities.absence_prevention",
              "activities.absence_prevention.queue",
              "activities.absence_prevention.queue.recuperation_request"
            ]
          })
        )
        .then(response => {
          this.subscriptionData = response.data;
          this.loadSubscriptionDetail = false;
        });
    },
    deleteSubscription(data) {
      let message = data.number_absence_prevention
        ? " Cette souscription a " +
          data.number_absence_prevention +
          " absence" +
          (data.number_absence_prevention > 1 ? "s" : "") +
          " prévenus"
        : "";

      this.$inertia.delete(route("subscriptions.destroy", data.id), {
        onBefore: () => confirm("Supprimer la souscription ? " + message),
        onSuccess: () => this.iReload()
      });
    }
  }
};
</script>
<style scoped>
.subscription-comment {
  background-color: #efefef;
}
</style>
