<template>
  <div class="container skb-body">
    <button
      :title="$t('commons.go_back')"
      type="button"
      class="w-40px p-0 rounded-circle btn-close btn"
      @click.prevent="goBack()"
    >
      <font-awesome-icon :icon="['fas', 'times']" />
    </button>
    <form>
      <div class="">
        <div class="register-card w-100 h-100">
          <!-- First row  -->
          <div class="row mb-3">
            <div class="col-1">
              <b-img
                v-if="urlImageProfil"
                class="rounded-circle logo-size-75"
                :src="urlImageProfil"
              />
              <avatar
                v-else-if="utilisateur"
                class="p-0"
                :username="utilisateur.last_name + ' ' + utilisateur.first_name"
                :size="75"
              />
            </div>
            <div class="col-5 my-auto">
              <div class="form-group mb-0">
                <fieldset
                  id="imageProfil"
                  class="imageProfil"
                >
                  <input
                    ref="imageProfil"
                    name="imageProfil"
                    type="file"
                    @change="onFileSelected"
                  >
                  <div
                    v-for="errorText in formErrors.imageProfil"
                    :key="'imageProfil_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
          <!-- Second row  -->
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="email"
                  class="email"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('user.fields.email') }}</label>
                  <input
                    v-model="formFields.email"
                    v-validate="'required|email'"
                    type="text"
                    name="email"
                    class="form-control"
                    :placeholder="$t('user.placeholder.email')"
                  >
                  <div
                    v-for="errorText in formErrors.email"
                    :key="'email_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="firstName"
                  class="firstName"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('user.fields.first_name') }}</label>
                  <input
                    v-model="formFields.firstName"
                    v-validate="'required'"
                    name="firstName"
                    type="text"
                    class="form-control"
                    :placeholder="$t('user.placeholder.first_name')"
                  >
                  <div
                    v-for="errorText in formErrors.firstName"
                    :key="'firstName_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
          <!-- Third row  -->
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="lastName"
                  class="lastName"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('user.fields.last_name') }}</label>
                  <input
                    v-model="formFields.lastName"
                    type="text"
                    class="form-control"
                    :placeholder="$t('user.placeholder.last_name')"
                  >
                  <div
                    v-for="errorText in formErrors.lastName"
                    :key="'lastName_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="username"
                  class="username"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('user.fields.username') }}</label>
                  <input
                    v-model="formFields.username"
                    v-validate="'required_username'"
                    type="text"
                    name="username"
                    class="form-control"
                    :placeholder="$t('user.placeholder.username')"
                  >
                  <div
                    v-for="errorText in formErrors.username"
                    :key="'username_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
          <!-- Fourth row -->
          <div class="row">
            <div class="col-6">
              <div class="row">
                <div class="col-12">
                  <label class="fontUbuntuItalic fontSize16">{{ $t('user.fields.password') }}</label>
                </div>
              </div>
              <div class="row justify-content-between align-items-center">
                <div class="col-4">
                  <span class="w-50">*********</span>
                </div>
                <div class="col-3">
                  <button
                    type="button"
                    class="btn fontUbuntu italic button_skb"
                    @click="changePassword()"
                  >
                    {{ passwordActive ? $t('commons.close') : $t('commons.edit') }}
                  </button>
                </div>
              </div>
            </div>
          </div>
          <!-- Fifth row -->
          <div
            v-if="passwordActive"
            class="row"
          >
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="first"
                  class="first"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('user.fields.plain_password_first') }}</label>
                  <input
                    v-model="formFields.plainPassword.first"
                    v-validate="'required'"
                    type="text"
                    name="plainPassword"
                    class="form-control"
                  >
                  <div
                    v-for="errorText in formErrors.first"
                    :key="'plainPasswordFirst_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="second"
                  class="second"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('user.fields.plain_password_second') }}</label>
                  <input
                    v-model="formFields.plainPassword.second"
                    v-validate="'required'"
                    type="text"
                    name="plainPassword"
                    class="form-control"
                    :class="formErrors.first.length > 0 ? 'border-danger-skb' : '' "
                  >
                  <div
                    v-for="errorText in formErrors.first"
                    :key="'plainPasswordSecond_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
          <div class="row mt-5">
            <div class="col-6 offset-3">
              <button
                type="button"
                class="btn button_skb fontUbuntu italic"
                @click="$validateForm()"
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
  import Avatar from 'vue-avatar';
  import _ from 'lodash';

  export default {
    components: {
      Avatar
    },
    mixins: [
      validatorRulesMixin
    ],
    props: {
      utilisateurId: {
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
        formFields: {
          email: null,
          firstName: null,
          lastName: null,
          username: null,
          currentPassword: null,
          plainPassword: new Object(),
          _token: null
        },
        formErrors: {
          email: [],
          firstName: [],
          lastName: [],
          username: [],
          currentPassword: [],
          first: [],
          second: []
        },
        urlImageProfil: null,
        imageProfilSelected: null,
        utilisateur: null,
        imageName: '',
        passwordActive: false
      };
    },
    created() {
      if (this.utilisateurId) {
        return axios.get('/api/admin/utilisateurs/' + this.utilisateurId)
          .then(response => {
            this.$setEditForm(response.data);
            this.utilisateur = response.data;
            if (this.utilisateur.image_profil) {
              this.urlImageProfil = '/build/images/uploads/' + this.utilisateur.image_profil;
            }
            this.loading = false;
          }).catch(e => {
            this.$handleError(e);
            this.loading = false;
          });
      }
    },

    methods: {
      onFileSelected() {
        this.imageProfilSelected = this.$refs.imageProfil.files[0];
        this.imageName = this.$refs.imageProfil.files[0].name;
        this.urlImageProfil = URL.createObjectURL(this.imageProfilSelected);
      },

      submitForm() {
        this.formFields._token = _.cloneDeep(this.token);
        let formData = this.$getFormFieldsData(this.formFields);
        if (this.imageProfilSelected) {
          formData.append('file', this.imageProfilSelected);
        }
        return axios.post('/api/utilisateur/edit/' + this.utilisateurId, formData)
          .then(response => {
            window.location.assign(response.headers.location);
            this.loading = false;
          }).catch(e => {
            if (e.response && e.response.status && e.response.status == 400) {
              this.$handleFormError(e.response.data);
            }
            else {
              this.$handleError(e);
            }
            this.loading = false;

          });
      },

      goBack() {
        this.$goTo(this.urlPrecedente);
      },

      changePassword() {
        this.passwordActive = !this.passwordActive;

      }
    },
  };
</script>
