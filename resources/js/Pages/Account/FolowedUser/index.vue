<template>
  <app-layout>
    <template #pageTitle>Clients suivi</template>
    <div class="folowed-user">
      <div class="folwed-user__container">
        <div class="request-form--container">
          <RequestForm />
        </div>
        <div class="folowed-user__list mt-2">
          <UserList />
        </div>
      </div>
    </div>
  </app-layout>
</template>

<script>
import RequestForm from "./RequestForm.vue";
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
