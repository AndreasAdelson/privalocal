<template>
  <div class="quoteModal">
    <div
      id="quoteModal"
      class="modal fade"
      data-backdrop="static"
      data-keyboard="false"
      tabindex="-1"
      role="dialog"
      aria-labelledby="quoteModal"
      aria-hidden="true"
    >
      <div
        class="modal-dialog modal-xl modal-quote-centered"
        role="document"
      >
        <div>
          <div class="modal-content w-100">
            <div class="modal-header border-bototm-0">
              <h5
                id="answerOpportunityLabel"
                class="modal-title"
              >
                {{ $t('opportunity.quote.modal_answer.title') }}<br> <span
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
                  <fieldset class="messageEmail">
                    <label>{{ $t('opportunity.quote.modal_answer.your_answer') }}</label>
                    <textarea
                      v-model="formFields.messageEmail"
                      v-validate="'required'"
                      class="form-control"
                      :placeholder="'Votre réponse'"
                      name="messageEmail"
                      :rows="8"
                    />
                    <div
                      :class="!$getNbCharactersLeft(formFields.messageEmail, 1500) ? 'red-skb' : 'black-skb'"
                      class="text-right pt-2 fontSize12"
                    >
                      {{ $tc('commons.n_charaters_left', $getNbCharactersLeft(formFields.messageEmail, 1500)) }}
                    </div>
                    <div
                      v-for="errorText in formErrors.messageEmail"
                      :key="'messageEmail_' + errorText"
                      class="line-height-1"
                    >
                      <span class="fontSize10 red-skb">{{ errorText }}</span>
                    </div>
                  </fieldset>
                </div>
              </div>
              <div class="row mt-2">
                <div class="col-4">
                  <div class="form-group mb-0">
                    <fieldset
                      id="file"
                      class="file"
                    >
                      <input
                        ref="file"
                        name="file"
                        type="file"
                        accept="application/pdf"
                        @change="onFileSelected"
                      >
                      <div
                        v-for="errorText in formErrors.file"
                        :key="'file_' + errorText"
                      >
                        <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                      </div>
                      <div
                        v-if="errorMessage"
                        :key="'file_' + errorMessage"
                      >
                        <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorMessage }}</span>
                      </div>
                    </fieldset>
                  </div>
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
      // ConfirmModal
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
        loading: false,
        CONFIRM_MODAL_ID: 'ANSWER_OPPORTUNITY_MODAL',
        formFields: {
          messageEmail: '',
          _token: null
        },
        formErrors: {
          messageEmail: [],
          quote: [],
          file: []
        },
        emptyModalFields: {
          messageEmail:'',
          company: null,
        },
        quoteDocumentSelected: null,
        documentName: null,
        errorMessage: null
      };
    },
    watch: {
      opportunity(newValue) {
        if (newValue) {
          if(newValue.title && this.company && this.company.name) {
            this.formFields.messageEmail = this.$t('opportunity.customer.default_message', [
              newValue.title,
              this.company.name
            ]);

          }
        }

      },

      company(newValue) {
        if (newValue) {
          if(newValue.name && this.opportunity && this.opportunity.title) {
            this.formFields.messageEmail = this.$t('opportunity.customer.default_message', [
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
          // this.formFields = _.cloneDeep(this.emptyFormFields);
          this.$emit('cancel-form');
        }, 150);
      },

      onCancelButton() {
        this.resetForm();
      },

      onFileSelected() {
        this.quoteDocumentSelected = this.$refs.file.files[0];
        this.documentName = this.$refs.file.files[0].name;
      },

      async submitForm() {
        this.$removeFormErrors();
        EventBus.$emit('answer-modal-submit-called');
        this.formFields.utilisateur = _.cloneDeep(this.utilisateurId);
        this.formFields._token = _.cloneDeep(this.token);
        let formData = this.$getFormFieldsData(this.formFields);
        if (this.quoteDocumentSelected) {
          formData.append('file', this.quoteDocumentSelected);
        }
        return axios.post('/api/answers/quote/' + this.opportunity.answers[0].id  + '/send/'+ this.utilisateurId, formData).then(response => {
          window.location.assign(response.headers.location);
        }).catch(e => {
          if (e.response && e.response.status && e.response.status === 400) {
            if (e.response.headers['x-message']) {
              this.errorMessage = decodeURIComponent(e.response.headers['x-message']);
            } else {
              this.$handleFormError(e.response.data);
            }
          } else {
            this.$handleError(e);
          }
        });
      },
    },

  };
</script>
