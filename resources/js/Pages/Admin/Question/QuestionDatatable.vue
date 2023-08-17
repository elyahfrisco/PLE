<template>
  <div class="table-responsive">
    <table id="table_id" class="display table datatable">
      <thead>
        <tr>
          <th
            v-for="label in labels"
            :key="label"
            :style="'width:' + (label.width ? label.width + 'px' : '')"
          >
            {{ label.label }}
          </th>
          <th style="width: 127px" class="text-right">Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="item in TableData" :key="item.id">
          <td class="text-capitalize">{{ item.content }}</td>
          <td class="text-uppercase">
            {{ item.type === "single" ? "unique" : "multiple" }}
          </td>
          <td class="text-uppercase">
            {{ item.other_response ? "oui" : "non" }}
          </td>
          <td>
            <ul v-if="item.answers.length">
              <li v-for="answer in item.answers" :key="answer.id">
                {{ answer.content }}
              </li>
            </ul>
          </td>
          <td class="column-actions">
            <btn-edit :href="route('questions.edit', item.id)" />
            <btn-delete @click.prevent="deleteQuestion(item.id)" />
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  props: ["TableData"],
  data() {
    return {
      labels: []
    };
  },
  beforeMount() {
    this.labels.push({ label: "Question" });
    this.labels.push({ label: "Type", width: "100" });
    this.labels.push({ label: "Autre reponse", width: "100" });
    this.labels.push({ label: "Reponses possible", width: "150" });
  },
  methods: {
    deleteQuestion(id) {
      this.$inertia.delete(route("questions.destroy", id), {
        onBefore: () => confirm("Supprimer etablisement?")
      });
    }
  }
};
</script>


