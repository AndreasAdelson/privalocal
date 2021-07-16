<template>
  <div class="container-fluid skb-body p-4 dashboard">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div class="row justify-content-around mb-4">
      <div class="col-2" />
      <div class="col-6">
        <div class="row navigation-opportunities">
          <button
            class="btn col-4 py-2 links"
            :class="customerList ? 'active': ''"
            @click="customerListCliqued()"
          >
            <font-awesome-icon
              class="grey-skb fontSize24 mr-2"
              :icon="['fas', 'child']"
            /><span>Particuliers</span>
          </button>
          <button
            class="btn col-4 py-2 links"
            :class="companyList ? 'active': ''"
            @click="companyListCliqued()"
          >
            <font-awesome-icon
              class="grey-skb fontSize24 mr-2"
              :icon="['fas', 'city']"
            /><span>Entreprises</span>
          </button>
          <button
            class="btn col-4 py-2 links"
            :class="quoteList ? 'active': ''"
            @click="quoteListCliqued()"
          >
            <font-awesome-icon
              class="grey-skb fontSize24 mr-2"
              :icon="['fas', 'edit']"
            /><span>Devis</span>
          </button>
        </div>
      </div>
      <div class="col-2">
        <fieldset
          id="companySelected"
          class="companySelected"
        >
          <multiselect
            v-model="companySelected"
            :disabled="onlyOne"
            :options="companies"
            name="companySelected"
            :searchable="false"
            :close-on-select="true"
            :show-labels="false"
            label="name"
            track-by="name"
          />
        </fieldset>
      </div>
    </div>
    <customer-list
      v-show="customerList"
      :utilisateur-id="utilisateurId"
      :company-selected="companySelected"
      :token="token"
    />
    <company-list
      v-show="companyList"
      :utilisateur-id="utilisateurId"
      :company-selected="companySelected"
      :token="token"
    />
    <quote-list
      v-show="quoteList"
      :company-selected="companySelected"
    />
  </div>
</template>
<script>
  import axios from 'axios';
  import _ from 'lodash';
  import CustomerList from './customer/index.vue';
  import QuoteList from './quote/index.vue';
  import CompanyList from './company/index.vue';

  export default {
    components: {
      CustomerList,
      QuoteList,
      CompanyList
    },
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
        customerList: true,
        companyList: false,
        quoteList: false,
        loading: true,
        loading2: false,
        companySelected: null,
        companies: [],
        onlyOne: true,
      };
    },
    async created() {
      this.setPageByUrlParameter();
      let promises = [];
      promises.push(axios.get('/api/companies/utilisateur/' + this.utilisateurId, {
        params: {
          onlySubscribed: true
        }
      }));
      return Promise.all(promises).then(res => {
        this.companies = _.cloneDeep(res[0].data);
        if (this.companies.length > 0) {
          this.companySelected = this.companies[0];
          if (this.companies.length > 1) {
            this.onlyOne =  false;
          }
        }
        this.loading = false;
      }).catch(e => {
        this.$handleError(e);
        this.loading = false;
      });
    },
    methods: {
      customerListCliqued() {
        this.companyList = false;
        this.quoteList = false;
        this.customerList = true;
      },

      companyListCliqued() {
        this.quoteList = false;
        this.customerList = false;
        this.companyList = true;
      },

      quoteListCliqued() {
        this.customerList = false;
        this.companyList = false;
        this.quoteList = true;
      },

      setPageByUrlParameter() {
        let page = this.getUrlParameter('page');
        if (page === 'quote') {
          this.quoteListCliqued();
        } else if (page === 'company') {
          this.companyListCliqued();
        } else if (page === 'customer') {
          this.customerListCliqued();
        } else {
          this.customerListCliqued();
        }
      },
      getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
        for (i = 0; i < sURLVariables.length; i++) {
          sParameterName = sURLVariables[i].split('=');

          if (sParameterName[0] === sParam) {
            return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
          }
        }
        return false;
      }
    }
  };
</script>
