<template>
  <div class="container skb-body">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div>
      <div class="row mt-2">
        <div class="col-3 offset-9">
          <fieldset
            id="company"
            class="company"
          >
            <multiselect
              v-model="formFields.company"
              :disabled="onlyOne"
              :options="companies"
              name="company"
              :searchable="false"
              :close-on-select="true"
              :show-labels="false"
              label="name"
              track-by="name"
              :allow-empty="false"
            />
          </fieldset>
        </div>
      </div>

      <div
        v-if="subscription"
        class="row px-3 py-3 pt-md-5 pb-md-4 mx-auto"
      >
        <div class="card-deck col-md-12">
          <div
            class="card mb-2 "
          >
            <div class="card-header text-center">
              <h3 class="my-0 font-weight-normal">
                {{ subscription.name }}
              </h3>
            </div>
            <div class="card-body">
              <h1 class="card-title pricing-card-title text-center">
                {{ subscription.price }} €<small class="text-muted">/ mois</small>
              </h1>
              <!--
                Using a label with a for attribute that matches the ID of the
                Element container enables the Element to automatically gain focus
                when the customer clicks on the label.
              -->
              <div
                v-if="defaultPaymentMethod"
                class="payment-method-list row"
              >
                <div class="col-12">
                  <span>{{ $t('subscription.default_payment_method') }}</span>
                  <div class="row">
                    <div class="col-8 mx-auto">
                      <div
                        class="card"
                      >
                        <div class="row">
                          <div class="col-9 text-center">
                            <span>
                              {{ $getPaymentMethodLabel(defaultPaymentMethod) }}
                            </span>
                          </div>
                          <div class="col-2 offset-1">
                            <button
                              class="link btn"
                              @click="editDefaultPayment()"
                            >
                              {{ $t('commons.edit') }}
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                  <div
                    v-if="edit"
                    class="row"
                  >
                    <div class="col-12">
                      <div class="card">
                        <form
                          id="payment-form"
                          class="row"
                        >
                          <div class="col-12">
                            <div class="form-group">
                              <label for="iban-element">
                                IBAN
                              </label>
                              <div
                                id="iban-element"
                                class="form-control"
                              >
                                <!-- Stripe Element here -->
                              </div>
                              <span
                                v-if="errorMessage"
                                id="error-message"
                                role="alert"
                                class="fontUbuntuItalic fontSize13 red-skb"
                              >
                                {{ errorMessage }}
                              </span>
                              <fieldset
                                class="fingerprint"
                              >
                                <input
                                  v-model="paymentMethodForm.fingerprint"
                                  v-validate="'required'"
                                  type="text"
                                  name="fingerprint"
                                  class="form-control"
                                  :hidden="true"
                                >
                                <div
                                  v-for="errorText in formErrors.fingerprint"
                                  :key="'fingerprint_' + errorText"
                                >
                                  <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                                </div>
                              </fieldset>
                            </div>
                          </div>
                        </form>
                        <div class="row mb-2">
                          <div class="col-4 mx-auto">
                            <button
                              :data-secret="clientSecret"
                              type="submit"
                              :class="!isCompleted ? 'button_skb_yellow_disabled' : 'button_skb_yellow'"
                              class="btn"
                              :disabled="!isCompleted"
                              @click="assignNewDefaultPayment()"
                            >
                              {{ $t('subscription.assign_default') }}
                            </button>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <form
                v-else
                id="payment-form"
              >
                <div class="row">
                  <div class="col-12">
                    <div class="form-group">
                      <label for="iban-element">
                        IBAN
                      </label>
                      <div
                        id="iban-element"
                        class="form-control"
                      >
                      <!-- Stripe Element here -->
                      </div>
                      <span
                        v-if="errorMessage"
                        id="error-message"
                        role="alert"
                        class="fontUbuntuItalic fontSize13 red-skb"
                      >
                        {{ errorMessage }}
                      </span>
                      <fieldset
                        class="fingerprint"
                      >
                        <input
                          v-model="paymentMethodForm.fingerprint"
                          v-validate="'required'"
                          type="text"
                          name="fingerprint"
                          class="form-control"
                          :hidden="true"
                        >
                        <div
                          v-for="errorText in formErrors.fingerprint"
                          :key="'fingerprint_' + errorText"
                        >
                          <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                        </div>
                      </fieldset>
                    </div>
                  </div>
                </div>
              </form>
              <!-- Message for reload subscription -->
              <div
                v-if="companyGetActiveSubscription && !stripeId"
                class="mb-2"
              >
                <!-- defaultPaymentMethod condition -->
                <span
                  v-if="defaultPaymentMethod"
                  class="red-skb bold"
                >{{ $t('subscription.subscription_cancelled_reload', [$getDateLabel(activeCompanySubscription.dt_fin)]) }}</span>
                <span
                  v-else
                  class="red-skb bold"
                >{{ $t('subscription.subscription_cancelled_continue', [$getDateLabel(activeCompanySubscription.dt_fin)]) }}</span>
              </div>
              <div
                v-else-if="companyGetActiveSubscription && stripeId"
                class="mb-2"
              >
                <!-- Message for paid/unpaid subscription -->
                <span
                  v-if="companyGetActiveSubscription && subscriptionStatusCode === 'VAL'"
                  class="red-skb bold"
                >{{ $t('subscription.already_subscribed_paid', [$getDateLabel(activeCompanySubscription.dt_fin)]) }}</span>
                <span
                  v-else-if="companyGetActiveSubscription && subscriptionStatusCode === 'ENC'"
                  class="red-skb bold"
                >{{ $t('subscription.already_subscribed_pending', [$getDateLabel(activeCompanySubscription.dt_fin)]) }}</span>
              </div>


              <div
                id="mandate-acceptance"
                class="fontSize10 mb-2"
              >
                {{ $t('subscription.mandate_acceptance') }}
              </div>
              <div v-if="!companyGetActiveSubscription">
                <button
                  :data-secret="clientSecret"
                  type="submit"
                  :class="isButtonActive ? 'button_skb_yellow_disabled' : 'button_skb_yellow'"
                  class="btn"
                  :disabled="isButtonActive"
                  @click="subscribe(false)"
                >
                  {{ $t('subscription.sign_up') }}
                </button>
              </div>
              <div v-else-if="companyGetActiveSubscription && !stripeId">
                <button
                  v-if="defaultPaymentMethod"
                  :data-secret="clientSecret"
                  type="submit"
                  class="btn button_skb_yellow"
                  @click="subscribeWithDefaultPaymentMethod()"
                >
                  {{ $t('subscription.continue_subscription') }}
                </button>
                <button
                  v-else
                  :data-secret="clientSecret"
                  type="submit"
                  :class="isButtonActive ? 'button_skb_yellow_disabled' : 'button_skb_yellow'"
                  class="btn"
                  :disabled="isButtonActive"
                  @click="subscribe(true)"
                >
                  {{ $t('subscription.continue_subscription') }}
                </button>
              </div>
              <div v-else-if="companyGetActiveSubscription && stripeId">
                <button
                  :data-secret="clientSecret"
                  type="submit"
                  :class="subscriptionStatusCode === 'ENC' ? 'button_skb_blue_disabled' : 'button_skb_blue'"
                  class="btn "
                  :disabled="subscriptionStatusCode === 'ENC'"
                  @click="cancelSubscription()"
                >
                  {{ $t('subscription.cancel_subscription') }}
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <confirm-modal
      :id="CONFIRM_SUBSCRIPTION"
      :title-text="$t('commons.confirm_modal.subscription_validate.validate.title')"
      :body-text="$t('commons.confirm_modal.subscription_validate.validate.text')"
      :button-yes-text="$t('commons.yes')"
      :button-no-text="$t('commons.no')"
      :are-buttons-on-same-line="true"
      @confirm-modal-yes="cancelSubscription()"
    />
  </div>
</template>

<script>

  import axios from 'axios';
  import moment from 'moment';
  import ConfirmModal from 'components/commons/confirm-modal';
  import _ from 'lodash';
  export default {
    components: {
      ConfirmModal,
    },
    props: {
      utilisateurId: {
        type: Number,
        default: null
      },
      subscriptionName: {
        type: String,
        default: null
      },
      token: {
        type: String,
        default: null
      },
    },
    data() {
      return {
        stripeAPIToken: process.env.STRIPE_PUBLISHABLE_KEY,
        CONFIRM_SUBSCRIPTION: 'confirm_subscription_modal',
        loading: true,
        API_URL: '/api/subscribes',
        formFields: {
          dtDebut: moment().format('YYYY/MM/DD H:m:s'),
          dtFin: moment().format('YYYY/MM/DD H:m:s'),
          company: new Object(),
          subscription: new Object(),
          stripeId: null,
          isTrial: null,
          _token: null
        },
        formErrors: {
          dtDebut: [],
          dtFin: [],
          company: [],
          subscription: [],
          stripeId: [],
          fingerprint: [],
        },
        companies: [],
        onlyOne: true,
        subscription: null,
        ibanOptions: {
          supportedCountries: ['SEPA'],
          placeholderCountry: 'FR',
        },
        clientSecret: null,
        iban: null,
        stripe: '',
        elements: '',
        companyIsloaded: false,
        errorMessage: null,
        isCompleted : false,
        paymentMethodForm: {
          stripeId: null,
          company: null,
          fingerprint: null,
          subscription: null,
          _token: null
        },
        firstCall: true,
        edit: false,
        companyGetActiveSubscription: false,
        stripeId: null,
        subscriptionStatusCode: null
      };
    },
    computed: {
      companySelected() {
        return this.formFields.company;
      },
      isButtonActive() {
        if (this.isCompleted) {
          return false;
        } else {
          return true;
        }
      },
      activeCompanySubscription() {
        if (this.companyIsloaded) {
          let companySubscriptions = this.formFields.company.company_subscriptions;
          if (companySubscriptions) {
            companySubscriptions.sort(function(a, b) {
              return (a.stripe_id === null) - (b.stripe_id === null);
            });
            let subscription = companySubscriptions.find((item) => {
              return moment(item.dt_fin, 'DD/MM/YYYY HH:mm:ss').isAfter();
            });
            if(subscription) {
              return subscription;
            }
          }
        }
        return null;
      },
      defaultPaymentMethod() {
        if (this.companyIsloaded) {
          let companyPaymentMethods = this.formFields.company.payment_methods;
          if (companyPaymentMethods.length > 0) {
            let defaultPM = companyPaymentMethods.find((item) => {
              return item.default_method == true;
            });
            if (defaultPM) {
              return defaultPM;
            }
          }
        }
        return null;
      }
    },
    watch: {
      companySelected() {
        this.getStripeIntent();
        this.edit = false;
        if (!this.defaultPaymentMethod) {
          setTimeout(() => {
            this.setIbanInput();
          }, 500);
        }
      },
      activeCompanySubscription(newValue) {
        if (newValue) {
          this.companyGetActiveSubscription = true;
          this.stripeId = newValue.stripe_id;
          this.subscriptionStatusCode = newValue.subscription_status.code;
        } else {
          this.companyGetActiveSubscription = false;
          this.stripeId = null;
          this.subscriptionStatusCode = null;
        }
      }
    },
    mounted() {
      this.includeStripe('js.stripe.com/v3/', function(){
        this.configureStripe();
      }.bind(this) );
    },
    created() {
      let promises = [];
      promises.push(axios.get('/api/subscribes/' + this.subscriptionName));
      promises.push(axios.get('/api/companies/utilisateur/' + this.utilisateurId));

      return Promise.all(promises).then(response => {
        this.subscription = _.cloneDeep(response[0].data);
        this.formFields.subscription = _.cloneDeep(response[0].data);
        this.companies = _.cloneDeep(_.filter(response[1].data, function(company) {
          return company.company_statut.code === 'VAL';
        }));
        this.formFields.company = _.cloneDeep(response[1].data[0]);
        if (this.companies.length > 1) {
          this.onlyOne = false;
        }
        this.companyIsloaded = true;
      }).catch(error => {
        this.$handleError(error);
        this.loading = false;
      });
    },
    methods: {
      includeStripe( URL, callback ){
        let documentTag = document, tag = 'script',
            object = documentTag.createElement(tag),
            scriptTag = documentTag.getElementsByTagName(tag)[0];
        object.src = '//' + URL;
        if (callback) { object.addEventListener('load', function(e) { callback(null, e); }, false); }
        scriptTag.parentNode.insertBefore(object, scriptTag);
      },
      configureStripe(){
        this.stripe = Stripe(this.stripeAPIToken);
      },
      getStripeIntent() {
        this.loading = true;
        return axios.get('/api/intent/' + this.companySelected.id)
          .then (response => {
            this.clientSecret = response.data;
            this.loading = false;
          }).catch(e => {
            this.$handleError(e);
          });
      },
      setSubscription() {
        this.formFields._token = _.cloneDeep(this.token);
        let formData = this.$getFormFieldsData(this.formFields);
        return axios.post(this.API_URL, formData)
          .then(response => {
            window.location.assign(response.headers.location);
          }).catch(error => {
            this.$handleError(error);
            this.loading = false;
          });
      },
      subscribe($isOffer) {
        this.loading = true;
        this.stripe.confirmSepaDebitSetup(
          this.clientSecret,
          {
            payment_method: {
              sepa_debit: this.iban,
              billing_details: {
                name: this.companySelected.name,
                email: this.companySelected.email,
              },
            },
          }
        ).then(response => {
          this.paymentMethodForm.stripeId = response.setupIntent.payment_method;
          this.paymentMethodForm.company = this.companySelected.id;
          this.paymentMethodForm.subscription = _.cloneDeep(this.subscription);
          //Create a trial for those who subscribed when they are still in offer subscription.
          if ($isOffer && this.activeCompanySubscription) {
            this.paymentMethodForm.dtStart = moment(this.activeCompanySubscription.dt_fin, 'DD/MM/YYYY H:mm:ss').format('YYYY/MM/DD H:m:s');
          }
          this.paymentMethodForm._token = _.cloneDeep(this.token);
          let formData = this.$getFormFieldsData(this.paymentMethodForm);
          axios.post('/api/default-payment', formData)
            .then(result => {
              if ($isOffer && this.activeCompanySubscription) {
                this.formFields.dtDebut = moment(this.activeCompanySubscription.dt_fin, 'DD/MM/YYYY H:mm:ss').format('YYYY/MM/DD H:m:s');
                this.formFields.dtFin = moment(this.activeCompanySubscription.dt_fin, 'DD/MM/YYYY H:mm:ss').add(1,'M').format('YYYY/MM/DD H:m:s');
              } else {
                this.formFields.dtDebut = moment(result.data._values.current_period_start * 1000).format('YYYY/MM/DD H:m:s');
                this.formFields.dtFin = moment(result.data._values.current_period_end * 1000).format('YYYY/MM/DD H:m:s');
              }
              this.formFields.stripeId = result.data._values.id;
              this.formFields.isTrial = false;
              this.setSubscription();
            }).catch(e => {
              if (e.response && e.response.status && e.response.status === 400) {
                if (e.response.headers['x-message']) {
                  this.errorMessage = decodeURIComponent(e.response.headers['x-message']);
                } else  {
                  this.$handleFormError(e.response.data);
                }
              }
              else if (e.response && e.response.status && e.response.status === 500) {
                this.errorMessage = decodeURIComponent(e.response.data.message);
              } else {
                this.$handleError(e);
              }
              this.loading = false;
              this.getStripeIntent();
            });
        });
      },
      cancelSubscription() {
        this.loading = true;
        axios.post('/api/cancel-subscription', {
          company: this.companySelected.id
        })
          .then(response => {
            window.location.assign(response.headers.location);
          }).catch(e => {
            if (e.response && e.response.status && e.response.status === 400) {
              if (e.response.headers['x-message']) {
                this.errorMessage = decodeURIComponent(e.response.headers['x-message']);
              } else  {
                this.$handleFormError(e.response.data);
              }
            }
            else if (e.response && e.response.status && e.response.status === 500) {
              this.errorMessage = decodeURIComponent(e.response.data.message);
            } else {
              this.$handleError(e);
            }
            this.loading = false;
          });
      },
      subscribeWithDefaultPaymentMethod() {
        this.loading = true;
        this.stripe.confirmSepaDebitSetup(
          this.clientSecret,
          {
            payment_method: this.defaultPaymentMethod.stripe_id
          }).then(response => {
          this.paymentMethodForm.company = this.companySelected.id;
          // Revoir condition pour dtStart ici
          if (this.activeCompanySubscription && moment(this.activeCompanySubscription.dt_fin, 'DD/MM/YYYY HH:mm:ss').isAfter()) {
            this.paymentMethodForm.dtStart = moment(this.activeCompanySubscription.dt_fin, 'DD/MM/YYYY H:m:s').format('X');
          }
          this.paymentMethodForm.subscription = _.cloneDeep(this.subscription);
          let formData = this.$getFormFieldsData(this.paymentMethodForm);
          axios.post('/api/payment', formData)
            .then(result => {
              this.formFields.dtFin = moment(result.data._values.current_period_end * 1000).format('YYYY/MM/DD H:m:s');
              this.formFields.dtDebut = moment(result.data._values.current_period_start * 1000).format('YYYY/MM/DD H:m:s');
              this.formFields.stripeId = result.data._values.id;
              if (this.activeCompanySubscription && !this.activeCompanySubscription.stripe_id) {
                this.formFields.isTrial = true;
              } else {
                this.formFields.isTrial = false;
              }
              this.setSubscription();
            }).catch(e => {
              if (e.response && e.response.status && e.response.status === 400) {
                if (e.response.headers['x-message']) {
                  this.errorMessage = decodeURIComponent(e.response.headers['x-message']);
                } else  {
                  this.$handleFormError(e.response.data);
                }
              }
              else if (e.response && e.response.status && e.response.status === 500) {
                this.errorMessage = decodeURIComponent(e.response.data.message);
              } else {
                this.$handleError(e);
              }
              this.loading = false;
              this.getStripeIntent();
            });
        });
      },
      setIbanInput() {
        var style = {
          base: {
            color: '#32325d',
            fontSize: '16px',
            '::placeholder': {
              color: '#aab7c4'
            },
            ':-webkit-autofill': {
              color: '#32325d',
            },
          },
          invalid: {
            color: '#fa755a',
            iconColor: '#fa755a',
            ':-webkit-autofill': {
              color: '#fa755a',
            },
          },
        };

        var options = {
          style: style,
          supportedCountries: ['SEPA'],
          placeholderCountry: 'FR',
        };
        this.elements = this.stripe.elements();
        this.iban = this.elements.create('iban', options);
        this.iban.mount('#iban-element');
        this.iban.on('change', function(event) {
          if (event.error) {
            this.errorMessage = event.error.message;
          } else {
            this.errorMessage = null;
          }
          if (event.complete) {
            this.isCompleted = true;
          } else {
            this.isCompleted = false;
          }

        }.bind(this));
      },
      sortNullishValues(array) {
        const assignValue = val => {
          if(!val){
            return Infinity;
          }
          else{
            return val;
          }
        };
        const sorter = (a, b) => {
          return assignValue(a.stripe_id) - assignValue(b.stripe_id);
        };
        array.sort(sorter);
      },
      editDefaultPayment() {
        this.edit = !this.edit;
        if (this.edit) {
          this.$nextTick(() => {
            this.setIbanInput();
          });
        }
      },
      assignNewDefaultPayment() {
        this.loading = true;
        this.stripe.createPaymentMethod({
          type: 'sepa_debit',
          sepa_debit: this.iban,
          billing_details: {
            name: this.companySelected.name,
            email: this.companySelected.email,
          },
        }).then(response => {
          console.log(response.paymentMethod.id);
          this.paymentMethodForm.stripeId = response.paymentMethod.id;
          this.paymentMethodForm.company = this.companySelected.id;
          this.paymentMethodForm.subscriptionName = this.subscriptionName;
          let formData = this.$getFormFieldsData(this.paymentMethodForm);
          axios.post('/api/update-payment-method', formData)
            .then(result => {
              window.location.assign(result.headers.location);
            }).catch(e => {
              if (e.response && e.response.status && e.response.status === 400) {
                if (e.response.headers['x-message']) {
                  this.errorMessage = decodeURIComponent(e.response.headers['x-message']);
                } else  {
                  this.$handleFormError(e.response.data);
                }
              }
              else if (e.response && e.response.status && e.response.status === 500) {
                this.errorMessage = decodeURIComponent(e.response.data.message);
              } else {
                this.$handleError(e);
              }
              this.loading = false;
              this.getStripeIntent();
            });
        });
      }
    }
  };
</script>
