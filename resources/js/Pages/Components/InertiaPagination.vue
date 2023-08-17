<template>
  <nav v-if="data.length > 3" aria-label="Page navigation">
    <ul class="pagination" v-if="data">
      <li
        v-for="(link, i) in data"
        :key="i"
        :class="'page-item ' + (link.active ? 'active' : '')"
      >
        <inertia-link
          v-if="link.url"
          class="page-link"
          :href="link.url ? link.url : '#'"
          v-html="link.label"
          preserve-scroll
          :only="only"
          :class="{ active: link.active }"
        ></inertia-link>
        <a
          v-else
          href="#"
          class="page-link"
          :class="{ active: link.active }"
          v-html="link.label"
        ></a>
      </li>
    </ul>
  </nav>
</template>

<script>
export default {
  props: ["data", "only"]
};
</script>


