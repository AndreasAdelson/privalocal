<template>
  <div class="container-fluid skb-body p-4 dashboard">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <!-- <div class="row pt-4 mb-4">
      <div class="col">
        <a
          href="/dashboard"
          class="btn btn-back py-2"
        >
          <font-awesome-icon :icon="['fas', 'fa-long-arrow-alt-left']" />
        </a>
      </div>
    </div> -->
    <div class="row justify-content-between pt-4 mb-4">
      <div class="col-4 align-self-center">
        <font-awesome-icon
          class="grey-skb fontSize20 mr-2"
          :icon="['fas', 'city']"
        />
        <h1 class="text-center fontPoppins fontSize20 dashboard-title">
          {{ $tc('company.title_list_user', companies.length) }}
        </h1>
      </div>
    </div>
    <div
      v-for="(company, index) in companies"
      :key="'company_' + index"
      class="row"
    >
      <div class="col-12">
        <div class="card">
          <div class="row card-title">
            <div class="col-12 ">
              <div class="row">
                <div class="col-12 mt-3 card-main-info-2">
                  <h3 class="fontPoppins fontW600 mb-0">
                    {{ company.name }}
                  </h3>
                </div>
              </div>
            </div>
          </div>
          <div class="row card-body">
            <div class="col-12">
              <b-img
                v-if="company.image_profil"
                :src="'/build/images/uploads/' +company.url_name + '/' + company.image_profil"
                class="company-image-2"
              />
              <b-img
                v-else
                src="/build/default_company.jpg"
                class="company-image-2"
              />
              <div class="row">
                <div
                  class="col-9"
                >
                  <!-- Category & Address & Statut -->
                  <div class="row justify-content-between">
                    <div class="col-3 fontSize18 fontPoppins">
                      <font-awesome-icon
                        class=" mr-1"
                        :icon="['fas', 'city']"
                      />
                      <span class="bold">{{ $t('company.card.label.category') }}</span>
                    </div>
                    <div class="col-3 fontSize18 fontPoppins">
                      <font-awesome-icon
                        class=" mr-1"
                        :icon="['fas', 'building']"
                      />
                      <span class="bold">{{ $t('company.card.label.address') }}</span>
                    </div>
                    <div class="col-3 fontSize18 fontPoppins">
                      <font-awesome-icon
                        class=" mr-1"
                        :icon="['fas', 'inbox']"
                      />
                      <span class="bold">{{ $t('company.card.label.statut') }}</span>
                      <!-- TODO rajouter tooltip d'info sur le traitement des inscriptions. -->
                      <span class="link fontSize10 cursor-pointer">{{ $t('commons.more_details') }}</span>
                    </div>
                  </div>
                  <div class="row mb-3 justify-content-between">
                    <div
                      v-if="company && company.category"
                      class="col-3"
                    >
                      <span class="fontSize16">{{ company.category.name }}</span>
                    </div>
                    <div
                      v-if="company && company.address"
                      class="col-3"
                    >
                      <span class="fontSize16">{{ company.address.postal_address }}</span>
                    </div>
                    <div
                      v-if="company && company.company_statut"
                      :class="getStatutsClass(company.company_statut.code)"
                      class="col-3"
                    >
                      <font-awesome-icon :icon="['fas', getStatutsLabel(company.company_statut.code)[1]]" />

                      <span class="bold fontSize16">{{ getStatutsLabel(company.company_statut.code)[0] }}</span>
                    </div>
                  </div>

                  <!-- Num siret & url name & date de validation -->
                  <div class="row justify-content-between">
                    <div class="col-3 fontSize18 fontPoppins">
                      <font-awesome-icon
                        class=" mr-1"
                        :icon="['fas', 'calendar']"
                      />

                      <span class="bold">{{ $t('company.card.label.dt_created') }}</span>
                    </div>
                    <div class="col-3 fontSize18 fontPoppins">
                      <font-awesome-icon
                        class=" mr-1"
                        :icon="['fas', 'map-pin']"
                      />
                      <span class="bold">{{ $t('company.card.label.code') }}</span>
                    </div>
                    <div class="col-3 fontSize18 fontPoppins">
                      <font-awesome-icon
                        class=" mr-1"
                        :icon="['fas', 'calendar-alt']"
                      />
                      <span class="bold">{{ $t('company.card.label.dt_fin') }}</span>
                    </div>
                  </div>
                  <div class="row mb-3 justify-content-between">
                    <div class="col-3">
                      <span class="fontSize16">{{ getCreationDateLabel(company.dt_created) }}</span>
                    </div>
                    <div
                      v-if="company && company.address"
                      class="col-3"
                    >
                      <span class="fontSize16">{{ company.address.postal_code }}</span>
                    </div>
                    <div
                      v-if="company && company_subscriptions"
                      class="col-3"
                    >
                      <span class="fontSize16">{{ getSubscriptionDateLabel(company.company_subscriptions) }}</span>
                    </div>
                  </div>

                  <!-- Address & postal code & city -->
                  <div class="row justify-content-between">
                    <div class="col-3 fontSize18 fontPoppins">
                      <font-awesome-icon
                        class=" mr-1"
                        :icon="['fas', 'info-circle']"
                      />
                      <span class="bold">{{ $t('company.card.label.siret') }}</span>
                    </div>
                    <div class="col-3 fontSize18 fontPoppins">
                      <font-awesome-icon
                        class=" mr-1"
                        :icon="['fas', 'city']"
                      />
                      <span class="bold">{{ $t('company.card.label.city') }}</span>
                    </div>
                    <div class="col-3 fontSize18 fontPoppins">
                      <font-awesome-icon
                        class=" mr-1"
                        :icon="['fas', 'desktop']"
                      />
                      <span class="bold">{{ $t('company.card.label.url_name') }}</span>
                    </div>
                  </div>
                  <div class="row mb-3 justify-content-between">
                    <div class="col-3">
                      <span class="fontSize16">{{ company.num_siret }}</span>
                    </div>
                    <div
                      v-if="company && company.city"
                      class="col-3"
                    >
                      <span class="fontSize16">{{ company.city.name }}</span>
                    </div>
                    <div class="col-3">
                      <span class="fontSize16">{{ company.url_name }}</span>
                    </div>
                  </div>
                </div>
                <div
                  v-if="company && company.company_statut && company.company_statut.code === 'VAL'"
                  class="col-3"
                >
                  <div
                    class="row mb-1"
                  >
                    <div class="col-12 fontSize18 fontPoppins">
                      <font-awesome-icon
                        class=" mr-1"
                        :icon="['fas', 'cubes']"
                      />
                      <span class="bold">{{ $t('company.card.label.sous_category') }}</span>
                    </div>
                  </div>
                  <div
                    v-if="company && company.sous_categorys && company.sous_categorys.length != 0 "
                    class="row mb-2"
                  >
                    <div class="col-12">
                      <span
                        v-for="(sousCategory, index2) in company.sous_categorys"
                        :key="'sousCategory_' + index2"
                        class="multiselect__tag_no_icon"
                      >{{ sousCategory.name }}</span>
                    </div>
                  </div>
                </div>
              </div>
              <div
                v-if="company && company.company_statut && company.company_statut.code === 'VAL'"
                class="row my-2"
              >
                <div class="col-10 offset-1">
                  <div class="row align-items-center">
                    <div class="col-2">
                      <a
                        :href="'/entreprise/' + company.url_name"
                        class="btn button_skb_blue mb-1"
                      >
                        <font-awesome-icon
                          class=" mr-1"
                          :icon="['fas', 'eye']"
                        />
                        {{ $t('commons.presentation') }}
                      </a>
                    </div>
                    <div class="col-2">
                      <a
                        :href="'/entreprise/edit/' + company.url_name"
                        class="btn button_skb_yellow"
                      >
                        <font-awesome-icon
                          class=" mr-1"
                          :icon="['fas', 'edit']"
                        />
                        {{ $t('commons.edit') }}
                      </a>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import axios from 'axios';
  import _ from 'lodash';
  import moment from 'moment';

  export default {
    props: {
      utilisateurId: {
        type: Number,
        default: null
      },
    },
    data() {
      return {
        loading: true,
        companies: [],
      };
    },
    computed: {

    },
    created() {
      let promises = [];
      if (this.utilisateurId) {
        promises.push(axios.get('/api/companies/utilisateur/' + this.utilisateurId));
      }
      return Promise.all(promises).then(res => {
        if (this.utilisateurId) {
          this.companies = _.orderBy(_.cloneDeep(res[0].data), 'dt_created', 'desc');
          this.sortSubscriptions();
        }
        this.loading = false;
      }).catch(error => {
        this.$handleError(error);
        this.loading = false;
      });
    },
    methods: {
      getStatutsClass(companyStatutCode) {
        let cssClass;
        switch(companyStatutCode) {
        case 'VAL':
          cssClass = 'green-skb';
          break;
        case 'ENC':
          cssClass = 'blue-skb';
          break;
        case 'REF':
          cssClass = 'red-skb';
          break;
        }
        return cssClass;
      },

      getStatutsLabel(companyStatutCode) {
        let label;
        let icon;
        switch(companyStatutCode) {
        case 'VAL':
          label = this.$t('company_statut.state.validate');
          icon = 'check';
          break;
        case 'ENC':
          label = this.$t('company_statut.state.pending');
          icon = 'history';
          break;
        case 'REF':
          label = this.$t('company_statut.state.refused');
          icon = 'times';
          break;
        }
        return [label, icon];
      },

      sortSubscriptions() {
        this.companies.forEach(company => {
          company.company_subscriptions = _.orderBy(company.company_subscriptions, [
            function(subscription) {
              let dtFin = moment(subscription.dt_fin, 'DD/MM/YYYY HH:mm:ss').format('MM/DD/YYYY H:mm:ss');
              if (moment(subscription.dt_fin, 'DD/MM/YYYY HH:mm:ss').isAfter()) {
                subscription.isActive = true;
              } else {
                subscription.isActive = false;
              }
              return new Date(dtFin.toString());
            }
          ], ['desc']);
        });
      },

      getSubscriptionDateLabel(companySubscriptions) {
        let labels;
        labels = _.filter(companySubscriptions, subscription => {
          return subscription.isActive;
        });
        if (labels.length > 0) {
          labels = moment(labels[0].dt_fin, 'DD/MM/YYYY HH:mm:ss').format('[le] DD/MM/YYYY, [à] HH:mm');
        } else {
          labels = this.$t('company.presentation.no_subscriptions');
        }
        return labels;
      },

      getCreationDateLabel(date) {
        return moment(date, 'DD/MM/YYYY HH:mm:ss').format('DD/MM/YYYY');
      },
    },
  };
</script>
