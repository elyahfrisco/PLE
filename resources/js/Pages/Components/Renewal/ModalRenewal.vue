<template>
  <jet-modal :id="id" maxWidth="xl" title="Renouvellement">
    <div v-if="renewalData">
      <form @submit.prevent="saveRenewal">
        <div v-if="renewalData.renewal" class="row">
          <div class="ml-auto">
            <button
              href="#"
              class="btn btn-danger btn-sm btn-action"
              data-toggle="tooltip"
              title="Annuler le renouvellement"
              @click.prevent="deleteRenewal(renewalData.renewal)"
            >
              <i class="fa fa-trash"></i>
            </button>
          </div>
        </div>
        <div class="subscription_info">
          <div class="row">
            <div class="col-md-6">
              <p>
                <label>Client</label> : {{ renewalData.customer.full_name }}
                <inertia-link
                  class="btn btn-action btn-outline-info d-inline ml-2"
                  :href="
                    route('renewals.index', {
                      q: 'client_id:' + renewalData.customer.id
                    })
                  "
                  data-toggle="tooltip"
                  :title="
                    'Afficher tous les renouvellements de ' +
                      renewalData.customer.full_name
                  "
                  ><i class="fa fa-eye"></i
                ></inertia-link>
              </p>
              <p>
                <label>Saison</label> : {{ renewalData.season.year_start }} -{{
                  renewalData.season.year_end
                }}
              </p>
              <p>
                <label>Pass</label> : {{ renewalData.pass.name }}
                {{ renewalData.establishment.sigle }}
              </p>
              <p><label>Activité</label> : {{ first_session.activity.name }}</p>
            </div>
            <div class="col-md-6">
              <p>
                <label>Date de souscription</label> :
                {{ dateFr(renewalData.created_at) }}
              </p>
              <p>
                <label>Periode de souscrit</label> :
                {{ dateFr(first_session.date) }} au
                {{ dateFr(last_session.date) }}
              </p>
              <p>
                <label>Nombre de semaine</label> :
                {{ renewalData.activities.length }}
              </p>
              <p>
                <label>Trimestre</label> :
                {{ renewalData.num_trimester }}
              </p>
            </div>
          </div>
        </div>

        <div class="btns-renewal row mx-0">
          <div class="form-group col-md-4 px-0 mb-1">
            <label for="renewal_status">Statut de renouvelement</label>
            <Multiselect
              id="renewal_status"
              placeholder="--Statut de renouvelement--"
              v-model="form.renewal_status"
              :options="select.renewal_status"
              :canDeselect="false"
              required
            />
          </div>
        </div>

        <div class="form-for-renewal-status-selected mt-3">
          <div
            v-if="form.renewal_status == 'not_informed'"
            class="not_informed"
          >
            <h5>
              Le client ne sait pas encore s’il va poursuivre l'activité au
              <strong>{{ trimester_txt }}</strong>
            </h5>
          </div>
          <div v-else-if="form.renewal_status == 'continue'" class="continue">
            <h5>
              Le client continue l'activité au
              <strong>{{ trimester_txt }}</strong>
            </h5>
          </div>
          <div
            v-else-if="form.renewal_status == 'continue_change_time'"
            class="continue_change_time"
          >
            <h5>
              Le client continue l'activité au
              <strong>{{ trimester_txt }}</strong> et souhaite changer d'horaire
            </h5>
          </div>
          <div
            v-else-if="form.renewal_status == 'continue_change_time_else_stop'"
            class="continue_change_time_else_stop"
          >
            <h5>
              Le client doit changer d’horaire au
              <strong>{{ trimester_txt }}</strong> sinon il ne continuera pas
              l'activité
            </h5>
          </div>

          <div
            v-if="
              [
                'continue_change_time',
                'continue_change_time_else_stop'
              ].includes(form.renewal_status)
            "
          >
            <div v-if="renewalData.renewal?.id" class="form-group">
              <label for="seence">Activité selectionnée : </label>
              <div>
                <h6>
                  Le client à choisi l'activité
                  {{ renewalData.renewal?.activity?.name }} du
                  {{ dateHFr(renewalData.renewal?.start_at) }}
                </h6>
              </div>
            </div>
            <div class="row">
              <div class="form-group col-md-4">
                <label for="seence">Centre : </label>
                <div>
                  <Multiselect
                    v-model="form.establishment_id"
                    placeholder="--Centre pour le renouvelement--"
                    :options="select.establishments"
                    label="name"
                    trackBy="name"
                    valueProp="id"
                    required
                  />
                </div>
              </div>
            </div>

            <button
              v-if="renewalData.id"
              type="button"
              class="btn btn-success btn-sm rounded-0 mb-2"
              @click.prevent="show_calendar = !show_calendar"
            >
              {{ renewalData.renewal ? "Modifier" : "Selectionner" }} le
              planning de renouvelement
            </button>
            <div class="row">
              <div
                id="section_calendar"
                v-show="show_calendar"
                class="mb-3 col-md-8"
              >
                <Calendar
                  :filter="{
                    calendar_for: 'renewal',
                    establishment_id: form.establishment_id,
                    num_trimester: form.num_trimester,
                    season_id: form.season_id,
                    user_id: renewalData.user_id,
                    planning_id: renewalData.activities[0].planning_id
                  }"
                  :calSync="true"
                  @attrib-plannings="attribPlannings"
                  :initSelectedSessions="form.selectedSessions"
                ></Calendar>
              </div>
              <div
                :class="{
                  'col-md-4': show_calendar,
                  'col-12': !show_calendar
                }"
              >
                <div class="row">
                  <template
                    v-for="(selectedSession_,
                    iselectedSession) in form.selectedSessions"
                    :key="selectedSession_.id"
                  >
                    <div
                      :class="{
                        'col-md-4': !show_calendar,
                        'col-12': show_calendar
                      }"
                    >
                      <PlanningCard
                        v-if="selectedSession_?.id"
                        :planning="selectedSession_"
                      />
                      <div class="actions-renewal">
                        <a
                          v-if="
                            $can('create_subscription_fro_renewal') &&
                              selectedSession_.renewal_id &&
                              selectedSession_.remaining_place > 0
                          "
                          target="_blank"
                          :href="
                            route('subscriptions.create', {
                              user_id: renewalData.user_id,
                              renewal_id: selectedSession_.renewal_id,
                              season_id: form.season_id,
                              establishment_id: form.establishment_id,
                              num_trimester: form.num_trimester,
                              pass_id: renewalData.pass_id,
                              pass_type: 'trimester',
                              subscription_id: renewalData.id
                            })
                          "
                          class="
                            btn btn-outline-success btn-action btn-sm
                            py-0
                            px-1
                            ml-1
                            btn-valide-renewal
                          "
                          :title="
                            'Valider et créer une nouvelle souscription pour le renouvellement de ' +
                              renewalData.customer.full_name
                          "
                          data-toggle="tooltip"
                          ><i class="fa fa-plus"></i
                        ></a>
                        <btn-delete
                          type="button"
                          @click.prevent="deselectWishe(iselectedSession)"
                        />
                      </div>
                    </div>
                  </template>
                </div>
              </div>
            </div>
          </div>

          <div v-else-if="form.renewal_status == 'stop'" class="stop">
            <h5>
              Le client ne continue pas l'activité au
              <strong>{{ trimester_txt }}</strong>
            </h5>

            <div class="form-group">
              <label>Raison</label>
              <textarea rows="4" class="form-control" v-model="form.reason">
              </textarea>
            </div>
          </div>
        </div>
        <div class="text-center">
          <button
            type="submit"
            :disabled="
              !form.renewal_status ||
                (!['not_informed', 'stop', 'continue'].includes(
                  form.renewal_status
                ) &&
                  !form.selectedSessions.length)
            "
            class="btn btn-success"
          >
            Enregistrer
          </button>
        </div>
      </form>
    </div>
    <div
      v-show="!renewalData"
      class="skeleton skeleton-line w-100"
      style="--lines: 5; --l-h: 10px; --l-gap: 15px"
    ></div>
  </jet-modal>
</template>

<script>
import Calendar from "@/Pages/Components/Calendar.vue";
import PlanningCard from "@/Pages/Components/PlanningCard.vue";
export default {
  props: ["subscription_id", "renewalData"],
  components: {
    Calendar,
    PlanningCard
  },
  data() {
    return {
      id: null,
      form: {
        renewal_status: "not_informed",
        establishment_id: null,
        activity_id: null,
        num_trimester: null,
        day: null,
        reason: null,
        selectedSessions: []
      },
      select: {
        renewal_status: []
      },
      selectedSession: {},
      show_calendar: false
    };
  },
  watch: {
    renewalData: {
      deep: true,
      handler() {
        setTimeout(() => {
          this.form.establishment_id = this.renewalData.establishment_id;
          this.form.num_trimester =
            this.renewalData.num_trimester < 3
              ? this.renewalData.num_trimester + 1
              : 1;
          this.form.subscription_id = this.subscription_id;
          this.form.id = null;

          if (this.renewalData.renewal) {
            this.form.id = this.renewalData.id;
            this.form.season_id = this.renewalData.renewal.season_id;
            this.form.renewal_status = this.renewalData.renewal?.renewal_status;

            if (
              [
                "continue_change_time",
                "continue_change_time_else_stop"
              ].includes(this.renewalData.renewal?.renewal_status)
            ) {
              let selectedSessions = [];
              for (let wishe of this.renewalData.renewal.wishes) {
                selectedSessions.push({
                  id: wishe.activity_session_id,
                  bgcolor: wishe.activity?.background_color,
                  font_color: wishe.activity?.font_color,
                  date: wishe.start_at,
                  timestart: wishe.planning?.time_start,
                  time_start: wishe.planning?.time_start,
                  timeend: wishe.planning?.time_end,
                  time_end: wishe.planning?.time_end,
                  day: wishe.planning?.day,
                  activity: wishe.activity?.name,
                  activity_id: wishe.activity_id,
                  establishment_id: wishe.establishment_id,
                  planning_id: wishe.planning_id,
                  renewal_id: wishe.id,
                  max_effective: wishe.activity_session.max_effective,
                  participants_count:
                    wishe.activity_session.participants_no_relation.length,
                  remaining_place:
                    wishe.activity_session.max_effective -
                    wishe.activity_session.participants_no_relation.length
                });
              }

              this.form.selectedSessions = selectedSessions;
            }
          } else if (this.form.num_trimester == 1) {
            axios
              .get(route("api.season.next", this.renewalData.season_id))
              .then(response => {
                this.form.season_id = response.data;
              });
          } else {
            this.form.season_id = this.renewalData.season.id;
          }
          this.form.activity_id = this.first_session.activity_id;
        }, 100);
      }
    }
  },
  methods: {
    saveRenewal() {
      this.$inertia.post(route("renewals.store"), this.form, {
        onSuccess: () => {
          $("[id^=modal-renewal].show .close").click();
          this.$emit("renewal_saved");
        },
        preserveScroll: true
      });
    },
    attribPlannings(plannings) {
      let selectedSessions = [];
      for (let index of Object.keys(plannings)) {
        selectedSessions.push(plannings[index]);
      }

      this.form.selectedSessions = selectedSessions;
    },
    deselectWishe(id) {
      this.form.selectedSessions.splice(id, 1);
    },
    deleteRenewal(data) {
      this.$inertia.delete(route("renewals.destroy", data.id), {
        onBefore: () => confirm("Supprimer le renouvellement ?"),
        preserveScroll: true,
        onSuccess: () => {
          $("[id^=modal-renewal].show .close").click();
        }
      });
    }
  },
  mounted() {
    axios.get(route("api.renewals.status")).then(response => {
      this.select.renewal_status = response.data;
      if (!this.form.id) {
        $("#section_calendar").removeClass("collapse");
      }
    });
    this.id = "modal-renewal" + this.subscription_id;
    this.select.establishments = this.$page.props.establishments_list;
  },
  computed: {
    first_session() {
      return this.renewalData.activities[0];
    },
    last_session() {
      return this.renewalData.activities[
        this.renewalData.activities.length - 1
      ];
    },
    next_trimester() {
      let next_ = this.renewalData.num_trimester + 1;
      return next_ > 3 ? 1 : next_;
    },
    trimester_txt() {
      let next_ = this.next_trimester;
      return (
        next_ +
        (next_ == 1 ? "er" : "e") +
        (next_ == 1 ? " Trimestre de la saison suivant" : "")
      );
    }
  }
};
</script>

<style lang="scss" scoped>
.subscription_info {
  p {
    margin-bottom: 0 !important;
  }
}

.btns-renewal {
  & > div {
    padding: 5px;
  }
  .btn {
    border-radius: 0 !important;
    width: 100%;
    height: 100%;
    &.active {
      background-color: #1d9cc4 !important;
      border: none !important;
    }
  }
}

.actions-renewal {
  text-align: center;

  .btn-action {
    position: relative;
    top: -31px;
    border-radius: 100%;
    min-width: 35px;
    padding-bottom: 7px !important;
    padding-top: 7px !important;
  }
}

.btn-valide-renewal {
  &:not(:hover) {
    background-color: white;
  }
}

#section_calendar {
  max-height: 400px;
  overflow-y: scroll;
}
</style>
