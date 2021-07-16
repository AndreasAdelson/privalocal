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
          :icon="['fas', 'child']"
        />
        <h1 class="text-center fontPoppins fontSize20 dashboard-title">
          {{ $t('opportunity.customer.title_list') }} <span class="fontPoppins fontSize12 py-1 px-2 orange-gradiant white-skb rounded">{{ nbResult }}</span>
        </h1>
      </div>
    </div>
    <div class="opportunity-list">
      <div class="row">
        <div class="col-10 mx-auto">
          <div class="row">
            <div class="col-12">
              <opportunity-card
                v-for="(opportunity, index) in printedEntities"
                :key="'opp_' + index"
                :opportunity="opportunity"
                :index="index"
                :modal-id="MODAL_ID"
                class="row mb-2 card"
                @modal-openned="currentOpportunity = opportunity"
              />
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
                v-observe-visibility="(isVisible,entry) => throttledScroll(isVisible,API_URL, params,entry, 'customer')"
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
          </div>
        </div>
      </div>
    </div>
    <answer-opportunity-modal
      :opportunity="currentOpportunity"
      :opportunity-title="currentOpportunity ? currentOpportunity.title : null"
      :company="companySelected"
      :company-name="companySelected ? companySelected.name : null"
      :modal-id="MODAL_ID"
      :token="token"
      @cancel-form="currentOpportunity = new Object()"
    />
  </div>
</template>
<script>

  import axios from 'axios';
  import _ from 'lodash';
  import OpportunityCard from './opportunity-card.vue';
  import AnswerOpportunityModal from './answer-opportunity-modal.vue';
  import { EventBus } from 'plugins/eventBus';
  import searchAndScrollMixin from 'mixins/searchAndScrollMixin';

  export default {
    components: {
      OpportunityCard,
      AnswerOpportunityModal
    },
    mixins: [searchAndScrollMixin],
    props: {
      utilisateurId: {
        type: Number,
        default: null
      },
      companySelected: {
        type: Object,
        default: null
      },
      token: {
        type: String,
        default: ''
      }
    },

    data() {
      return {
        API_URL: '/api/opportunities',
        MODAL_ID: 'ANSWER_OPPORTUNITY_MODAL',
        loading: false,
        loading2: false,
        firstAttempt: true,
        opsButton: {
          bar: {
            keepShow: true,
            minSize: 0.3,
          },
          rail: {
            background: '#c5c9cc',
            opacity: 0.4,
            size: '6px',
            specifyBorderRadius: false,
            gutterOfEnds: '1px',
            gutterOfSide: '2px',
            keepShow: false
          },
        },
        printedEntities: [],
        category: null,
        sousCategories: null,
        isScrolling: false,
        bottom: false,
        nbResult: 0,
        nbMaxResult: 10,
        currentPage: 2,
        currentOpportunity: null
      };
    },
    computed: {
      params() {
        let params = {};
        params.category = this.category;
        params.sousCategory = this.sousCategories;
        params.currentPage = this.currentPage;
        return params;
      }
    },
    watch: {
      companySelected(newValue) {
        this.$nextTick(() => {
          this.sousCategories = [];
          this.category =  _.clone(newValue.category.code);
          if (newValue.sous_categorys.length > 0) {
            this.sousCategories = _.clone(_.map(newValue.sous_categorys, 'code'));
          }
          this.resetSearch();
          this.getOpportunities();
        });
      }
    },
    async created() {
      EventBus.$on('answer-modal-submited', async event => {
        let index = _.findIndex(this.printedEntities, opportunity => {
          return opportunity.id === event.id;
        });
        await this.$nextTick(() => {
          console.log(index);
          this.printedEntities[index].isAnswered = event.value;
          this.printedEntities[index].answers.push({
            request_quote: false
          });
        });
      });
    },
    methods: {
      getOpportunities() {
        let promises = [];
        this.loading = true;
        promises.push(axios.get(this.API_URL, {
          params: {
            category: this.category,
            sousCategory: this.sousCategories,
            company: false
          }
        }));
        if (this.firstAttempt) {
          promises.push(axios.get(this.API_URL, {
            params: {
              category: this.category,
              sousCategory: this.sousCategories,
              company: false,
              count: true
            }
          }));
        }
        return Promise.all(promises).then(res => {
          this.loading = false;
          this.printedEntities = this.checkAnswer(_.cloneDeep(res[0].data));
          if (this.printedEntities.length < this.nbMaxResult) {
            this.bottom = true;
          }
          if (this.firstAttempt) {
            this.nbResult = _.cloneDeep(res[1].data);
            this.firstAttempt = false;
          }
        }).catch(e => {
          this.$handleError(e);
          this.loading = false;
        });
      },

      /**
       * Check if the selected company already answer to the opportunity
       * Return the same array with the isAnswered field completed
       * @param {Array} opportunities
       */
      checkAnswer(opportunities) {
        opportunities.forEach(opportunity => {
          let companyAnswer = _.find(opportunity.answers, answer => {
            return answer.company.id === this.companySelected.id;
          });
          if (companyAnswer) {
            opportunity.isAnswered = true;
          } else {
            opportunity.isAnswered = false;
          }
        });

        return opportunities;
      },
    }
  };
</script>
