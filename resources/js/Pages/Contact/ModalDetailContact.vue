<template>
  <jet-modal
    :id="'modal-detail-contact-' + contact.id"
    :title="contact.user.full_name + ' : ' + contact.object"
  >
    <div class="px-2">
      <p v-html="contact.content"></p>
    </div>
  </jet-modal>
</template>

<script>
export default {
  props: ["contact"]
};
</script>
