<template>
  <div class="skb-body container">
    <h3 class="fontUbuntuItalic orange-skb text-center p-1">
      {{ $t('comment.write_comment') }}
    </h3>
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <a @click="goBack()">
      <button
        title="Annulez "
        type="button"
        class="w-40px p-0 rounded-circle btn-close btn"
      >
        <font-awesome-icon :icon="['fas', 'times']" />
      </button>
    </a>
    <form>
      <div class="">
        <div class="register-card w-100 h-100">
          <!-- company card row  -->
          <div
            v-if="company"
            class="row"
          >
            <div class="col-12 border-bottom mb-4 pb-2">
              <div class="row">
                <b-img
                  v-if="company.image_profil"
                  :src="'/build/images/uploads/' + company.url_name + '/' + company.image_profil"
                  class="company-image"
                />
                <b-img
                  v-else
                  src="/build/default_company.jpg"
                  class="company-image"
                />
                <div class="col">
                  <div class="row">
                    <div class="col-9">
                      <span class="fontPoppins fontSize20 fontW600">{{ company.name }}</span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <font-awesome-icon
                        class="orange-login-skb"
                        :icon="['fas', 'map-marker-alt']"
                      />
                      <span class="fontPoppins fontSize14 fontW600 "> {{ company.address.postal_address }}, {{ company.city.name }} </span>
                    </div>
                  </div>
                  <div class="row">
                    <div class="col-12">
                      <span class="fontPoppins fontSize14 italic">{{ company.category.name }} </span>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!-- First row  -->
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <fieldset
                  id="note"
                  class="note"
                >
                  <label class="fontUbuntuItalic fontSize14">{{ $t('comment.fields.note') }} <span class="red">*</span></label>
                  <div class="row">
                    <div class="col-4 mx-auto">
                      <star-rating
                        v-model="formFields.note"
                        :item-size="50"
                        :increment="1"
                      />
                      <div
                        v-for="errorText in formErrors.note"
                        :key="'note_' + errorText"
                      >
                        <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                      </div>
                    </div>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
          <!-- second row -->
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <fieldset
                  id="title"
                  class="title"
                >
                  <label class="fontUbuntuItalic fontSize14">{{ $t('comment.fields.title') }} <span class="red">*</span></label>
                  <input
                    v-model="formFields.title"
                    v-validate="'required'"
                    type="text"
                    name="title"
                    class="form-control"
                    :placeholder="$t('comment.placeholder.title')"
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
          <!-- Third row -->
          <div class="row">
            <div class="col-12">
              <div class="form-group">
                <fieldset
                  id="message"
                  class="message"
                >
                  <label class="fontUbuntuItalic fontSize14">{{ $t('comment.fields.message') }} <span class="red">*</span></label>
                  <textarea
                    v-model="formFields.message"
                    v-validate="'required'"
                    name="message"
                    class="form-control"
                    :placeholder="$t('comment.placeholder.message')"
                    :maxlength="1000"
                    :rows="8"
                  />
                  <div
                    :class="!$getNbCharactersLeft(formFields.message, 1000) ? 'red-skb' : 'black-skb'"
                    class="text-right pt-2 fontSize12"
                  >
                    {{ $tc('commons.n_charaters_left', $getNbCharactersLeft(formFields.message, 1000)) }}
                  </div>
                  <div
                    v-for="errorText in formErrors.message"
                    :key="'message_' + errorText"
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
                @click="$validateForm()"
              >
                {{ $t('comment.create') }}
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
  import _ from 'lodash';
  import adminFormMixin from 'mixins/adminFormMixin';
  export default {
    components: {
    },
    mixins: [
      validatorRulesMixin,
      adminFormMixin
    ],
    props: {
      utilisateurId: {
        type: Number,
        default: null
      },
      companyId: {
        type: Number,
        default: null
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
        loading: false,
        loadingMap: false,
        API_URL: '/api/comments',
        formFields: {
          title: null,
          message: null,
          note: null,
          utilisateur: null,
          company:null,
          _token: null
        },
        formErrors: {
          title: [],
          message: [],
          note: [],
          utilisateur: [],
          company: []
        },
        company: null,
        utilisateur: null,
        userEmail: null,
        errorMessage: null
      };
    },
    created() {
      this.loading = true;
      let promises = [];
      if(this.companyId) {
        promises.push(axios.get('/api/admin/companies/' + this.companyId));
      }
      return Promise.all(promises).then(res => {
        if (this.companyId) {
          this.company = res[0].data;
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
        this.formFields.utilisateur = this.utilisateurId;
        this.formFields.company = _.cloneDeep(this.company);
        this.formFields._token = _.cloneDeep(this.token);
        let formData = this.$getFormFieldsData(this.formFields);
        return axios.post(this.API_URL, formData)
          .then(response => {
            this.loading = false;
            window.location.assign(response.headers.location);
          }).catch(e => {
            if (e.response && e.response.status && e.response.status == 400) {
              if (e.response.headers['x-message']) {
                this.errorMessage = decodeURIComponent(e.response.headers['x-message']);
              }
            } else  {
              this.$handleFormError(e.response.data);
            }
            this.loading = false;
          });
      },
      goBack() {
        this.$goTo(this.urlPrecedente);
      },
    },
  };
</script>
