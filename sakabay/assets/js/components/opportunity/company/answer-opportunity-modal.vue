<template>
  <div class="answerOpportunityModal">
    <div
      :id="modalId"
      class="modal fade"
      data-backdrop="static"
      data-keyboard="false"
      tabindex="-1"
      role="dialog"
      aria-labelledby="answerOpportunityModal"
      aria-hidden="true"
    >
      <div
        class="modal-dialog modal-xl modal-opportunity-centered"
        role="document"
      >
        <div>
          <div class="modal-content w-100">
            <div class="modal-header border-bototm-0">
              <h5
                id="answerOpportunityLabel"
                class="modal-title"
              >
                {{ $t('opportunity.customer.modal_answer.title') }}<br> <span
                  v-if="opportunity && opportunity.title"
                  class="bold"
                >{{ opportunity.title }}</span>
              </h5>
              <button
                type="button"
                class="close"
                data-dismiss="modal"
                aria-label="Close"
                @click="onCancelButton()"
              >
                <span aria-hidden="true">×</span>
              </button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-12">
                  <fieldset class="message">
                    <label>{{ $t('opportunity.customer.modal_answer.your_answer') }}</label>
                    <textarea
                      v-model="formFields.message"
                      v-validate="'required'"
                      class="form-control"
                      :placeholder="'Votre réponse'"
                      name="message"
                      :rows="8"
                    />
                    <div
                      :class="!$getNbCharactersLeft(formFields.message, 1500) ? 'red-skb' : 'black-skb'"
                      class="text-right pt-2 fontSize12"
                    >
                      {{ $tc('commons.n_charaters_left', $getNbCharactersLeft(formFields.message, 1500)) }}
                    </div>
                    <div
                      v-for="errorText in formErrors.message"
                      :key="'message_' + errorText"
                      class="line-height-1"
                    >
                      <span class="fontSize10 red-skb">{{ errorText }}</span>
                    </div>
                  </fieldset>
                </div>
              </div>
            </div>
            <div
              class="modal-footer border-top-0"
            >
              <div>
                <button
                  type="button"
                  class="btn btn-secondary"
                  data-dismiss="modal"
                  @click="$emit('cancel-form')"
                >
                  <span class="whitetxt">{{ $t('opportunity.cancel') }}</span>
                </button>
              </div>
              <div>
                <button
                  type="button"
                  class="btn btn-success"
                  data-dismiss="modal"
                  @click="submitForm()"
                >
                  <span class="whitetxt">{{ $t('opportunity.validate') }}</span>
                </button>
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
  import { EventBus } from 'plugins/eventBus';

  export default {
    components: {
    },
    props: {
      opportunity: {
        type: Object,
        default: () => new Object()
      },
      company: {
        type: Object,
        default: () => new Object()
      },
      modalId: {
        type: String,
        default: ''
      },
      token: {
        type: String,
        default: ''
      }
    },
    data() {
      return {
        loading: false,
        formFields: {
          message: '',
          besoin: null,
          company: null,
          _token: null
        },
        formErrors: {
          message: [],
          besoin: [],
          company: []
        },
        emptyFormFields: {
          message: '',
          besoin: null,
          company: null,
          _token: null
        }
      };
    },
    watch: {
      opportunity(newValue) {
        if (newValue) {
          this.formFields.besoin = _.cloneDeep(newValue);
          if(newValue.title && this.company && this.company.name) {
            this.formFields.message = this.$t('opportunity.customer.default_message', [
              newValue.title,
              this.company.name
            ]);
          }
        }

      },

      company(newValue) {
        if (newValue) {
          this.formFields.company = _.cloneDeep(newValue);
          if(newValue.name && this.opportunity && this.opportunity.title) {
            this.formFields.message = this.$t('opportunity.customer.default_message', [
              this.opportunity.title,
              newValue.name
            ]);
          }
        }
      }
    },
    methods: {
      resetForm() {
        setTimeout(() => {
          this.loading = false;
          this.formFields = _.cloneDeep(this.emptyFormFields);
          // this.initialFormFields = _.cloneDeep(this.emptyFormFields);
          this.$removeFormErrors();
          this.$emit('cancel-form');
        }, 150);
      },

      onCancelButton() {
        this.resetForm();
      },

      async submitForm() {
        EventBus.$emit('answer-modal-submit-called');
        this.formFields._token = _.cloneDeep(this.token);
        let formData = this.$getFormFieldsData(this.formFields);
        return axios.post('/api/answer', formData).then(response => {
          EventBus.$emit('answer-modal-submited', {id: this.opportunity.id, value: true});
        }).catch(e => {
          if (e.response && e.response.status && e.response.status == 400) {
            this.$handleFormError(e.response.data);
          } else {
            this.$handleError(e);
          }
          this.$emit('answer-modal-submit-error');
        });
      }
    },

  };
</script>
