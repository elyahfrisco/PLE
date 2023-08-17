<template>
  <div
    class="row"
    :class="{
      'my-1': !w_auto
    }"
  >
    <div
      class="form-group col"
      :class="[!w_auto && 'ml-auto col-md-6 col-sm-6', class_]"
    >
      <input
        type="searchQuery"
        :placeholder="placeholder ? placeholder : 'mot clé'"
        v-model.trim="q"
        @keyup.prevent="searchQuery"
        class="form-control"
      />
    </div>
  </div>
</template>

<script>
import debounce from "lodash/debounce";
export default {
  props: [
    "url",
    "only",
    "w_auto",
    "class_",
    "placeholder",
    "preserve_state",
    "attrs",
    "ajaxMode"
  ],
  data() {
    return {
      q: null,
      href: null,
      mounted_: false
    };
  },
  beforeMount() {
    this.q = this.q_.q;
  },
  mounted() {
    this.href = this.url ? this.url : this.current_url;
    this.mounted_ = true;
  },
  methods: {
    searchQuery: debounce(function(e) {
      if (
        e.keyCode !== 13 &&
        ((e.keyCode !== 46 && e.keyCode !== 8) || this.q != "")
      ) {
        return;
      }

      if (this.ajaxMode) {
        this.$emitter.emit("filterTable", { q: this.q });
        return;
      }

      if (this.mounted_) {
        var params = {
          data: {
            q: this.q,
            account_type: this.q_.account_type
          },
          preserveScroll: true,
          preserveState: this.preserve_state
        };

        if (Array.isArray(this.only)) {
          if (this.only.length) {
            params.only = [...this.only, "req"];
          }
        }

        params.page ? delete params.page : 0;

        if (this.q != this.q_.q) {
          this.$inertia.visit(this.href, params);
        }
      }
    }, 300)
  }
};
</script>
