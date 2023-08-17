<template>
  <div class="d-flex" v-if="bill.subscriptions == undefined">
    <div
      class="skeleton skeleton-line w-100"
      style="--lines: 1; --l-h: 100px"
    ></div>
  </div>
  <template v-else>
    <div class="row text-right mb-2">
      <a
        v-if="!print && auth_user.role_name == 'admin'"
        target="_blank"
        class="btn btn-action btn-outline-info d-inline d-none-print"
        :href="
          route(bill.is_paied ? 'payments.index' : 'invoice.unpaid.index', {
            q: 'client_id:' + bill.user_id
          })
        "
        data-toggle="tooltip"
        :title="
          'Consulter toutes les factures ' +
            (bill.is_paied ? 'payées' : 'impayées') +
            ' de ' +
            bill.user.full_name
        "
        ><i class="fa fa-eye"></i> Consulter toutes les factures
        {{ bill.is_paied ? "payées" : "impayées" }} de
        {{ bill.user.full_name }}</a
      >
    </div>
    <div class="row invoice">
      <div class="col-12 text-center mb-3">
        <a
          v-if="print != 1"
          target="_blank"
          class="btn btn-secondary mx-auto btn-print btn-print-invoice flat"
          :href="route('invoice.show', { id: bill_id, print: true })"
        >
          <i class="fa fa-print"></i> Imprimer</a
        >
        <a
          v-else
          class="btn btn-secondary mx-auto btn-print btn-print-invoice flat"
          @click.prevent="nav_print"
        >
          <i class="fa fa-print"></i> Imprimer</a
        >
      </div>
      <div class="col-12">
        <div class="row">
          <div class="col-md-12 mb-3">
            <img
              src="/images/logo.png"
              alt="Logo Les plaisirs de l'eau"
              class="logo-ple"
            />
          </div>
          <div class="col-md-6">
            <h2><b>LES PLAISIRS DE L'EAU</b></h2>
            <h4>267, Avenue d'Arès</h4>
            <div class="mt-3 invoice__contact mb-3">
              <h4>33200 BORDEAUX</h4>
              <h5>Tel : 05 56 96 95 13</h5>
              <h5>Internet : contact@lesplaisirsdeleau.fr</h5>
            </div>
          </div>
          <div class="col-md-6 text-center">
            <h2 class="invoice_title"><b>FACTURE</b></h2>
            <table class="table">
              <thead>
                <tr>
                  <th>NUMÉRO</th>
                  <th>DATE</th>
                  <th>CLIENT</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>{{ bill.id }}</td>
                  <td>{{ dateFr(bill.created_at) }}</td>
                  <td class="text-uppercase">
                    {{ bill.user.full_name_min }}
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
      <div class="col-12">
        <div class="row">
          <div class="col-md-6">
            <h5>RCS : Bx 99 B 00026</h5>
            <h5>TVA Intra. : FR72421373549</h5>
            <h5>N° Siret : 42137354900037</h5>
          </div>
          <div class="col-md-6 my-2">
            <div
              class="invoice__client__info d-flex align-items-center h-100 p-2"
            >
              <div class="w-100">
                <h3>
                  <b>
                    <span class="">{{
                      bill.user.first_name
                    }}</span>
                    <span class="text-capitalize ml-2">{{
                      bill.user.name.toUpperCase()
                    }}</span>
                  </b>
                </h3>
                <h4>{{ bill.user.address }}</h4>
                <h4>{{ bill.user.postal_code }} {{ bill.user.city }}</h4>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-12">
        <table class="table text-center">
          <thead>
            <tr>
              <th>MODE DE RÈGLEMENT</th>
              <th>ÉCHÉANCE</th>
              <th>NUMÉRO D'ID. CEE</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <td>
                {{ payment_method ? payment_method : bill.payment_method }}
              </td>
              <td>{{ dateFr(bill.predictable_payment_date) }}</td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
      <!-- Produits -->
      <div class="col-md-12">
        <table class="table">
          <thead>
            <tr>
              <th>RÉFÉRENCE</th>
              <th class="invoice__designation_columns">DÉSIGNATION</th>
              <th>QTE</th>
              <th>PRIX UNIT</th>
              <th>REM</th>
              <th>PRIX HT</th>
              <th>PRIX TTC</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="fees in bill.fees" :key="'fees' + fees.id">
              <td>{{ fees.invoice_data.reference }}</td>
              <td><span v-html="fees.invoice_data.designation" /></td>
              <td class="text-right">1</td>
              <td class="text-right">{{ fees.invoice_data.unit_price }}</td>
              <td class="text-right">{{ fees.invoice_data.rem }}</td>
              <td class="text-right">{{ fees.invoice_data.ht_price }}</td>
              <td class="text-right">{{ fees.invoice_data.ttc_price }}</td>
            </tr>
            <tr
              v-for="subscription in bill.subscriptions"
              :key="'subscription' + subscription.id"
              :class="{
                'active_subscription_view animate__animated animate__repeat-1 animate__flash':
                  subscription_id == subscription.id
              }"
            >
              <td>{{ subscription.invoice_data.reference }}</td>
              <td><span v-html="subscription.invoice_data.designation" /></td>
              <td class="text-right">1</td>
              <td class="text-right">
                {{ subscription.invoice_data.unit_price }}
              </td>
              <td class="text-right">{{ subscription.invoice_data.rem }}</td>
              <td class="text-right">
                {{ subscription.invoice_data.ht_price }}
              </td>
              <td class="text-right">
                {{ subscription.invoice_data.ttc_price }}
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-12">
        <div class="row">
          <div class="col-md-6">
            Pénalité de retard au taux annuel de : 2,27%
          </div>
          <div class="col-md-6 text-right">
            Pas d'escompte en cas de paiement anticipé
          </div>
          <p class="col-12">
            Indemnité forfaitaire de frais de recouvrement : 40€
          </p>
        </div>
      </div>
      <!-- TVA -->
      <div class="col-md-7">
        <table class="table first-column-colored">
          <thead>
            <tr>
              <th></th>
              <th>BASE</th>
              <th>%</th>
              <th>MONTANTS</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th>TVA N°1</th>
              <td class="text-right">{{ bill.ht_amount }}</td>
              <td class="text-right">{{ bill.tva }}%</td>
              <td class="text-right">{{ bill.tva_amount }}</td>
            </tr>
            <tr>
              <th>TVA N°2</th>
              <td></td>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <th>TPF</th>
              <td></td>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-5">
        <table class="table first-column-colored">
          <thead>
            <tr>
              <th></th>
              <th>%</th>
              <th>MONTANTS</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th>REMISE GLOBALE</th>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <th>ESCOMPTE</th>
              <td></td>
              <td></td>
            </tr>
            <tr>
              <th>ACOMPTE</th>
              <td></td>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-4">
        <table class="table first-column-colored">
          <thead>
            <tr>
              <th></th>
              <th>MONTANTS</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <th>PORT HT</th>
              <td></td>
            </tr>
            <tr>
              <th>TVA / PORT</th>
              <td></td>
            </tr>
            <tr>
              <th>PORT TTC</th>
              <td></td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-md-8">
        <table class="table">
          <thead>
            <tr>
              <th>TOTAL HT</th>
              <th>TOTAL TVA</th>
              <th>TOTAL TTC</th>
              <th>NET À PAYER</th>
            </tr>
          </thead>
          <tbody>
            <tr class="text-right total-amount">
              <td>{{ bill.ht_total }}</td>
              <td>{{ bill.tva_total }}</td>
              <td>{{ bill.ttc_total }}</td>
              <td>
                <b>{{ bill.net_to_pay }}</b>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="col-12" v-if="bill.is_paied">
        <div class="float-right">
          Facture: 
          <span class="badg-success">
            ACQUITTÉE
          </span>
        </div>
      </div>
      <div class="col-12 invoice__footer">
        <p>S.A.S. au capital de 7 700,00 euros</p>
        <p>
          RESERVE DE PROPRIETE : Nous nous réservons la propriété des
          marchandises jusqu'au complet paiement du prix par l'acheteur.
        </p>
        <p>
          Notre droit de revendication porte aussi bien sur les marchandises que
          sur leur prix si elles ont déjà été revendues. (Loi du 12/05/1980)
        </p>
      </div>
    </div>
  </template>
</template>

<script>
export default {
  props: ["bill_id", "payment_method", "hide_btn_print", "subscription_id"],
  data() {
    return {
      bill: {},
      print: null
    };
  },
  beforeMount() {
    this.print = this.$page.props.print;
  },
  mounted() {
    this.getBillDetail();
  },
  methods: {
    getBillDetail() {
      axios
        .get(route("api.bill.detail", { bill_id: this.bill_id }))
        .then(response => {
          this.bill = response.data.data;
          this.bill.user = this.bill.user ? this.bill.user : {};
          if (this.print == true) {
            setTimeout(() => {
              window.print();
            }, 1000);
          }
        });
    }
  }
};
</script>

<style lang="scss">
.invoice {
  padding: 40px 20px;

  .logo-ple {
    height: 4cm;
  }

  .first-column-colored tr > th:first-child,
  table th,
  table td {
    border-top: none;
  }

  table thead {
    text-align: center;
  }

  table tbody tr > * {
    padding: 0.25rem !important;
  }

  thead > tr {
    background-color: #eeeeee !important;
  }

  @media print {
    thead > tr {
      background-color: #eeeeee !important;
    }
  }

  thead tr > * {
    border: 2px solid black !important;
  }

  table tr > * {
    border-left: 2px solid black !important;
    border-right: 2px solid black !important;
    vertical-align: middle;
  }

  table tr:last-child > * {
    border-bottom: 2px solid black !important;
  }

  .invoice_title {
    border: 2px solid black;
  }

  .invoice__client__info {
    border: 1px solid black;
  }

  @media (min-width: 1200px) {
    .invoice__designation_columns {
      min-width: 300px;
    }
  }

  .active_subscription_view {
    background-color: #ffe6e3 !important;
  }

  .total-amount > td {
    height: 95px !important;
    font-size: 1.2rem;
  }

  .invoice__footer p {
    margin-bottom: 0.2rem;
  }

  .badg-danger {
    background-color: red;
    padding: 3px;
    font-size: 18px;
    color: white;
  }

  .badg-success {
    background-color: green;
    padding: 3px;
    font-size: 18px;
    color: white;
  }
}
</style>
