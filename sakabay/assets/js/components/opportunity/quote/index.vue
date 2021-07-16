<template>
  <div>
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div class="row justify-content-between mb-4">
      <div class="col-4 align-self-center">
        <font-awesome-icon
          class="grey-skb fontSize24 mr-2"
          :icon="['fas', 'edit']"
        />
        <h1 class="text-center fontPoppins fontSize20 dashboard-title">
          {{ $t('opportunity.quote.title') }} <span class="fontPoppins fontSize12 py-1 px-2 orange-gradiant white-skb rounded">{{ getTotalResult }}</span>
        </h1>
      </div>
    </div>
    <div class="opportunity-list">
      <div class="row">
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="row">
                <div class="col">
                  <div class="title-card-skb ">
                    <span class="underline">{{ $t('opportunity.quote.individual_request') }}</span>
                    <span
                      class="fontPoppins fontSize12 py-1 px-2 orange-gradiant white-skb rounded"
                    >{{ nbResult }}</span>
                  </div>
                </div>
              </div>
              <div v-if="printedEntities.length == 0">
                <div class="row">
                  <div class="col">
                    <p class="text-center mt-3">
                      {{ $t('opportunity.quote.no_individual_request') }}
                    </p>
                  </div>
                </div>
              </div>
              <div
                v-else
                class="row scroll-h500"
              >
                <vuescroll>
                  <div class="col-12">
                    <div
                      v-for="(pendingQuote, index) in printedEntities"
                      :key="'pendingQuote_' + index"
                      :class="setColorCard(pendingQuote)"
                      class="service-card mb-2 px-3 pt-3 border"
                    >
                      <quote-card
                        :pending-quote="pendingQuote"
                        :company-selected="companySelected"
                      />
                    </div>
                    <div
                      class="w-100 whitebg text-center"
                    >
                      <div
                        v-if="isScrolling"
                        v-show="loading2"
                        class="my-5"
                      >
                        <div class="loader4" />
                      </div>
                    </div>
                    <div
                      v-if="printedEntities.length"
                      v-observe-visibility="(isVisible,entry) => throttledScroll(isVisible,API_URL,params,entry, 'quote')"
                      name="spy"
                    />
                    <div
                      v-if="bottom && printedEntities.length > 0"
                      class="text-center pt-4"
                    >
                      <span>{{ $t('opportunity.customer.end_of_results') }}</span>
                    </div>
                    <div
                      v-else-if="bottom && printedEntities.length === 0"
                      class="text-center pt-4"
                    >
                      <span>{{ $t('opportunity.customer.no_opportunity') }}</span>
                    </div>
                  </div>
                </vuescroll>
              </div>
            </div>
          </div>
          <div class="card mt-2">
            <div class="card-body">
              <div class="row">
                <div class="col">
                  <div class="title-card-skb ">
                    <span class="underline">{{ $t('opportunity.quote.company_request') }}</span>
                    <span
                      class="fontPoppins fontSize12 py-1 px-2 orange-gradiant white-skb rounded"
                    >{{ nbResult2 }}</span>
                  </div>
                </div>
              </div>
              <div v-if="printedCompanyQuote.length == 0">
                <div class="row">
                  <div class="col">
                    <p class="text-center mt-3">
                      {{ $t('opportunity.quote.no_company_request') }}
                    </p>
                  </div>
                </div>
              </div>
              <div
                v-else
                class="row scroll-h500"
              >
                <vuescroll>
                  <div class="col-12">
                    <div
                      v-for="(pendingCompanyQuote, index) in printedCompanyQuote"
                      :key="'pendingQuote_' + index"
                      :class="setColorCard(pendingCompanyQuote)"
                      class="service-card mb-2 px-3 pt-3 border"
                    >
                      <quote-card
                        :pending-quote="pendingCompanyQuote"
                        :company-selected="companySelected"
                      />
                    </div>
                    <div
                      class="w-100 whitebg text-center"
                    >
                      <div
                        v-if="isScrolling"
                        v-show="loading3"
                        class="my-5"
                      >
                        <div class="loader4" />
                      </div>
                    </div>
                    <div
                      v-if="printedCompanyQuote.length"
                      v-observe-visibility="(isVisible,entry) => throttledScroll(isVisible,API_URL,params,entry, 'companyQuote')"
                      name="spy"
                    />
                    <div
                      v-if="bottom && printedCompanyQuote.length > 0"
                      class="text-center pt-4"
                    >
                      <span>{{ $t('opportunity.customer.end_of_results') }}</span>
                    </div>
                    <div
                      v-else-if="bottom && printedCompanyQuote.length === 0"
                      class="text-center pt-4"
                    >
                      <span>{{ $t('opportunity.customer.no_opportunity') }}</span>
                    </div>
                  </div>
                </vuescroll>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import searchAndScrollMixin from 'mixins/searchAndScrollMixin';
  import axios from 'axios';
  import _ from 'lodash';
  import vuescroll from 'vuescroll';
  import QuoteCard from './quote-card.vue';

  export default {
    components:{
      vuescroll,
      QuoteCard
    },
    mixins: [searchAndScrollMixin],
    props: {
      companySelected: {
        type: Object,
        default: null
      }
    },
    data() {
      return {
        API_URL: '/api/opportunities/quote',
        loading: false,
        loading2: false,
        loading3: false,
        nbResult: 0,
        nbResult2: 0,
        isScrolling: false,
        bottom: false,
        bottom2: false,
        nbMaxResult: 10,
        currentPage: 2,
        printedEntities: [],
        printedCompanyQuote: [],
        firstAttempt: true,
      };
    },
    computed: {
      params() {
        let params = {};
        params.company = this.companySelected.id;
        params.currentPage = this.currentPage;
        return params;
      },
      getTotalResult() {
        return parseInt(this.nbResult) + parseInt(this.nbResult2);
      }
    },
    watch: {
      companySelected(newValue) {
        this.resetSearch();
        this.getOpportunities();
      }
    },
    methods: {
      getOpportunities() {
        let promises = [];
        this.loading = true;
        promises.push(axios.get('/api/opportunities/quote', {
          params: {
            company: this.companySelected.id,
            onlyCompany: false
          }
        }));
        promises.push(axios.get('/api/opportunities/quote', {
          params: {
            company: this.companySelected.id,
            onlyCompany: true
          }
        }));
        if (this.firstAttempt) {
          promises.push(axios.get('/api/opportunities/quote?count=true', {
            params: {
              company: this.companySelected.id,
              onlyCompany: false

            }
          }));
          promises.push(axios.get('/api/opportunities/quote?count=true', {
            params: {
              company: this.companySelected.id,
              onlyCompany: true
            }
          }));
        }
        return Promise.all(promises).then(res => {
          this.printedEntities = _.cloneDeep(res[0].data);
          this.printedCompanyQuote = _.cloneDeep(res[1].data);
          if (this.printedEntities.length < this.nbMaxResult) {
            this.bottom = true;
          }
          if (this.printedCompanyQuote.length < this.nbMaxResult) {
            this.bottom2 = true;
          }
          if (this.firstAttempt) {
            this.nbResult = _.cloneDeep(res[2].data);
            this.nbResult2 = _.cloneDeep(res[3].data);
            this.firstAttempt = false;
          }
          this.loading = false;
        }).catch(e => {
          this.$handleError(e);
          this.loading = false;
        });
      },
      setColorCard(pendingQuote) {
        let cssClass = '';
        if (pendingQuote.answers && pendingQuote.answers.length > 0) {
          if (!pendingQuote.answers[0].quote && pendingQuote.besoin_statut.code === 'PUB') {
            cssClass = 'yellow-light-bg-skb';
          } else if (!pendingQuote.answers[0].quote && pendingQuote.besoin_statut.code !== 'PUB') {
            cssClass = 'blue-light-bg-skb';
          } else if (pendingQuote.answers[0].quote) {
            cssClass = 'green-light-bg-skb';
          }
        }
        return cssClass;
      },

    },
  };
</script>
