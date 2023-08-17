<template>
  <app-layout>
    <div class="dashboard-page">
      <div class="dashboard-page__next-session">
        <span>Prochaine scéances</span>
        <div
          class="dashboard-page__next-session__items"
          v-if="planningData.length != 0"
        >
          <div
            class="dashboard-page__next-session__item"
            v-for="(p, i) in planningData"
            :key="p.activity + i"
          >
            <PlanningCard
              :data="p.date"
              :presence="p"
              @prevent_absence="showModalPreventAbsence(p)"
            />
          </div>
          <loadings :processing="loadingPlanning" />
        </div>
        <div
          v-else-if="planningData.length == 0 && !loadingPlanning"
          class="alert empty-planning"
        >
          Vous n'êtes pas encore souscrit à une activité
        </div>
      </div>
      <div class="dashboard-page__actuality">
        <span>Actualités</span>
        <div class="dashboard-page__page__actuality__items">
          <div
            class="dashboard-page__page__actuality__item"
            v-for="a in actualities"
            :key="a.id"
          >
            <ActualityCard
              :title="a.title"
              :timer="a.elapseTime"
              :content="a.content"
              :type="a.category"
              :id="a.id"
            />
          </div>
        </div>
        <div
          class="dashboard-page__actuality__more"
          v-if="lastPage !== page || loading"
        >
          <button
            type="button"
            class="btn dashboard-page__actuality__more-button"
            :disabled="loading"
            @click="loadNextListArticle"
            v-if="!loading"
          >
            Voir plus
          </button>
          <loadings :processing="loading" />
        </div>
      </div>
      <modal-prevent-absence :selectedSeance="selectedSeance" />
    </div>
  </app-layout>
</template>

<script>
import PlanningCard from "@/Pages/Components/PlanningCard.vue";
import ActualityCard from "@/Pages/Components/ActualityCard.vue";
import modalPreventAbsence from "@/Pages/Components/modalPreventAbsence.vue";

export default {
  components: {
    PlanningCard,
    ActualityCard,
    modalPreventAbsence
  },
  data: () => ({
    planningData: [],
    actualities: [],
    page: 1,
    type: {
      // TODO : Type missing from backend - static data
      text: "Actualité",
      color: "#f66d9b"
    },
    loading: true,
    loadingPlanning: true,
    selectedSeance: null
  }),
  beforeMount() {
    // --
    // TODO : Plug with the backend API while fetching the list of planning
    // set planning Data with dynamic data here
    // --

    this.getUpcommingSeance();
    this.getArticles();
  },
  methods: {
    showModalPreventAbsence(planing) {
      this.selectedSeance = planing;
      $("#modal-prevent-absence").modal();
    },
    getArticles: function() {
      this.loading = true;
      axios
        .get(route("api.posts", { page: this.page }))
        .then(response => {
          cpp(response.data.data).map(d => {
            this.actualities.push(d);
          });
          this.lastPage = cpp(response.data.last_page);
          this.loading = false;
        })
        .catch(() => {
          this.loading = false;
        });
    },
    getUpcommingSeance: function() {
      this.loadingPlanning = true;
      var params = {};
      params.user_id = this.auth_user.id;
      params.paged = true;
      params.per_page = 3;
      params.minDate = new Date();

      axios
        .get(route("api.plannings.sessions"), {
          params
        })
        .then(response => {
          if (response.data.data != undefined) {
            this.planningData = response.data.data;
          } else {
            this.planningData = [];
          }
          this.loadingPlanning = false;
        });
    },
    loadNextListArticle: async function() {
      this.page++;
      this.getArticles();
    }
  }
};
</script>

<style lang="scss" scoped>
@import "@/Pages/Components/main.scss";

.empty-planning {
  background-color: $color-warning_empty;
  color: white;
}

@mixin __header-title() {
  text-align: center;

  span {
    color: $color-lpdl;
    font-weight: bold;
    font-size: 1.5rem;
  }
}

@mixin __item-flex() {
  display: flex;
  justify-content: space-between;
  flex-wrap: wrap;
}

.dashboard-page {
  .dashboard-page__next-session {
    @include __header-title();

    .dashboard-page__next-session__items {
      @include __item-flex();

      .dashboard-page__next-session__item {
        width: 32%;

        @include tabletBreakpoint {
          width: 49%;
        }

        @include mobileBreakpoint {
          width: 100%;
        }
      }
    }
  }

  .dashboard-page__actuality {
    @include __header-title();

    .dashboard-page__page__actuality__items {
      @include __item-flex();

      .dashboard-page__page__actuality__item {
        width: 45%;

        @include mobileBreakpoint {
          width: 100%;
        }
      }
    }

    .dashboard-page__actuality__more {
      margin: 1.5rem auto;

      .dashboard-page__actuality__more-button {
        background-color: white;
        color: $color-lpdl;
        border: 2px solid $color-lpdl;
        font-size: 1rem;
      }
    }
  }
}
</style>
