<template>
  <div :class="id">
    <div
      :id="id"
      class="modal fade"
      data-backdrop="static"
      data-keyboard="false"
      tabindex="-1"
      role="dialog"
      :aria-labelledby="id"
      aria-hidden="true"
    >
      <div
        class="modal-dialog modal-xl"
        role="document"
      >
        <div v-show="loading">
          <div class="loader-container-full">
            <div class="loader" />
          </div>
        </div>
        <div>
          <div class="modal-content w-100">
            <div class="modal-header border-bototm-0">
              <h5
                class="modal-title"
              >
                {{ $t('besoin.modal_manage.title') }}<br> <span
                  v-if="besoin && besoin.title"
                  class="bold"
                >{{ besoin.title }}</span>
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
              <div
                v-if="errorMessage"
                class="line-height-1"
              >
                <span class="fontSize10 red-skb">{{ errorMessage }}</span>
              </div>
              <div class="row">
                <div
                  class="col-8 mx-auto"
                >
                  <fieldset class="besoinStatut">
                    <multiselect
                      v-model="formFields.besoinStatut"
                      v-validate="'required'"
                      :options="status"
                      :placeholder="$t('besoin.modal_manage.placeholder.besoin_statut')"
                      name="besoinStatut"
                      :searchable="false"
                      :show-labels="false"
                      track-by="code"
                      :allow-empty="false"
                      :custom-label="(entity)=>$t(`besoin.modal_manage.message.${entity.code}` )"
                      :rows="8"
                    />
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
              <div v-if="formFields.besoinStatut && formFields.besoinStatut.code === 'ARC' && companys.length > 0">
                <div
                  class="row"
                >
                  <div class="col-8 mx-auto">
                    <fieldset class="companySelected">
                      <label class="ml-2 mt-3">{{ $t('besoin.modal_manage.field.company') }}</label>
                      <multiselect
                        v-model="formFields.companySelected"
                        v-validate="'required'"
                        :options="companys"
                        :placeholder="$t('besoin.modal_manage.placeholder.besoin_statut')"
                        name="company"
                        :searchable="false"
                        :show-labels="false"
                        track-by="name"
                        :allow-empty="false"
                        label="name"
                        :rows="8"
                      />
                      <div
                        v-for="errorText in formErrors.companySelected"
                        :key="'companySelected_' + errorText"
                        class="line-height-1"
                      >
                        <span class="fontSize10 red-skb">{{ errorText }}</span>
                      </div>
                    </fieldset>
                  </div>
                </div>
                <div class="row">
                  <div class="col-10 mx-auto">
                    <fieldset class="note">
                      <label class="ml-2 mt-3">{{ $t('besoin.modal_manage.field.note') }}</label>
                      <div class="row">
                        <div class="col-4 mx-auto">
                          <star-rating
                            v-model="formFields.comment.note"
                            :item-size="50"
                            :increment="1"
                          />
                          <div
                            v-for="errorText in formErrors.note"
                            :key="'note_' + errorText"
                            class="line-height-1"
                          >
                            <span class="fontSize10 red-skb">{{ errorText }}</span>
                          </div>
                        </div>
                      </div>
                    </fieldset>
                  </div>
                </div>
                <div class="row">
                  <div class="col-10 mx-auto">
                    <fieldset class="title">
                      <label class="ml-2 mt-3">{{ $t('besoin.modal_manage.field.title') }}</label>
                      <input
                        v-model="formFields.comment.title"
                        v-validate="formFields.comment.note ? 'required' : ''"
                        class="form-control"
                        :placeholder="$t('besoin.modal_manage.placeholder.title')"
                        name="title"
                        :rows="8"
                      >
                      <div
                        v-for="errorText in formErrors.title"
                        :key="'title_' + errorText"
                        class="line-height-1"
                      >
                        <span class="fontSize10 red-skb">{{ errorText }}</span>
                      </div>
                    </fieldset>
                  </div>
                </div>
                <div class="row">
                  <div class="col-10 mx-auto">
                    <fieldset class="message">
                      <label class="ml-2 mt-3">{{ $t('besoin.modal_manage.field.message') }}</label>
                      <textarea
                        v-model="formFields.comment.message"
                        v-validate="formFields.comment.note ? 'required' : ''"
                        class="form-control"
                        :placeholder="$t('besoin.modal_manage.placeholder.message')"
                        name="message"
                        :rows="8"
                      />
                      <div
                        :class="!$getNbCharactersLeft(formFields.comment.message, 1500) ? 'red-skb' : 'black-skb'"
                        class="text-right pt-2 fontSize12"
                      >
                        {{ $tc('commons.n_charaters_left', $getNbCharactersLeft(formFields.comment.message, 1500)) }}
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
                  :disabled="!formFields.besoinStatut || loading"
                  type="button"
                  class="btn btn-success"
                  @click="submitForm()"
                >
                  <span class="whitetxt">{{ $t('commons.validate') }}</span>
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
      id: {
        type: String,
        default: ''
      },
      besoin: {
        type: Object,
        default: () => new Object()
      },
      besoinStatuts: {
        type: Array,
        default: () => new Array()
      },
      utilisateurId: {
        type: Number,
        default: null
      },
      entitySelected: {
        type: Object,
        default: () => new Object()
      },
      isCompany: {
        type: Boolean,
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
        formFields: {
          besoinStatut: null,
          companySelected: null,
          comment: {
            title: null,
            message: null,
            company: null,
            besoin: null,
            note: null,
            authorCompany: null,
            utilisateur: null,
            _token: null
          },
          _token: null
        },
        formErrors: {
          besoinStatut: [],
          companySelected: [],
          message: [],
          company: [],
          besoin: [],
          note: [],
          title: [],

        },
        emptyFormFields: null,
        errorMessage: null
      };
    },
    computed: {
      companys() {
        let list = [];
        if (this.besoin && this.besoin.answers) {
          this.besoin.answers.forEach(answer => {
            list.push(answer.company);
          });
        }
        return list;
      },
      status() {
        let list = _.cloneDeep(this.besoinStatuts);
        if (this.companys.length === 0) {
          list = _.drop(list);
        } else {
          list = _.dropRight(list);
        }
        return list;
      },
      companySelected() {
        return this.formFields.companySelected;
      }
    },
    created() {
      $('#' + this.id).modal('handleUpdate');
    },
    mounted() {
      this.emptyFormFields = _.cloneDeep(this.formFields);
    },
    methods: {
      resetForm() {
        setTimeout(() => {
          this.$removeFormErrors();
          this.loading = false;
          this.formFields = _.cloneDeep(this.emptyFormFields);
          this.$emit('cancel-form');
        }, 150);
      },

      onCancelButton() {
        this.resetForm();
      },

      archiveBesoin() {
        if (this.formFields.comment.note && this.companySelected) {
          this.formFields.comment.besoin = _.cloneDeep(this.besoin);
          this.formFields.comment.utilisateur = this.utilisateurId;
          this.formFields.comment.company = _.cloneDeep(this.companySelected);
          this.formFields.comment._token = _.cloneDeep(this.token);
          if (this.isCompany) {
            this.formFields.comment.authorCompany = _.cloneDeep(this.entitySelected);
          }
        }
        let formData = this.$getFormFieldsData(this.formFields);
        this.loading = true;
        return axios.post('/api/besoins/' + this.besoin.id + '/validate', formData).then(response => {
          EventBus.$emit('besoin-closed-end');
          $('#' + this.id).modal('hide');
          this.loading = false;
        }).catch(e => {
          if (e.response && e.response.status && e.response.status === 400) {
            if (e.response.headers && e.response.headers['x-message']) {
              this.errorMessage = decodeURIComponent(e.response.headers['x-message']);
            } else {
              this.$handleFormError(e.response.data);
            }
          } else {
            this.$handleError(e);
          }
          this.loading = false;
        });
      },

      expiratedBesoin() {
        let formData = this.$getFormFieldsData(this.formFields);
        this.loading = true;
        return axios.post('/api/besoins/' + this.besoin.id + '/expirate', formData).then(response => {
          EventBus.$emit('besoin-closed-end');
          $('#' + this.id).modal('hide');
          this.loading = false;
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
          this.loading = false;
        });
      },

      deleteBesoin() {
        this.loading = true;
        return axios.delete('/api/besoins/' + this.besoin.id)
          .then(res => {
            EventBus.$emit('besoin-deleted');
            $('#' + this.id).modal('hide');
            this.loading = false;
          })
          .catch(e => {
            this.$handleError(e);
            this.loading = false;
          });
      },

      submitForm() {
        this.formFields._token = _.cloneDeep(this.token);
        this.$removeFormErrors();
        if (this.formFields.besoinStatut && this.formFields.besoinStatut.code === 'ARC') {
          this.archiveBesoin();
        } else if (this.formFields.besoinStatut && this.formFields.besoinStatut.code === 'ERR') {
          this.deleteBesoin();
        } else if (this.formFields.besoinStatut && this.formFields.besoinStatut.code === 'EXP') {
          this.expiratedBesoin();
        }
      },
    },

  };
</script>
