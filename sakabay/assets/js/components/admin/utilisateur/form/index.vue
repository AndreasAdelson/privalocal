<template>
  <div>
    <div class="container skb-body">
      <div v-show="loading">
        <div class="loader-container-full">
          <div class="loader" />
        </div>
      </div>
      <a href="/admin/utilisateur">
        <button
          :title="$t('commons.undo_change')"
          type="button"
          class="w-40px p-0 rounded-circle btn-close btn"
        >
          <font-awesome-icon :icon="['fas', 'times']" />
        </button>
      </a>
      <form>
        <div class="">
          <div class="register-card w-100 h-100">
            <!-- First row  -->
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
                      placeholder="Enter email"
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
                      placeholder="Enter first name"
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
            <!-- Second row  -->
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
                      placeholder="Enter first name"
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
                      placeholder="Enter username"
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
            <span
              v-if="errorMessage"
              id="error-message"
              role="alert"
              class="fontUbuntuItalic fontSize13 red-skb"
            >
              {{ errorMessage }}
            </span>
            <div class="row">
              <div class="col-6 offset-3">
                <button
                  type="button"
                  class="btn button_skb fontUbuntuItalic"
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
  </div>
</template>
<script>
  import axios from 'axios';
  import validatorRulesMixin from 'mixins/validatorRulesMixin';
  import adminFormMixin from 'mixins/adminFormMixin';

  export default {
    mixins: [
      validatorRulesMixin,
      adminFormMixin
    ],
    props: {
      utilisateurId: {
        type: Number,
        default: null,
      },
      token: {
        type: String,
        default: null
      }
    },
    data() {
      return {
        API_URL: '/api/admin/utilisateurs/' + this.utilisateurId,
        loading: false,
        formFields: {
          email: null,
          firstName: null,
          lastName: null,
          username: null,
          _token: null
        },
        formErrors: {
          email: [],
          firstName: [],
          lastName: [],
          username: []
        },
        errorMessage: null
      };
    },
    created() {
      if (this.utilisateurId) {
        this.loading = true;
        return axios.get('/api/admin/utilisateurs/' + this.utilisateurId)
          .then(response => {
            this.$setEditForm(response.data);
            this.loading = false;
          }).catch(error => {
            this.$handleError(error);
            this.loading = false;
          });
      }
    },
    methods: {

    },
  };
</script>
