<template>
  <jet-modal
    :id="'modal-detail-subscription' + subscription.id"
    maxWidth="xl"
    title="Detail de la souscription"
  >
    <div class="row">
      <div class="col-md-4">
        <p>Client : {{ subscription.customer.full_name }}</p>
        <p>Centre : {{ subscription.establishment.name }}</p>
        <p>
          Saison : {{ subscription.season.year_start }} -
          {{ subscription.season.year_end }}
        </p>
        <p v-if="subscription.num_trimester">
          Trimestre : {{ subscription.num_trimester }}
        </p>
      </div>
      <div class="col-md-4">
        <p>Pass : {{ subscription.pass.name }}</p>
        <p>Date de souscription : {{ dateFr(subscription.created_at) }}</p>
        <p>
          Debut activité : {{ dateHFr(subscription.date.start) }} (
          {{ subscription.date.elapsetime_start }} )
        </p>
        <p>
          Fin activité : {{ dateHFr(subscription.date.end) }} (
          {{ subscription.date.elapsetime_end }} )
        </p>
      </div>
      <div class="col-md-4">
        <p>Rénouvellement : PAS INFORME</p>
        <p>
          Réglement :
          <span class="badge badge-success ml-2" v-if="subscription.payment"
            >réglé</span
          >
          <span class="badge badge-danger ml-2" v-else>non réglé</span>
        </p>
        <p>Status : <status-badge :status="subscription.status" /></p>
      </div>
    </div>

    <detail-subscription-table :subscription="subscription" />

    <!-- <pre>
  {{ subscription }}
</pre
    > -->
  </jet-modal>
</template>

<script>
import DetailSubscriptionTable from "@/Pages/Components/DetailSubscriptionTable";
export default {
  props: ["subscription"],
  components: { DetailSubscriptionTable }
};
</script>
