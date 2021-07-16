<template>
  <div class="skb-body container">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <a href="/services/list">
      <button
        title="Annulez "
        type="button"
        class="w-40px p-0 rounded-circle btn-close btn"
      >
        <font-awesome-icon :icon="['fas', 'times']" />
      </button>
    </a>
    <form>
      <div class="register-card w-100 h-100">
        <!-- First row -->
        <div
          v-if="companies.length > 0"
          class="row mb-2"
        >
          <div class="col-4">
            <fieldset
              id="company"
              class="company"
            >
              <label class="fontUbuntuItalic fontSize14">{{ $t('besoin.label.select') }}</label>
              <multiselect
                v-model="formFields.company"
                v-validate="'required'"
                :placeholder="$t('besoin.placeholder.select')"
                :disabled="companies.length < 0"
                :options="companies"
                name="company"
                :searchable="false"
                :close-on-select="true"
                :show-labels="false"
                :custom-label="$getAuthorLabel"
                track-by="name"
              />
            </fieldset>
          </div>
        </div>
        <!-- second row  -->
        <div class="row">
          <div class="col-12">
            <div class="form-group">
              <fieldset
                id="title"
                class="title"
              >
                <label class="fontUbuntuItalic fontSize14">{{ $t('besoin.label.title') }}</label>
                <input
                  v-model="formFields.title"
                  v-validate="'required'"
                  type="text"
                  name="title"
                  class="form-control"
                  :placeholder="$t('besoin.placeholder.title')"
                >
                <div
                  v-for="errorText in formErrors.title"
                  :key="'title_' + errorText"
                >
                  <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                </div>
              </fieldset>
            </div>
          </div>
        </div>
        <!-- third row -->
        <div class="row">
          <div class="col-6">
            <div class="form-group">
              <fieldset
                id="category"
                class="category"
              >
                <label class="fontSize16">{{ $t('besoin.label.category') }}</label>
                <multiselect
                  v-model="formFields.category"
                  v-validate="'required'"
                  :disabled="besoinId !== null"
                  :options="category"
                  :options-limit="300"
                  name="category"
                  :placeholder="$t('besoin.placeholder.category')"
                  :searchable="false"
                  :close-on-select="true"
                  :show-labels="false"
                  label="name"
                  track-by="name"
                  class="fontAlice"
                  @select="resetSousCategorys"
                />
                <div
                  v-for="errorText in formErrors.category"
                  :key="'message_' + errorText"
                >
                  <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                </div>
                <span
                  v-if="besoinId"
                  class="fontSize14 italic"
                >
                  {{ $t('besoin.not_editable') }}
                </span>
              </fieldset>
            </div>
          </div>
          <div class="col-6">
            <div class="form-group">
              <fieldset
                id="sousCategory"
                class="sousCategory"
              >
                <label class="fontSize16">{{ $t('besoin.label.activity') }}</label>
                <multiselect
                  v-model="formFields.sousCategorys"
                  v-validate="'required'"
                  :disabled="formFields.category === null || besoinId !== null"
                  :options="sousCategory"
                  :options-limit="300"
                  name="sousCategory"
                  :placeholder="$t('besoin.placeholder.activity')"
                  :searchable="false"
                  :close-on-select="false"
                  :show-labels="false"
                  label="name"
                  track-by="name"
                  class="fontAlice"
                  :multiple="true"
                />
                <div
                  v-for="errorText in formErrors.sousCategory"
                  :key="'message_' + errorText"
                >
                  <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                </div>
                <span
                  v-if="besoinId"
                  class="fontSize14 italic"
                >
                  {{ $t('besoin.not_editable') }}
                </span>
              </fieldset>
            </div>
          </div>
        </div>

        <!-- fourth row -->
        <div class="row mb-3">
          <div class="col-12">
            <div class="form-group">
              <fieldset
                id="description"
                class="description"
              >
                <label class="fontSize16">{{ $t('besoin.label.description') }}</label>
                <textarea
                  v-model="formFields.description"
                  v-validate="'required'"
                  class="form-control"
                  name="description"
                  :maxlength="1000"
                  :rows="8"
                  :placeholder="$tc('commons.maximum_n_characters', 1000)"
                />
                <div
                  :class="!$getNbCharactersLeft(formFields.description, 1000) ? 'red-skb' : 'black-skb'"
                  class="text-right pt-2 fontSize12"
                >
                  {{ $tc('commons.n_charaters_left', $getNbCharactersLeft(formFields.description, 1000)) }}
                </div>
                <div
                  v-for="errorText in formErrors.description"
                  :key="'message_' + errorText"
                >
                  <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                </div>
              </fieldset>
            </div>
          </div>
        </div>
      </div>


      <div
        v-if="companies.length > 0"
        class="row my-3"
      >
        <div class="col-6 offset-3">
          <button
            type="button"
            class="btn button_skb fontUbuntuItalic"
            data-toggle="modal"
            :data-target="'#' + SEND_CONFIRM_MODAL_ID"
          >
            {{ besoinId ? $t('commons.edit') : $t('commons.create') }}
          </button>
        </div>
      </div>
      <div
        v-else
        class="row my-3"
      >
        <div class="col-6 offset-3">
          <button
            type="button"
            class="btn button_skb fontUbuntuItalic"
            @click="$validateForm()"
          >
            {{ besoinId ? $t('commons.edit') : $t('commons.create') }}
          </button>
        </div>
      </div>
    </form>
    <confirm-modal
      :id="SEND_CONFIRM_MODAL_ID"
      :title-text="$t('besoin.modal_send_besoin.title')"
      :body-text="$t('besoin.modal_send_besoin.text', [$getAuthorLabel(formFields.company)])"
      :button-yes-text="$t('commons.yes')"
      :button-no-text="$t('commons.no')"
      :are-buttons-on-same-line="true"
      @confirm-modal-yes="$validateForm()"
    />
  </div>
</template>
<script>
  import axios from 'axios';
  import _ from 'lodash';
  import ConfirmModal from 'components/commons/confirm-modal';

  export default {
    components: {
      ConfirmModal
    },
    props: {
      utilisateurId: {
        type: Number,
        default: null
      },
      besoinId: {
        type: Number,
        default: null
      },
      token: {
        type: String,
        default: null
      }
    },
    data() {
      return {
        SEND_CONFIRM_MODAL_ID: 'send_confirmModal',
        API_URL: this.besoinId ? '/api/besoins/' + this.besoinId : '/api/besoins',
        loading: true,
        category: [],
        companies: [],
        sousCategory: [],
        utilisateur: null,
        formFields: {
          title: null,
          description: null,
          category: null,
          sousCategorys: null,
          author: null,
          company: null,
          _token: null
        },
        formErrors: {
          title: [],
          description: [],
          category: [],
          sousCategory: [],
          company: []
        }
      };
    },
    computed: {
      categoryIsSet() {
        return this.formFields.category;
      },
    },
    watch: {
      /**
       * @param {Object} newValue
       */
      categoryIsSet(newValue) {
        if (newValue) {
          this.sousCategory = newValue.sous_categorys;
        }
      },
    },
    created() {
      let promises = [];
      //WARNING nom du serial group à revoir ?

      promises.push(axios.get('/api/admin/categories'));
      promises.push(axios.get('/api/companies/utilisateur/' + this.utilisateurId));
      if (this.besoinId) {
        promises.push(axios.get('/api/besoins/' + this.besoinId));
      }
      return Promise.all(promises).then(res => {
        this.category = _.cloneDeep(res[0].data);
        this.companies = _.cloneDeep(res[1].data);
        if (this.besoinId) {
          let besoin = _.cloneDeep(res[2].data);
          this.$removeFieldsNotInForm(besoin, Object.keys(this.formFields));
          this.$setEditForm(besoin);
        }
        if (this.companies.length > 0) {
          this.utilisateur = _.cloneDeep(this.companies[0].utilisateur);
          this.companies.push(this.utilisateur);
        }
        this.loading = false;

      }).catch(e => {
        this.$handleError(e);
        this.loading = false;
      });
    },
    methods: {
      submitForm() {
        this.loading = true;
        this.formFields.author = _.cloneDeep(this.utilisateurId);
        this.formFields._token = _.cloneDeep(this.token);
        if (this.formFields.company && this.formFields.company.last_name && this.formFields.company.id === this.utilisateurId) {
          this.formFields.company = null;
        }
        let formData = this.$getFormFieldsData(this.formFields);
        return axios.post(this.API_URL, formData).then(response => {
          this.loading = false;
          window.location.assign(response.headers.location);
        }).catch(e => {
          if (e.response && e.response.status && e.response.status == 400) {
            this.$handleFormError(e.response.data);
          } else {
            this.$handleError(e);
          }
          this.loading = false;
        });
      },
      resetSousCategorys() {
        this.formFields.sousCategorys = [];
      }
    },

  };
</script>
