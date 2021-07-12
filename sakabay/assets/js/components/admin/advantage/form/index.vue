<template>
  <div class="container skb-body">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <a href="/admin/advantage">
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
                  id="message"
                  class="message"
                >
                  <label class="fontUbuntuItalic fontSize14">{{ $t('advantage.fields.message') }}</label>
                  <input
                    v-model="formFields.message"
                    v-validate="'required'"
                    type="text"
                    name="message"
                    class="form-control"
                    :placeholder="$t('advantage.placeholder.message')"
                  >
                  <div
                    v-for="errorText in formErrors.message"
                    :key="'message_' + errorText"
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
                  <label class="fontUbuntuItalic fontSize14">{{ $t('advantage.fields.code') }}</label>
                  <input
                    v-model="formFields.code"
                    v-validate="'required'"
                    type="text"
                    name="code"
                    class="form-control"
                    :placeholder="$t('advantage.placeholder.code')"
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
                  <label class="fontUbuntuItalic fontSize14">{{ $t('advantage.fields.priority') }}</label>
                  <input
                    v-model="formFields.priority"
                    v-validate="'required'"
                    type="text"
                    name="priority"
                    class="form-control"
                    :placeholder="$t('advantage.placeholder.priority')"
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
                {{ advantageId ? $t('commons.edit') : $t('commons.create') }}
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
      adminFormMixin,
    ],
    props: {
      advantageId: {
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
        API_URL: '/api/admin/advantages' + (this.advantageId ? `/${this.advantageId}` : ''),
        sousCategorys: [],
        formFields: {
          message: null,
          priority: null,
          code: null,
          _token: null
        },
        formErrors: {
          message: [],
          priority: [],
          code: null
        },
        errorMessage: null
      };
    },
    created() {
      let promises = [];

      if (this.advantageId) {
        promises.push(axios.get(this.API_URL));
      }
      return Promise.all(promises).then(res => {
        if (this.advantageId) {
          let advantage = res[0].data;
          this.$removeFieldsNotInForm(advantage, Object.keys(this.formFields));
          this.$setEditForm(advantage);
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
