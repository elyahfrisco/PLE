<template>
  <template v-if="searchable || ajaxMode">
    <input-search
      v-if="Array.isArray(only)"
      :preserve_state="preserve_state"
      :only="only"
      :url="current_url"
      :ajaxMode="ajaxMode"
    />
    <input-search
      v-else
      :preserve_state="preserve_state"
      :url="current_url"
      :ajaxMode="ajaxMode"
    />
  </template>
  <div class="table-responsive">
    <table id="table_id" class="display table">
      <thead>
        <tr>
          <slot name="header" />
        </tr>
      </thead>
      <tbody>
        <slot name="content" />
      </tbody>
    </table>
  </div>
  <div class="d-flex w-100 justify-content-end">
    <div
      v-if="paginationData.links"
      :class="{ 'w-100': !paginationData.total }"
    >
      <div>
        <p v-if="paginationData.total" class="mb-1 text-right">
          {{ paginationData.to }} / {{ paginationData.total }}
        </p>
        <p v-else class="mb-1 text-center">Aucun élément à afficher</p>
      </div>
      <inertia-pagination
        v-if="Array.isArray(only)"
        :only="only"
        :data="paginationData.links"
      />
      <inertia-pagination v-else :data="paginationData.links" />
    </div>
  </div>
</template>

<script>
import InertiaPagination from "@/Pages/Components/InertiaPagination";
import debounce from "lodash/debounce";
export default {
  components: {
    InertiaPagination
  },
  props: {
    pagination: Object,
    only: Array,
    searchable: Boolean,
    ajaxMode: Boolean,
    id: String,
    preserve_state: { default: false }
  },
  beforeMount() {
    if (!this.ajaxMode) {
      this.$emitter.on("sortTable", this.sortTable);
    }
    this.href = this.url ? this.url : this.current_url;
    this.paginationData = this.pagination == null ? {} : this.pagination;
  },
  data() {
    return {
      href: null,
      paginationData: {}
    };
  },
  methods: {
    sortTable: debounce(function(sort_param) {
      var params = {
        data: {
          ...route().params,
          ...sort_param
        },
        preserveScroll: true,
        preserveState: this.preserve_state
      };
      this.$inertia.visit(this.href, params);
    }, 300)
  }
};
</script>


