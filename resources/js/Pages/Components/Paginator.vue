<template>
  <nav aria-label="Page navigation">
    <ul class="pagination" v-if="data && data.last_page > 2">
      <li v-if="data.current_page > 1" class="page-item">
        <a class="page-link" href="#" @click.prevent="prev">Precedent</a>
      </li>
      <li v-else class="page-item disabled">
        <a class="page-link" @click.prevent="prev">Precedent</a>
      </li>

      <li
        v-for="page in data.last_page"
        :key="'paginator-' + page"
        :class="'page-item ' + (data.current_page == page ? 'active' : '')"
      >
        <a class="page-link" href="#" @click.prevent="goTo(page)">{{ page }}</a>
      </li>

      <li class="page-item" v-if="data.current_page < data.last_page">
        <a class="page-link" href="#" @click.prevent="next">Suivant</a>
      </li>
      <li class="page-item disabled" v-else>
        <a class="page-link">Suivant</a>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  props: ["data"],
  methods: {
    goTo(page) {
      this.$emit("goto", page);
    },
    next() {
      this.goTo(this.data.current_page + 1);
    },
    prev() {
      this.goTo(this.data.current_page - 1);
    }
  }
};
</script>


