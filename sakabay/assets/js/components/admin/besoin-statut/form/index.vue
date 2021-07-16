<template>
  <div class="container skb-body">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <a href="/admin/besoin-statut">
      <button
        title="Annulez les modifications"
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
                  id="name"
                  class="name"
                >
                  <label class="fontUbuntuItalic fontSize14">{{ $t('besoin_statut.fields.name') }}</label>
                  <input
                    v-model="formFields.name"
                    v-validate="'required'"
                    type="text"
                    name="name"
                    class="form-control"
                    :placeholder="$t('besoin_statut.placeholder.name')"
                  >
                  <div
                    v-for="errorText in formErrors.name"
                    :key="'name_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="code"
                  class="code"
                >
                  <label class="fontUbuntuItalic fontSize14">{{ $t('besoin_statut.fields.code') }}</label>
                  <input
                    v-model="formFields.code"
                    v-validate="'required'"
                    name="code"
                    type="text"
                    class="form-control"
                    :placeholder="$t('besoin_statut.placeholder.code')"
                  >
                  <div
                    v-for="errorText in formErrors.code"
                    :key="'code_' + errorText"
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
                  id="priority"
                  class="priority"
                >
                  <label class="fontUbuntuItalic fontSize14">{{ $t('besoin_statut.fields.priority') }}</label>
                  <input
                    v-model="formFields.priority"
                    v-validate="'required'"
                    type="text"
                    name="priority"
                    class="form-control"
                    :placeholder="$t('besoin_statut.placeholder.priority')"
                  >
                  <div
                    v-for="errorText in formErrors.priority"
                    :key="'priority_' + errorText"
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
                {{ besoinStatutId ? $t('commons.edit') : $t('commons.create') }}
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

  export default {
    components: {
    },
    mixins: [
      validatorRulesMixin,
      adminFormMixin
    ],
    props: {
      besoinStatutId: {
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
        loading: true,
        API_URL: '/api/admin/besoin-statuts' + (this.besoinStatutId ? `/${this.besoinStatutId}` : ''),
        formFields: {
          name: null,
          code: null,
          priority: null,
          _token: null
        },
        formErrors: {
          name: [],
          code: [],
          priority: []
        },
        errorMessage: null
      };
    },
    created() {
      let promises = [];
      promises.push(axios.get(this.API_URL));
      return Promise.all(promises).then(res => {
        if (this.besoinStatutId) {
          let besoinStatut = res[0].data;
          this.$removeFieldsNotInForm(besoinStatut, Object.keys(this.formFields));
          this.$setEditForm(besoinStatut);
        }
        this.loading = false;
      }).catch(e => {
        this.$handleError(e);
        this.loading = false;
      });
    },
    methods: {

    },
  };
</script>
