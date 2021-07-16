<template>
  <div class="container-fluid skb-body p-4 dashboard">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div class="row justify-content-between pt-4 mb-4">
      <div class="col-3 align-self-center">
        <font-awesome-icon
          class="grey-skb fontSize24 mr-2"
          :icon="['fas', 'edit']"
        />
        <h1 class="text-center fontPoppins fontSize20 dashboard-title">
          {{ $t('besoin.title_list') }}
        </h1>
      </div>
      <div
        v-if="companies.length > 0"
        class="col-3 mb-2"
      >
        <fieldset
          id="entitySelected"
          class="entitySelected"
        >
          <multiselect
            ref="multiselect"
            v-model="entitySelected"
            :placeholder="$t('besoin.placeholder.select')"
            :disabled="companies.length < 0"
            :options="companies"
            name="entitySelected"
            :searchable="false"
            :close-on-select="true"
            :show-labels="false"
            :custom-label="$getAuthorLabel"
            :allow-empty="false"
            track-by="name"
            @select="onSelectEntity"
          />
        </fieldset>
      </div>
      <div class="col-3 align-self-center">
        <a
          href="/services/new"
          class="button_skb_blue btn py-2"
        >
          <font-awesome-icon :icon="['fas', 'plus-circle']" />
          {{ $t('besoin.create_button') }}
        </a>
      </div>
    </div>
    <div class="row">
      <!-- Demandes en cours -->
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col">
                <div class="title-card-skb ">
                  <span class="underline">{{ $t('besoin.pending_request') }}</span>
                  <span
                    class="fontPoppins fontSize12 py-1 px-2 orange-gradiant white-skb rounded"
                  >{{ nbResult1 }}</span>
                </div>
              </div>
            </div>

            <div
              class="row scroll-h500"
            >
              <vuescroll>
                <div class="col-12">
                  <div
                    v-for="(pendingBesoin, index) in pendingBesoins"
                    :key="'pendingBesoin_' + index"
                    class="service-card mb-2 px-3 pt-3 border"
                  >
                    <pending-besoin
                      :pending-besoin="pendingBesoin"
                      :loading="loadingModal"
                      :modal-manage-id="MANAGE_MODAL_ID"
                      :request-quote-id="REQUEST_QUOTE_MODAL_ID"
                      @manage-modal-opened="onManagePendingService(index, pendingBesoin)"
                      @request-quote-modal-opened="event => onRequestQuotePending(index, pendingBesoin, event)"
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
                    v-if="pendingBesoins.length"
                    v-observe-visibility="(isVisible,entry) => throttledScroll(isVisible,API_URL, paramsPending, entry, 'pendingBesoins')"
                    name="spy"
                  />
                  <div
                    v-if="bottom && pendingBesoins.length > 0"
                    class="text-center pt-4"
                  >
                    <span>{{ $t('opportunity.customer.end_of_results') }}</span>
                  </div>
                  <div
                    v-else-if="bottom && pendingBesoins.length === 0"
                    class="text-center pt-4"
                  >
                    <span>{{ $t('besoin.no_pending_services') }}</span>
                  </div>
                </div>
              </vuescroll>
            </div>
          </div>
        </div>
      </div>
      <!-- Demandes expirées -->
      <div class="col-12 mt-2">
        <div class="card">
          <div class="card-body">
            <div class="row">
              <div class="col">
                <div class="title-card-skb">
                  {{ $t('besoin.expired_request') }}
                  <span
                    class="fontPoppins fontSize12 py-1 px-2 orange-gradiant white-skb rounded"
                  >{{ nbResult2 }}</span>
                </div>
              </div>
            </div>
            <div
              class="row scroll-h500"
            >
              <vuescroll :ops="opsButton">
                <div class="col">
                  <div
                    v-for="(expiredBesoin, index) in expiredBesoins"
                    :key="'expiredBesoin_' + index"
                    class="service-card mb-2 px-3 pt-3 border"
                  >
                    <expired-besoin
                      :expired-besoin="expiredBesoin"
                      @delete-modal-opened="onDeletedExpiredService(index, expiredBesoin.id)"
                    />
                  </div>
                  <div
                    class="w-100 whitebg text-center"
                  >
                    <div
                      v-if="isScrolling2"
                      v-show="loading3"
                      class="my-5"
                    >
                      <div class="loader4" />
                    </div>
                  </div>

                  <div
                    v-if="expiredBesoins.length"
                    v-observe-visibility="(isVisible,entry) => throttledScroll(isVisible, API_URL, paramsExpired, entry, 'expiredBesoins')"
                    name="spy"
                  />
                  <div
                    v-if="bottom2 && expiredBesoins.length > 0"
                    class="text-center pt-4"
                  >
                    <span>{{ $t('opportunity.customer.end_of_results') }}</span>
                  </div>
                  <div
                    v-else-if="bottom2 && expiredBesoins.length === 0"
                    class="text-center pt-4"
                  >
                    <span>{{ $t('besoin.no_expired_services') }}</span>
                  </div>
                </div>
              </vuescroll>
            </div>
          </div>
        </div>
      </div>
    </div>
    <manage-modal
      :id="MANAGE_MODAL_ID"
      :besoin="currentPendingBesoin"
      :besoin-statuts="besoinStatuts"
      :utilisateur-id="utilisateurId"
      :entity-selected="entitySelected"
      :is-company="isCompany"
      :token="token"
    />
    <confirm-modal
      :id="REQUEST_QUOTE_MODAL_ID"
      :title-text="$t('besoin.modal_request_quote.title')"
      :body-text="$t('besoin.modal_request_quote.text', [currentCompanyName, currentPendingBesoinTitle])"
      :nl2br="true"
      :button-yes-text="$t('commons.yes')"
      :button-no-text="$t('commons.no')"
      :are-buttons-on-same-line="true"
      @confirm-modal-yes="submitRequestQuote()"
    />
  </div>
</template>
<script>
  import axios from 'axios';
  import vuescroll from 'vuescroll';
  import PendingBesoin from './pending-besoin/index.vue';
  import ExpiredBesoin from './expired-besoin-item.vue';
  import _ from 'lodash';
  import ManageModal from './pending-besoin/manage-modal.vue';
  import ConfirmModal from 'components/commons/confirm-modal';
  import searchAndScrollMixin from 'mixins/searchAndScrollMixin';
  import { EventBus } from 'plugins/eventBus';

  export default {
    components: {
      vuescroll,
      PendingBesoin,
      ExpiredBesoin,
      ConfirmModal,
      ManageModal
    },
    mixins: [searchAndScrollMixin],

    props: {
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
        API_URL: '/api/besoins/utilisateur/' + this.utilisateurId,
        REQUEST_QUOTE_MODAL_ID: 'request_quoteModal',
        MANAGE_MODAL_ID: 'manage_modal',
        loading: true,
        loading2: false,
        loading3: false,
        loadingModal: false,
        currentPendingId: null,
        indexPending: null,
        currentExpiredId: null,
        indexExpired: null,
        currentPendingBesoinTitle: null,
        currentCompanyName: null,
        currentAnswerId: null,
        currentPendingBesoin: null,
        indexAnswer: null,
        pendingBesoins: [],
        expiredBesoins: [],
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
        isScrolling: false,
        isScrolling2: false,
        bottom: false,
        bottom2: false,
        nbResult1: 0,
        nbResult2: 0,
        nbMaxResult: 10,
        currentPage: 2,
        currentPage2: 2,
        firstAttempt: true,
        companies: [],
        utilisateur: null,
        entitySelected: null,
        entityId: '',
        besoinStatuts: []
      };
    },
    computed: {
      paramsPending() {
        let params = {};
        params.codeStatut = 'PUB';
        params.currentPage = this.currentPage;
        return params;
      },
      paramsExpired() {
        let params = {};
        params.codeStatut = ['ARC', 'EXP'];
        params.currentPage = this.currentPage2;
        return params;
      },
      isCompany() {
        if (this.entitySelected && this.entitySelected.first_name) {
          return false;
        }
        return true;
      }
    },
    created() {
      EventBus.$on('besoin-closed-end', event => {
        this.archiveBesoin();
      });
      EventBus.$on('besoin-deleted', event => {
        this.deleteBesoin();
      });
      let promises = [];
      promises.push(axios.get('/api/besoin-statuts'));
      promises.push(axios.get('/api/companies/utilisateur/' + this.utilisateurId));
      return Promise.all(promises).then(res => {
        this.besoinStatuts = res[0].data;
        this.companies = _.cloneDeep(res[1].data);
        if(this.companies.length > 0) {
          this.utilisateur = _.cloneDeep(this.companies[0].utilisateur);
          this.companies.push(this.utilisateur);
          this.entitySelected = this.utilisateur;
        }
        this.getBesoins();
      }).catch(e => {
        this.$handleError(e);
        this.loading = false;
      });
    },
    methods: {
      onManagePendingService(index, pendingBesoin) {
        this.currentPendingBesoin = pendingBesoin;
        this.indexPending = index;
      },
      // onDeletedExpiredcService(index, expiredBesoinId) {
      //   this.currentExpiredId = expiredBesoinId;
      //   this.indexExpired = index;
      // },
      onRequestQuotePending(index, pendingBesoin, event) {
        this.indexPending = index;
        this.currentPendingBesoinTitle = pendingBesoin.title;
        this.currentCompanyName = event.company_name;
        this.currentAnswerId = event.answer_id;
        this.indexAnswer = event.answer_index;
      },
      submitRequestQuote() {
        this.loadingModal = true;
        axios.post('/api/answers/quote/' + this.currentAnswerId)
          .then(res => {
            this.pendingBesoins[this.indexPending].answers[this.indexAnswer].request_quote = true;
            this.indexPending = null;
            this.indexAnswer = null;
            this.loadingModal = false;

          });
      },
      getBesoins() {
        let promises = [];
        promises.push(axios.get(this.API_URL,{
          params: {
            codeStatut: 'PUB',
            company: this.entityId
          }
        }
        ));
        promises.push(axios.get(this.API_URL,{
          params: {
            codeStatut: ['ARC', 'EXP'],
            company: this.entityId
          }
        }
        ));

        if (this.firstAttempt) {
          promises.push(axios.get(this.API_URL, {
            params: {
              codeStatut: 'PUB',
              count: true,
              company: this.entityId
            }
          }));
          promises.push(axios.get(this.API_URL,  {
            params: {
              codeStatut: ['ARC', 'EXP'],
              count: true,
              company: this.entityId
            }
          }));
        }
        return Promise.all(promises).then(res => {
          this.pendingBesoins = _.cloneDeep(res[0].data);
          this.expiredBesoins = _.cloneDeep(res[1].data);

          if (this.pendingBesoins.length < this.nbMaxResult) {
            this.bottom = true;
          }
          if (this.expiredBesoins.length < this.nbMaxResult) {
            this.bottom2 = true;
          }
          if (this.firstAttempt) {
            this.nbResult1 = parseInt(res[2].data);
            this.nbResult2 = parseInt(res[3].data);
            this.firstAttempt = false;
          }
          this.loading = false;
        }).catch(e => {
          this.$handleError(e);
          this.loading = false;
        });
      },
      onSelectEntity() {
        this.$nextTick(() => {
          if (this.entitySelected) {
            if (this.utilisateur && !_.isEqual(this.entitySelected, this.utilisateur)) {
              this.entityId = this.entitySelected.id;
            } else {
              this.entityId = '';
            }
          }
          this.resetSearch();
          this.getBesoins();
        });
      },
      archiveBesoin() {
        let besoinToArchive = _.pullAt(this.pendingBesoins, this.indexPending);
        this.expiredBesoins.splice(0,0,besoinToArchive[0]);
        this.nbResult1 -= 1;
        this.nbResult2 += 1;
      },
      deleteBesoin() {
        this.pendingBesoins.splice(this.indexPending, 1);
        this.currentPendingId = null;
        this.indexPending = null;
        this.nbResult1 -= 1;
      },
    },

  };
</script>
