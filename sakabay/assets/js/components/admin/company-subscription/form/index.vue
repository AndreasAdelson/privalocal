<template>
  <div class="container skb-body">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <button
      title="Annulez les modifications"
      type="button"
      class="w-40px p-0 rounded-circle btn-close btn"
      @click="goBack()"
    >
      <font-awesome-icon :icon="['fas', 'times']" />
    </button>
    <form>
      <div class="">
        <div class="register-card w-100 h-100">
          <!-- First row  -->
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="dtDebut"
                  class="dtDebut"
                >
                  <label class="fontUbuntuItalic fontSize14">{{ $t('company_subscription.fields.dtDebut') }}</label>
                  <datepicker
                    ref="datepicker_dtDebut"
                    v-model="formFields.dtDebut"
                    v-validate="'required'"
                    :language="$i18n.locale === 'fr' || $i18n.locale === 'fr-FR' ? datePickerLanguages.fr : datePickerLanguages.en"
                    :monday-first="$i18n.locale === 'fr' || $i18n.locale === 'fr-FR'"
                    :typeable="true"
                    :format="customFormatter"
                    input-class="form-control"
                    name="dtDebut"
                  />
                  <div
                    v-for="errorText in formErrors.dtDebut"
                    :key="'dtDebut_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="dtFin"
                  class="dtFin"
                >
                  <label class="fontUbuntuItalic fontSize14">{{ $t('company_subscription.fields.dtFin') }}</label>
                  <datepicker
                    ref="datepicker_dtFin"
                    v-model="formFields.dtFin"
                    v-validate="'required'"
                    :language="$i18n.locale === 'fr' || $i18n.locale === 'fr-FR' ? datePickerLanguages.fr : datePickerLanguages.en"
                    :monday-first="$i18n.locale === 'fr' || $i18n.locale === 'fr-FR'"
                    :format="customFormatter"
                    :typeable="true"
                    name="dtFin"
                    input-class="form-control"
                  />
                  <div
                    v-for="errorText in formErrors.dtFin"
                    :key="'dtFin_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="subscriptionStatus"
                  class="subscriptionStatus"
                >
                  <label class="fontUbuntuItalic fontSize14">{{ $t('company_subscription.fields.subscription_status') }}</label>
                  <multiselect
                    v-model="formFields.subscriptionStatus"
                    v-validate="'required'"
                    :options="subscriptionStatus"
                    name="subscriptionStatus"
                    placeholder="Selectionner categorie"
                    :searchable="false"
                    :close-on-select="false"
                    :show-labels="false"
                    label="name"
                    track-by="name"
                  />
                  <div
                    v-for="errorText in formErrors.subscriptionStatus"
                    :key="'subscriptionStatus_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="stripeId"
                  class="stripeId"
                >
                  <label class="fontUbuntuItalic fontSize14">{{ $t('company_subscription.fields.stripe_id') }}</label>
                  <input
                    v-model="formFields.stripeId"
                    v-validate="'required'"
                    type="text"
                    name="stripeId"
                    class="form-control"
                    :placeholder="$t('company_subscription.placeholder.stripe_id')"
                  >
                  <div
                    v-for="errorText in formErrors.stripeId"
                    :key="'stripeId_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
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
          <div class="row my-3">
            <div class="col-6 offset-3">
              <button
                type="button"
                class="btn button_skb fontUbuntuItalic"
                @click="$validateForm(urlPrecedente)"
              >
                {{ $t('commons.edit') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
</template>
<script>
  import axios from 'axios';
  import validatorRulesMixin from 'mixins/validatorRulesMixin';
  import adminFormMixin from 'mixins/adminFormMixin';
  import Datepicker from 'vuejs-datepicker';
  import {en, fr} from 'vuejs-datepicker/dist/locale';
  import _ from 'lodash';
  import moment from 'moment';

  export default {
    components: {
      Datepicker
    },
    mixins: [
      validatorRulesMixin,
      adminFormMixin
    ],
    props: {
      companySubscriptionId: {
        type: Number,
        default: null,
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
        loading: true,
        API_URL: '/api/admin/company-subscription/' + this.companySubscriptionId,
        fonctionsAtCreation: null,
        formFields: {
          dtDebut: null,
          dtFin: null,
          subscriptionStatus: null,
          stripeId: null,
          _token: null
        },
        formErrors: {
          dtDebut: [],
          dtFin: [],
          subscriptionStatus: [],
          stripeId: [],
        },
        datePickerLanguages: {
          fr: fr,
          en: en,
        },
        subscriptionStatus: [],
        errorMessage: null
      };
    },
    created() {
      let promises = [];
      promises.push(axios.get('/api/admin/subscription-status'));
      if (this.companySubscriptionId) {
        promises.push(axios.get(this.API_URL));
      }
      return Promise.all(promises).then(res => {
        this.subscriptionStatus = _.cloneDeep(res[0].data);
        if (this.companySubscriptionId) {
          let companySubscription = res[1].data;
          this.$removeFieldsNotInForm(companySubscription, Object.keys(this.formFields));
          this.$setEditForm(companySubscription);
        }
        this.loading = false;
      }).catch(e => {
        this.$handleError(e);
        this.loading = false;
      });
    },
    methods: {
      goBack() {
        this.$goTo(this.urlPrecedente);
      },
      customFormatter(date) {
        return moment(date).format('DD/MM/YYYY, HH:mm');
      },
      dateFormat() {
        console.log('closed');
        if (!moment(this.formFields.dtFin).isValid()) {
          this.formFields.dtFin = moment().toDate();
        }
      },
      input(date) {
        console.log(Date.parse(date));
      },
      blur() {
        console.log('blur');
      }
    },
  };
</script>
