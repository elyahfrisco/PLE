<template>
  <app-layout>
    <template #pageTitle>Demandes de suivi</template>
    <div class="folowed-user">
      <div class="folwed-user__container">
        <!-- <div class="request-form--container">
          <RequestForm />
        </div> -->
        <div class="folowed-user__list mt-2">
          <UserList :admin="true" />
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import RequestForm from "../RequestForm.vue";
import UserList from "./UserList.vue";
export default {
  name: "FolowedUser",
  components: {
    RequestForm,
    UserList
  }
};
</script>

<style scoped></style>
