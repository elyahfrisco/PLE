<template>
  <form-role></form-role>
  <a :href="route('roles.create')">Ajouter</a>
  <table>
    <thead>
      <tr>
        <th>Titre</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <tr v-for="role in this.roles" v-bind:key="role.id">
        <td>{{ role.name }}</td>
        <td>
          <a :href="route('roles.create')">Modifier les autorisations</a>
        </td>
      </tr>
    </tbody>
  </table>
</template>

<script>
import formRole from "./form.vue";
export default {
  props: ["roles"],
  components: {
    formRole: formRole
  }
};
</script>


