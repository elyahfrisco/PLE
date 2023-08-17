<template>
  <p class="mb-0"><label for="">Panier</label></p>
  <table class="table min">
    <thead>
      <tr>
        <th>Pass</th>
        <th>Activité</th>
        <th>Jour</th>
        <th>Date</th>
        <th>Horaire</th>
      </tr>
    </thead>
    <tbody>
      <template v-if="subscriptionData.length > 0">
        <template v-for="(passSubscription, i) in subscriptionData" :key="i">
          <tr
            v-for="(sessionSubscription, j) in passSubscription.data.sessions"
            :key="j"
          >
            <td>
              {{ passSubscription.display.pass }}
            </td>
            <td>
              {{ sessionSubscription.display.activity }}
            </td>
            <td>
              {{ sessionSubscription.display.day }}
            </td>
            <td>
              {{ sessionSubscription.display.date }}
            </td>
            <td>
              {{ sessionSubscription.display.planning }}
            </td>
          </tr>
        </template>
      </template>
      <template v-else>
        <tr>
          <td colspan="5" class="text-center">
            Panier vide
            <small
              class="validation-error d-block"
              v-if="errors.subscriptionData"
              >{{ errors.subscriptionData }}</small
            >
          </td>
        </tr>
      </template>
    </tbody>
  </table>
</template>

<script>
export default {
  props: ["subscriptionData"]
};
</script>


