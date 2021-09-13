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
                            <div
                              v-show="ibanActive"
                              class="form-group"
                            >
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

                            <div
                              v-show="cardActive"
                              class="form-group"
                            >
                              <div id="card-element">
                                <!--Stripe.js injects the Card Element-->
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
                              :data-secret="clientSetupSecret"
                              type="submit"
                              :class="!isIbanCompleted ? 'button_skb_yellow_disabled' : 'button_skb_yellow'"
                              class="btn"
                              :disabled="!isIbanCompleted"
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
              <div v-else>
                <div class="row navigation-menu card-skb2">
                  <div class="col-3 mx-auto">
                    <button
                      :class="cardActive ? 'navigation-link-active' : 'navigation-link'"
                      @click="activeCard"
                    >
                      CARTE
                    </button>
                    <button
                      :class="ibanActive ? 'navigation-link-active' : 'navigation-link'"
                      @click="activeIban"
                    >
                      IBAN
                    </button>
                  </div>
                </div>
                <form

                  id="payment-form"
                >
                  <div
                    class="row"
                  >
                    <div class="col-6 mx-auto mt-3">
                      <div
                        v-show="ibanActive"
                        class="form-group"
                      >
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

                      <div
                        v-show="cardActive"
                        class="form-group"
                      >
                        <div id="card-element">
                        <!--Stripe.js injects the Card Element-->
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
              </div>
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
                  :data-secret="clientSetupSecret"
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
                  :data-secret="clientSetupSecret"
                  type="submit"
                  class="btn button_skb_yellow"
                  @click="subscribeWithDefaultPaymentMethod()"
                >
                  {{ $t('subscription.continue_subscription') }}
                </button>
                <button
                  v-else
                  :data-secret="clientSetupSecret"
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
                  :data-secret="clientSetupSecret"
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
        clientSetupSecret: null,
        clientPaymentSecret: null,
        iban: null,
        card: null,
        stripe: '',
        elements: '',
        companyIsloaded: false,
        errorMessage: null,
        isIbanCompleted : false,
        isCardCompleted : false,
        paymentMethodForm: {
          stripeId: null,
          company: null,
          fingerprint: null,
          subscription: null,
          _token: null,
          type: null
        },
        firstCall: true,
        edit: false,
        companyGetActiveSubscription: false,
        stripeId: null,
        subscriptionStatusCode: null,
        ibanActive: false,
        cardActive: false
      };
    },
    computed: {
      companySelected() {
        return this.formFields.company;
      },
      isButtonActive() {
        if (this.isIbanCompleted || this.isCardCompleted) {
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
        this.edit = false;
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
        this.loading = false;
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
      getStripeSetupIntent() {
        this.loading = true;
        return axios.get('/api/setup-intent/' + this.companySelected.id)
          .then (response => {
            this.clientSetupSecret = response.data;
          }).catch(e => {
            this.$handleError(e);
            this.loading = false;
          });
      },

      getStripeIntent() {
        this.loading = true;
        return axios.get('/api/intent/' + this.companySelected.id,{params: {price_id: this.subscription.stripe_id}})
          .then (response => {
            this.clientPaymentSecret = response.data;
          }).catch(e => {
            this.$handleError(e);
            this.loading = false;
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
      async subscribe(isOffer) {
        this.loading = true;
        let promises = [];
        if(this.ibanActive) {
          await this.getStripeSetupIntent();
          promises.push(this.stripe.confirmSepaDebitSetup(
            this.clientSetupSecret,
            {
              payment_method: {
                sepa_debit: this.iban,
                billing_details: {
                  name: this.companySelected.name,
                  email: this.companySelected.email,
                },
              },
            }
          ));
        } else if (this.cardActive) {
          await this.getStripeIntent();
          promises.push(this.stripe
            .confirmCardPayment(this.clientPaymentSecret, {
              payment_method: {
                card: this.card
              }
            }));
        }
        Promise.all(promises).then(response => {
          if (this.ibanActive) {
            this.createDefaultPaymentAndSubscribe(response, isOffer, 'iban');
          }
          else if (this.cardActive) {
            this.createDefaultPaymentAndSubscribe(response, isOffer, 'card');
          }

        }).catch(e => {
          this.$handleError(e);
          this.loading = false;
        });
      },
      cancelSubscription() {
        this.loading = true;
        axios.post('/api/cancel-subscription', {
          company: this.companySelected.id,
          _token: this.token
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
          this.clientSetupSecret,
          {
            payment_method: this.defaultPaymentMethod.stripe_id
          }).then(response => {
          this.paymentMethodForm.company = this.companySelected.id;
          // Revoir condition pour dtStart ici
          if (this.activeCompanySubscription && moment(this.activeCompanySubscription.dt_fin, 'DD/MM/YYYY HH:mm:ss').isAfter()) {
            this.paymentMethodForm.dtStart = moment(this.activeCompanySubscription.dt_fin, 'DD/MM/YYYY H:m:ss').format('X');
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
            this.isIbanCompleted = true;
          } else {
            this.isIbanCompleted = false;
          }

        }.bind(this));
      },
      setCardInput() {
        const cardStyle = {
          style: {
            base: {
              color: '#32325d',
              fontFamily: 'Arial, sans-serif',
              fontSmoothing: 'antialiased',
              fontSize: '16px',
              '::placeholder': {
                color: '#32325d'
              }
            },
            invalid: {
              color: '#fa755a',
              iconColor: '#fa755a'
            }
          }
        };
        this.elements = this.stripe.elements();
        this.card = this.elements.create('card', cardStyle);
        // Stripe injects an iframe into the DOM
        this.card.mount('#card-element');
        this.card.on('change', function(event) {
          if (event.error) {
            this.errorMessage = event.error.message;
          } else {
            this.errorMessage = null;
          }
          if (event.complete) {
            this.isCardCompleted = true;
          } else {
            this.isCardCompleted = false;
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
            });
        });
      },
      activeIban() {
        this.ibanActive = true;
        this.cardActive = false;
        this.errorMessage = null;
        this.setIbanInput();
      },
      activeCard() {
        this.ibanActive = false;
        this.cardActive = true;
        this.errorMessage = null;
        this.setCardInput();
      },

      createDefaultPaymentAndSubscribe(response, isOffer, paymentType) {
        this.loading = true;
        if(this.ibanActive) {
          this.paymentMethodForm.stripeId = response[0].setupIntent.payment_method;

        } else if(this.cardActive) {
          this.paymentMethodForm.stripeId = response[0].paymentIntent.payment_method;
        }

        this.paymentMethodForm.company = this.companySelected.id;
        this.paymentMethodForm.subscription = _.cloneDeep(this.subscription);
        //Create a trial for those who subscribed when they are still in offer subscription.
        if (isOffer && this.activeCompanySubscription) {
          this.paymentMethodForm.dtStart = moment(this.activeCompanySubscription.dt_fin, 'DD/MM/YYYY H:mm:ss').format('X');
        }
        this.paymentMethodForm.type = paymentType;
        this.paymentMethodForm._token = _.cloneDeep(this.token);
        let formData = this.$getFormFieldsData(this.paymentMethodForm);
        axios.post('/api/default-payment', formData)
          .then(result => {
            if (isOffer && this.activeCompanySubscription) {
              this.formFields.dtDebut = moment(this.activeCompanySubscription.dt_fin, 'DD/MM/YYYY H:mm:ss').format('YYYY/MM/DD H:m:s');
              this.formFields.dtFin = moment(this.activeCompanySubscription.dt_fin, 'DD/MM/YYYY H:mm:ss').add(1,'M').format('YYYY/MM/DD H:m:ss');
            } else {
              this.formFields.dtDebut = moment(result.data._values.current_period_start * 1000).format('YYYY/MM/DD H:m:ss');
              this.formFields.dtFin = moment(result.data._values.current_period_end * 1000).format('YYYY/MM/DD H:m:ss');
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
          });
      }
    }
  };
</script>
