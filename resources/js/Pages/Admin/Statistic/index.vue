<template>
  <app-layout>
    <div class="row">
      <div class="col-md-5">
        <div class="card flat">
          <div class="card-header">
            <h3 class="card-title">
              <i class="ion ion-clipboard mr-1"></i>
              Liste des passes
            </h3>
            <div class="row col-12">
              <div class="col-md-4">
                <input
                  class="form-control"
                  type="date"
                  placeholder="date min"
                  v-model="form.min_date"
                />
              </div>
              <div class="col-md-4">
                <input
                  class="form-control"
                  type="date"
                  placeholder="date max"
                  v-model="form.max_date"
                />
              </div>
              <div class="form-group col-md">
                <button
                  @click.prevent="getStats"
                  class="btn btn-success btn-sm"
                >
                  <i class="fa fa-filter"></i>
                </button>
                <button
                  @click.prevent="resetFilters"
                  class="btn btn-primary btn-sm ml-2"
                >
                  <i class="fa fa-trash"></i>
                </button>
              </div>
            </div>
          </div>
          <div class="card-body">
            <ul class="todo-list" data-widget="todo-list">
              <template v-for="item in pass">
                <li>
                  <span class="text">{{ item["name"] }}</span>
                  <small class="badge badge-info">{{ item["total"] }}</small>
                </li>
              </template>
              <li>
                <span class="text">Total: </span>
                <small class="badge badge-success">{{ totals }}</small>
              </li>
            </ul>
            <br />
            <br />
            <canvas id="myChart" width="400" height="400"></canvas>
          </div>
        </div>
        <section-prospect />
      </div>
      <div class="col-md-7">
        <section-reglement />
      </div>
    </div>
  </app-layout>
</template>

<script>
import SectionProspect from "./section_prospect.vue";
import SectionReglement from "./section_reglement.vue";

export default {
  props: ["total", "passes", "activities"],
  components: {
    SectionProspect,
    SectionReglement
  },
  data() {
    return {
      form: {},
      myChart: null,
      totals: null,
      pass: null,
      acts: null
    };
  },
  mounted() {
    this.totals = this.total;
    this.pass = this.passes;
    this.acts = this.activities;
    this.getChart();
  },
  methods: {
    getChart() {
      //pie

      var xValues = [];
      var yValues = [];
      var barColors = [];
      this.activities.forEach(element => {
        xValues.push(element["name"]);
        yValues.push(element["total"]);
        barColors.push(element["color"]);
      });
      //var ctx = document.getElementById('myChart'); // node
      //var ctx = document.getElementById('myChart').getContext('2d'); // 2d context
      var ctx = $("#myChart"); // jQuery instance
      var ctx = "myChart"; // element id
      this.myChart = new Chart(ctx, {
        type: "pie",
        data: {
          labels: xValues,
          datasets: [
            {
              backgroundColor: barColors,
              data: yValues
            }
          ]
        },
        options: {
          title: {
            display: true,
            text: "Passes par activité "
          }
        }
      });
    },
    resetFilters() {
      this.form.min_date = null;
      this.form.max_date = null;
    },
    getStats() {
      axios
        .get(route("api.statistic.filter"), {
          params: this.form
        })
        .then(response => {
          this.myChart.destroy();
          this.totals = response.data["total"];
          this.pass = response.data["passes"];
          this.acts = response.data["activities"];
          var xValues = [];
          var yValues = [];
          var barColors = [];
          this.acts.forEach(element => {
            xValues.push(element["name"]);
            yValues.push(element["total"]);
            barColors.push(element["color"]);
          });
          var ctx = "myChart"; // element id
          this.myChart = new Chart(ctx, {
            type: "pie",
            data: {
              labels: xValues,
              datasets: [
                {
                  backgroundColor: barColors,
                  data: yValues
                }
              ]
            },
            options: {
              title: {
                display: true,
                text: "Passes par activité "
              }
            }
          });
        });
    }
  }
};
</script>


