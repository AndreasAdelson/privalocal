<template>
  <div class="skb-body container ">
    <div v-if="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div v-else>
      <div
        v-if="canEdit"
        class="row mt-3 "
      >
        <div class="col-2 offset-10 justify-content-end">
          <a
            class="float-right"
            :href="'/admin/entreprise/edit/' + companyId"
          >
            <b-button class="button_skb">{{ $t('commons.edit') }}</b-button>
          </a>
        </div>
      </div>
      <a @click="goBack()">
        <button
          :title="$t('commons.go_back')"
          type="button"
          class="w-40px mt-4 p-0 rounded-circle btn-close btn"
        >
          <font-awesome-icon :icon="['fas', 'times']" />
        </button>
      </a>
      <div class="register-card mt-3 w-100 h-100">
        <!-- Name and url name -->
        <div class="row">
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('company.table.fields.name') }}</span>
          </div>
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('company.table.fields.url_name') }}</span>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ company.name }}</span>
          </div>
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ company.url_name }}</span>
          </div>
        </div>

        <!-- User username and category -->
        <div class="row">
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('company.table.fields.utilisateur') }}</span>
          </div>
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('company.table.fields.category') }}</span>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-6">
            <a :href="'/admin/utilisateur/show/' + company.utilisateur.id">
              <span class="fontHelveticaOblique fontSize18 link">{{ company.utilisateur.username }}</span>
            </a>
          </div>
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ company.category.name }}</span>
          </div>
        </div>
        <!-- Statut  -->
        <div class="row">
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('company.table.fields.statut') }}</span>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ company.company_statut.name }}</span>
          </div>
        </div>

        <!-- Postal adress and postal code -->
        <div class="row">
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('company.table.fields.address.postal_address') }}</span>
          </div>
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('company.table.fields.address.postal_code') }}</span>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ company.address.postal_address }}</span>
          </div>
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ company.address.postal_code }}</span>
          </div>
        </div>

        <!-- Latitude and longitude -->
        <div class="row">
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('company.table.fields.address.latitude') }}</span>
          </div>
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('company.table.fields.address.longitude') }}</span>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ company.address.latitude }}</span>
          </div>
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ company.address.longitude }}</span>
          </div>
        </div>

        <!-- Map -->
        <div class="row mb-2">
          <div class="col-10 mx-auto">
            <v-map
              :zoom="16"
              :center="[company.address.latitude, company.address.longitude]"
              style="height:300px"
            >
              <v-marker :lat-lng="[company.address.latitude, company.address.longitude]" />
              <v-tile-layer url="http://{s}.tile.osm.org/{z}/{x}/{y}.png" />
            </v-map>
          </div>
        </div>
        <div class="row">
          <span class="fontPatua fontSize20">{{ $t('company.fields.subscriptions') }}</span>
        </div>
        <div
          class="row scroll-h300 white-bg-skb"
        >
          <vuescroll>
            <div class="col-12">
              <history-item
                :company-subscriptions="companySubscriptions"
                :with-company-name="false"
                :can-edit="canEditSubscription"
              />
            </div>
          </vuescroll>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import axios from 'axios';
  import HistoryItem from '../../../../commons/history-item.vue';
  import vuescroll from 'vuescroll';
  import _ from 'lodash';

  export default {
    components: {
      HistoryItem,
      vuescroll
    },
    props: {
      companyId: {
        type: Number,
        default: null
      },
      canEdit: {
        type: Boolean,
        default: false
      },
      canEditSubscription: {
        type: Boolean,
        default: false
      },
      urlPrecedente: {
        type: String,
        default: null
      }
    },
    data() {
      return {
        company: null,
        loading: true,
        companySubscriptions: []
      };
    },
    watch: {
      company(newValue) {
        this.companySubscriptions = newValue.company_subscriptions;
      }
    },
    async created() {
      if (this.companyId) {
        this.loading = true;
        return axios.get('/api/admin/companies/' + this.companyId)
          .then(response => {
            this.company = response.data;
            this.loading = false;
          }).catch(error => {
            this.$handleError(error);
            this.loading = false;
          });
      }
    },
    methods: {
      goBack() {
        this.$goTo(this.urlPrecedente);
      },
    },
  };
</script>>
