<template>
  <app-layout>
    <div class="row mt-3">
      <div class="col-md-6 mb-3">
        <inertia-link
          :href="route('customers.create')"
          class="btn btn-success rounded-0"
        >
          <i class="fa fa-plus"></i> Ajouter un client
        </inertia-link>
        <inertia-link
          :href="
            route('customers.create', {
              account_type: 'prospect'
            })
          "
          class="ml-3 btn btn-primary rounded-0"
        >
          <i class="fa fa-plus"></i> Ajouter un prospect
        </inertia-link>
      </div>
      <div class="col-md-12">
        <dashboard-filter />
      </div>
      <div class="offset-md-6 col-md-6">
        <input-search
          :url="current_url"
          :only="['users']"
          placeholder="Recherche client/prospect..."
          :preserve_state="true"
        />
      </div>
    </div>
    <search-result v-if="users" :users="users" :account_type="account_type" />
    <div class="col-md-6 mt-3">
      <div class="row">
        <div class="col-12 border px-1" :style="{ 'overflow-y': 'hidden' }">
          <p class="font-weight-bold">Planning du jour</p>
          <div class="form-group mr-1">
            <label>Centre</label>
            <div class="row">
              <div class="col-11">
                <Multiselect
                  v-model="establishment_id"
                  placeholder="--centres--"
                  :options="this.$page.props.establishments_list"
                  :searchable="true"
                  :required="true"
                  :canDeselect="false"
                  trackBy="name"
                  label="name"
                  valueProp="id"
                />
              </div>
              <div class="col-1 px-0">
                <inertia-link
                  :href="
                    route('establishments.plannings.sessions.index', {
                      establishment: establishment_id
                    })
                  "
                  class="btn btn-success py-2 h-100 py-auto"
                  ><i class="fa fa-eye"></i
                ></inertia-link>
              </div>
            </div>
          </div>
          <Calendar
            :filter="{ establishment_id: establishment_id }"
            :calSync="true"
            :show_passed_date="true"
            :today_button="false"
            :hide_view_selector="true"
            :disableViews="['years', 'year', 'month', 'week']"
          />
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import Calendar from "@/Pages/Components/Calendar.vue";
import SearchResult from "./SearchResult.vue";
import DashboardFilter from "./Filter.vue";
export default {
  components: {
    SearchResult,
    DashboardFilter,
    Calendar
  },
  props: ["users"],
  data() {
    return {
      establishment_id: null
    };
  },
  beforeMount() {
    this.establishment_id = this.$page.props.establishments_list[0].id;
  },
  mounted() {},
  methods: {}
};
</script>


