<template>
  <div class="container">
    <div class="row justify-content-center my-5">
      <div class="col-sm-12 col-md-8 my-4" :class="'col-lg-' + col_number">
        <div class="card shadow-sm">
          <div class="card-header">
            <h3 class="card-title"><slot name="headerTitle"></slot></h3>
            <br />
            <br />
            <p class="card-description">
              <slot name="headerDescription"></slot>
            </p>
          </div>
          <div class="navtab">
            <div class="d-flex text-center">
              <slot name="NavTabButtons"></slot>
            </div>
          </div>
          <div class="card-body px-3">
            <slot />
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ["large"],
  data() {
    return {
      col_number: 5
    };
  },
  beforeMount() {
    if (this.large) {
      this.col_number = "8";
    }
  }
};
</script>
