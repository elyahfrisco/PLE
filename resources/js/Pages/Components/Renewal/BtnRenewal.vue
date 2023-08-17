<template>
  <button
    v-if="subscription.renewal"
    class="btn btn-sm btn-warning py-0 px-1 ml-1 btn-action"
    @click.prevent="showModal"
  >
    <i class="fa fa-edit"></i>
  </button>
  <button
    v-else
    class="btn btn-sm btn-warning btn-action"
    @click.prevent="showModal"
  >
    <i
      v-if="status && status.length && status !== 'not_informed'"
      class="fa fa-edit"
    ></i>
    Renouveler
  </button>

  <modal-renewal
    v-if="active"
    :subscription_id="active_subscription_id"
    :renewalData="renewalData"
    @renewal_saved="() => $emit('renewal_saved')"
  />
</template>

<script>
import ModalRenewal from "./ModalRenewal";
export default {
  props: ["subscription", "subscription_id", "status"],
  components: {
    ModalRenewal
  },
  data() {
    return {
      renewalData: null,
      active: false,
      active_subscription_id: null
    };
  },
  methods: {
    showModal() {
      this.active = true;

      this.active_subscription_id = this.subscription_id
        ? this.subscription_id
        : this.subscription.id;

      axios
        .get(
          route("api.subscription.detail", {
            id: this.active_subscription_id,
            with_renewal_wishes: true
          })
        )
        .then(response => {
          this.renewalData = response.data;
        });
      setTimeout(() => {
        $("#modal-renewal" + this.active_subscription_id).modal();
      }, 200);
    }
  }
};
</script>


