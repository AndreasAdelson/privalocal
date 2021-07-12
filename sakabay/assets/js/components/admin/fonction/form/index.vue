<template>
  <div>
    <div class="container">
      <a href="/admin/fonction">
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
                    id="code"
                    class="code"
                  >
                    <label class="fontUbuntuItalic fontSize16">{{ $t('fonction.fields.code') }}</label>
                    <input
                      v-model="formFields.code"
                      v-validate="'required'"
                      type="text"
                      name="code"
                      class="form-control"
                      :placeholder="$t('fonction.placeholder.code')"
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
              <div class="col-6">
                <div class="form-group">
                  <fieldset
                    id="description"
                    class="description"
                  >
                    <label class="fontUbuntuItalic fontSize16">{{ $t('fonction.placeholder.description') }}</label>
                    <input
                      v-model="formFields.description"
                      v-validate="'required'"
                      name="description"
                      type="text"
                      class="form-control"
                      :placeholder="$t('fonction.placeholder.description')"
                    >
                    <div
                      v-for="errorText in formErrors.description"
                      :key="'description_' + errorText"
                    >
                      <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                    </div>
                  </fieldset>
                </div>
              </div>
            </div>
            <!-- Second row  -->
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
                  {{ $t('commons.create') }}
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
      token: {
        type: String,
        default: null
      }
    },
    data() {
      return {
        API_URL: '/api/admin/fonctions',
        formFields: {
          code: null,
          description: null,
          _token: null
        },
        formErrors: {
          code: [],
          description: [],
        },
        errorMessage: null
      };
    },

    methods: {

    },
  };
</script>
