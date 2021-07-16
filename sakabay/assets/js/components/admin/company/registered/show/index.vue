<template>
  <div class="skb-body container ">
    <div v-if="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div v-else-if="!loading && company.company_statut.code === 'VAL'">
      <div class="row pt-5">
        <div class="col">
          <p class="text-center">
            {{ $t('company.already_validate') }}
          </p>
        </div>
      </div>
    </div>
    <div v-else-if="!loading && company.company_statut.code === 'REF'">
      <div class="row pt-5">
        <div class="col">
          <p class="text-center">
            {{ $t('company.already_refused') }}
          </p>
        </div>
      </div>
    </div>
    <div v-else-if="!loading && company.company_statut.code === 'ENC'">
      <div
        v-if="canEdit"
        class="row mt-3 "
      >
        <div
          class="col-2 offset-10 justify-content-end"
        >
          <a
            class="float-right"
            :href="'/admin/registered/entreprise/edit/' + companyId"
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
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('company.table.fields.city') }}</span>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ company.company_statut.name }}</span>
          </div>
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ company.city.name }}</span>
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

        <!-- Message alerte longitude et latitude à corriger -->
        <div
          v-if="company.address.latitude === 0 && company.address.longitude === 0"
          class="row mb-2"
        >
          <div class="col-12">
            <span class="fontPatua fontSize14 red-skb">{{ $t('company.table.fields.address.error_geoloc') }}</span>
          </div>
        </div>

        <!-- Map -->
        <div class="row">
          <div class="col-12">
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
      </div>
      <span
        v-if="errorMessage"
        id="error-message"
        role="alert"
        class="fontUbuntuItalic fontSize13 red-skb"
      >
        {{ errorMessage }}
      </span>
      <div
        v-if="company.company_statut.code === 'ENC'"
        class="row justify-content-center my-3"
      >
        <div class="col-2">
          <b-button
            data-toggle="modal"
            :data-target="'#' + DECLINE_CONFIRM_MODAL_ID"
            class="btn button_skb"
          >
            {{ $t('commons.decline') }}
          </b-button>
        </div>
        <div
          class="col-2"
        >
          <b-button
            :disabled="company.address.latitude === 0 && company.address.longitude === 0"
            data-toggle="modal"
            :data-target="'#' + VALIDATE_CONFIRM_MODAL_ID"
            class="btn btn-success w-100"
          >
            {{ $t('commons.validate') }}
          </b-button>
        </div>
      </div>
    </div>
    <confirm-modal
      :id="DECLINE_CONFIRM_MODAL_ID"
      :title-text="$t('commons.confirm_modal.decline.company.title')"
      :body-text="$t('commons.confirm_modal.decline.company.text')"
      :button-yes-text="$t('commons.yes')"
      :button-no-text="$t('commons.no')"
      :are-buttons-on-same-line="true"
      @confirm-modal-yes="declineCompany()"
    />

    <confirm-modal
      :id="VALIDATE_CONFIRM_MODAL_ID"
      :title-text="$t('commons.confirm_modal.validate.company.title')"
      :body-text="$t('commons.confirm_modal.validate.company.text')"
      :button-yes-text="$t('commons.yes')"
      :button-no-text="$t('commons.no')"
      :are-buttons-on-same-line="true"
      @confirm-modal-yes="validateCompany()"
    />
  </div>
</template>
<script>
  import axios from 'axios';
  import ConfirmModal from 'components/commons/confirm-modal';
  import moment from 'moment';
  import _ from 'lodash';

  export default {
    components: {
      ConfirmModal
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
      urlPrecedente: {
        type: String,
        default: null
      },
      token: {
        type: String,
        default: null
      }
    },
    data() {
      return {
        DECLINE_CONFIRM_MODAL_ID: 'decline_confirmModal',
        VALIDATE_CONFIRM_MODAL_ID: 'validate_confirmModal',
        company: null,
        loading: true,
        validateForm: {
          dtDebut: null,
          dtFin: null,
          company: null,
          stripeId: null,
          _token: null
        },
        declineForm: {
          _token: null
        },
        errorMessage: null
      };
    },
    created() {
      if (this.companyId) {
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
      validateCompany() {
        this.loading = true;
        let currentDate = moment(this.validateForm.dtStart);
        let futureMonth= moment(currentDate).add(2, 'M');
        this.validateForm.dtFin = futureMonth.format('YYYY/MM/DD H:m:s');
        this.validateForm.company = this.companyId;
        this.validateForm._token = _.cloneDeep(this.token);

        let formData = this.$getFormFieldsData(this.validateForm);
        return axios.post('/api/admin/companies/' + this.companyId + '/validation', formData)
          .then(response => {
            this.loading = false;
            window.location.assign(response.headers.location);
          }).catch(e => {
            this.loading = false;
            if (e.response && e.response.status && e.response.status === 400) {
              if (e.response.headers && e.response.headers['x-message']) {
                this.errorMessage = decodeURIComponent(e.response.headers['x-message']);
              } else {
                this.$handleFormError(e.response.data);
              }
            } else {
              this.$handleError(e);
            }
          });
      },
      declineCompany() {
        this.loading = true;
        this.declineForm._token = _.cloneDeep(this.token);
        let formData = this.$getFormFieldsData(this.declineForm);

        return axios.post('/api/admin/companies/' + this.companyId + '/decline', formData)
          .then(response => {
            this.loading = false;
            window.location.assign(response.headers.location);
          }).catch(e => {
            this.$handleError(e);
            this.loading = false;
          });
      },
      goBack() {
        this.$goTo(this.urlPrecedente);
      },
    },
  };
</script>>
