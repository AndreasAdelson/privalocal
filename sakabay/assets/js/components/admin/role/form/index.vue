<template>
  <div class="container skb-body">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <a href="/admin/role">
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
                  id="name"
                  class="name"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('role.fields.name') }}</label>
                  <input
                    v-model="formFields.name"
                    v-validate="'required'"
                    type="text"
                    name="name"
                    class="form-control"
                    :placeholder="$t('role.placeholder.name')"
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
                  <label class="fontUbuntuItalic fontSize16">{{ $t('role.fields.code') }}</label>
                  <input
                    v-model="formFields.code"
                    v-validate="'required'"
                    name="code"
                    type="text"
                    class="form-control"
                    :placeholder="$t('role.placeholder.code')"
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
          <!-- Second row  -->
          <div class="row">
            <fieldset
              id="fonctions"
              class="col-12 fonctions"
            >
              <label class="fontUbuntuItalic fontSize16">{{ $t('role.fields.fonctions') }}</label>
              <dual-list
                v-if="fonctionsAtCreation !== null"
                :items="fonctions"
                :item-label-fields="['description']"
                :selected-items-at-creation="fonctionsAtCreation"
                @selected-items-change="$onSelectedItemsChange(arguments, 'fonctions')"
              />
              <input
                v-model="formFields.fonctions"
                v-validate="'required'"
                name="fonctions"
                class="d-none"
              >
              <div
                v-for="errorText in formErrors.fonctions"
                :key="'fonctions_' + errorText"
                class="line-height-1"
              >
                <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
              </div>
            </fieldset>
          </div>
          <!-- Third row  -->
          <div class="row">
            <fieldset
              id="groups"
              class="col-12 groups"
            >
              <label class="fontUbuntuItalic fontSize16">{{ $t('role.fields.groups') }}</label>
              <dual-list
                v-if="groupsAtCreation !== null"
                :items="groups"
                :item-label-fields="['name']"
                :selected-items-at-creation="groupsAtCreation"
                @selected-items-change="$onSelectedItemsChange(arguments, 'groups')"
              />
              <input
                v-model="formFields.groups"
                name="groups"
                class="d-none"
              >
              <div
                v-for="errorText in formErrors.groups"
                :key="'groups_' + errorText"
                class="line-height-1"
              >
                <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
              </div>
            </fieldset>
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
                {{ roleId ? $t('commons.edit') : $t('commons.create') }}
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
  import DualList from 'components/commons/dual-list';
  import _ from 'lodash';

  export default {
    components: {
      DualList
    },
    mixins: [
      validatorRulesMixin,
      adminFormMixin
    ],
    props: {
      roleId: {
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
        API_URL: '/api/admin/roles' + (this.roleId ? `/${this.roleId}` : ''),
        fonctionsAtCreation: null,
        groupsAtCreation: null,
        fonctions: [],
        groups: [],
        formFields: {
          name: null,
          code: null,
          fonctions: [],
          groups: [],
          _token: null
        },
        formErrors: {
          name: [],
          code: [],
          fonctions: [],
          groups: []
        },
        errorMessage: null
      };
    },
    created() {
      let promises = [];
      promises.push(axios.get('/api/admin/fonctions'));
      promises.push(axios.get('/api/admin/groups'));
      if (this.roleId) {
        promises.push(axios.get(this.API_URL));
      }
      return Promise.all(promises).then(res => {
        this.fonctions = res[0].data;
        this.groups = res[1].data;
        if (this.roleId) {
          let role = res[2].data;
          this.$removeFieldsNotInForm(role, Object.keys(this.formFields));
          this.$setEditForm(role);
        }
        this.fonctionsAtCreation = _.cloneDeep(this.formFields.fonctions);
        this.groupsAtCreation = _.cloneDeep(this.formFields.groups);
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
