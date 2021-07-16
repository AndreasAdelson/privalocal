<template>
  <div class="container-fluid skb-body p-4 dashboard">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div class="row mb-4">
      <div class="col-4">
        <font-awesome-icon
          class="grey-skb fontSize24 mr-2"
          :icon="['fas', 'file-alt']"
        />
        <h1 class="text-center fontPoppins fontSize20 dashboard-title">
          {{ $t('opportunity.quote.recap.title') }}
        </h1>
      </div>
    </div>
    <div
      v-if="opportunity"
      class="row"
    >
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col-12">
                <div class="title-card-skb border">
                  <div class="border-bottom mb-2">
                    <div class="row justify-content-between mb-3 align-items-center">
                      <div class="col-6 ">
                        <div class="ml-2">
                          <span class="bold">{{ $capitalise(opportunity.title) }}</span>
                        </div>
                      </div>
                      <div class="col-2 text-right black-light-skb fontSize14">
                        <span>{{ $getDateLabelFromNow(opportunity.dt_created) }}</span>
                      </div>
                    </div>
                    <div class="row">
                      <div class="col-12 mx-auto">
                        <nl2br
                          class-name="ml-1"
                          tag="p"
                          :text="opportunity.description"
                        />
                      </div>
                    </div>
                  </div>
                  <div class="row mb-1">
                    <div class="col-6">
                      <span class="underline">Votre Réponse</span>
                    </div>
                  </div>
                  <div :class="opportunity.answers[0].quote ? 'border-bottom mb-2' : ''">
                    <div class="row">
                      <div class="col-12 mx-auto">
                        <nl2br
                          class-name="ml-1"
                          tag="p"
                          :text="opportunity.answers[0].message"
                        />
                      </div>
                    </div>
                  </div>
                  <!-- <div v-if="opportunity.answers[0].quote">
                    <div class="row">
                      <div class="col-12">
                      </div>
                    </div>
                  </div> -->
                </div>
                <div
                  v-if="!opportunity.answers[0].quote && opportunity.besoin_statut.code === 'PUB'"
                  class="row mt-3"
                >
                  <div class="col-4 mx-auto">
                    <button
                      data-toggle="modal"
                      data-target="#quoteModal"
                      class="btn button_skb_yellow"
                    >
                      {{ $t('besoin.send_quote') }}
                    </button>
                  </div>
                </div>
                <div
                  v-else-if="!opportunity.answers[0].quote && opportunity.besoin_statut.code !== 'PUB'"
                  class="row mt-3"
                >
                  <div class="col-4 mx-auto">
                    <button
                      class="btn btn_skb_green_disabled"
                    >
                      <font-awesome-icon
                        class="mr-2"
                        :icon="['fas', 'book-dead']"
                      />
                      {{ $t('besoin.expirated') }}
                    </button>
                  </div>
                </div>
                <div
                  v-else-if="opportunity.answers[0].quote"
                  class="row mt-3"
                >
                  <div class="col-4 mx-auto">
                    <button
                      class="btn btn_skb_green_disabled"
                    >
                      <font-awesome-icon
                        class="mr-2"
                        :icon="['fas', 'paper-plane']"
                      />
                      {{ $t('besoin.quote_sent') }}
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <quote-modal
      :opportunity="opportunity"
      :company="company"
      :utilisateur-id="utilisateurId"
      :token="token"
    />
  </div>
</template>
<script>
  import axios from 'axios';
  import _ from 'lodash';
  import QuoteModal from './quote-modal.vue';

  export default {
    components: {
      QuoteModal
    },
    props:{
      opportunityId: {
        type:Number,
        default: null
      },
      companyId: {
        type: Number,
        default: null
      },
      utilisateurId: {
        type: Number,
        default: null
      },
      token: {
        type: String,
        default: ''
      }
    },
    data() {
      return {
        loading: true,
        opportunity: null,
        company: null,
      };
    },
    computed: {
      pdfUrl() {
        if (this.opportunity && this.opportunity.answers[0].quote && this.company) {
          // require('/build/images/uploads');
          return ('/build/images/uploads/' + this.company.url_name + '/' + this.opportunity.answers[0].quote).toString();
        }
        return '';
      }
    },
    created() {
      let promises = [];
      promises.push(axios.get('/api/opportunities/recap/' + this.opportunityId + '/' + this.companyId));
      promises.push(axios.get('/api/companies/' + this.companyId));
      return Promise.all(promises).then(res => {
        this.opportunity = _.cloneDeep(res[0].data);
        this.company = _.cloneDeep(res[1].data);
        this.loading = false;
      }).catch(e => {
        this.$handleError(e);
        this.loading = false;
      });
    },
    methods: {

    },
  };
</script>
