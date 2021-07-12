<template>
  <div class="container skb-body">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <a href="/admin/group">
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
                  <label class="fontUbuntuItalic fontSize16">{{ $t('group.fields.name') }}</label>
                  <input
                    v-model="formFields.name"
                    v-validate="'required'"
                    type="text"
                    name="name"
                    class="form-control"
                    :placeholder="$t('group.placeholder.name')"
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
                  <label class="fontUbuntuItalic fontSize16">{{ $t('group.fields.code') }}</label>
                  <input
                    v-model="formFields.code"
                    v-validate="'required'"
                    name="code"
                    type="text"
                    class="form-control"
                    :placeholder="$t('group.placeholder.code')"
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
              id="roles"
              class="col-12 roles"
            >
              <label class="fontUbuntuItalic fontSize16">{{ $t('group.fields.roles') }}</label>
              <dual-list
                v-if="rolesAtCreation !== null"
                :items="roles"
                :item-label-fields="['name']"
                :selected-items-at-creation="rolesAtCreation"
                @selected-items-change="$onSelectedItemsChange(arguments, 'roles')"
              />
              <input
                v-model="formFields.roles"
                v-validate="'required'"
                name="roles"
                class="d-none"
              >
              <div
                v-for="errorText in formErrors.roles"
                :key="'roles_' + errorText"
                class="line-height-1"
              >
                <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
              </div>
            </fieldset>
          </div>
          <div class="row my-3">
            <fieldset
              id="utilisateurs"
              class="col-12 utilisateurs"
            >
              <label class="fontUbuntuItalic fontSize16">{{ $t('group.fields.utilisateurs') }}</label>
              <autocomplete
                ref="autocomplete"
                :min="3"
                :debounce="500"
                :on-should-render-child="$getUserLabel"
                :on-select="setUser"
                :on-before-ajax="() => loading = true"
                :on-ajax-loaded="onUsersAjaxLoaded"
                :placeholder="$t('group.placeholder.utilisateurs')"
                param="autocomplete"
                url="/api/admin/utilisateurs"
                anchor="label"
                :classes="{input: 'form-control'}"
              />
              <input
                v-model="formFields.utilisateurs"
                v-validate="'required'"
                name="utilisateurs"
                class="d-none "
              >
              <div
                v-for="errorText in formErrors.utilisateurs"
                :key="'utilisateurs_' + errorText"
                class="line-height-1"
              >
                <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
              </div>
            </fieldset>
            <user-card
              v-for="(utilisateur, index) in formFields.utilisateurs"
              :key="'utilisateurs' + index"
              :user="utilisateur"
              @delete-entity="$onDeleteEntity(index, 'utilisateurs', formFields)"
            />
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
                {{ groupId ? $t('commons.edit') : $t('commons.create') }}
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
  import Autocomplete from 'vue2-autocomplete-js';
  import validatorRulesMixin from 'mixins/validatorRulesMixin';
  import adminFormMixin from 'mixins/adminFormMixin';
  import DualList from 'components/commons/dual-list';
  import UserCard from 'components/commons/user-card';
  import _ from 'lodash';

  export default {
    components: {
      DualList,
      Autocomplete,
      UserCard
    },
    mixins: [
      validatorRulesMixin,
      adminFormMixin
    ],
    props: {
      groupId: {
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
        API_URL: '/api/admin/groups' + (this.groupId ? `/${this.groupId}` : ''),
        rolesAtCreation: null,
        roles: [],
        formFields: {
          name: null,
          code: null,
          roles: [],
          utilisateurs: [],
          _token: null
        },
        formErrors: {
          name: [],
          code: [],
          roles: [],
          utilisateurs: []
        },
        includedFormsFields: undefined,
        errorMessage: null
      };
    },
    created() {
      let promises = [];
      promises.push(axios.get('/api/admin/roles'));
      if (this.groupId) {
        promises.push(axios.get(this.API_URL));
      }
      return Promise.all(promises).then(res => {
        this.roles = _.cloneDeep(res[0].data);
        if (this.groupId) {
          let group = res[1].data;
          this.$removeFieldsNotInForm(group, Object.keys(this.formFields));
          this.$setEditForm(group);
        }
        this.rolesAtCreation = _.cloneDeep(this.formFields.roles);
        this.loading = false;
      }).catch(e => {
        this.$handleError(e);
        this.loading = false;
      });
    },
    methods: {
      setUser(utilisateur) {
        if (!this.find(utilisateur)) {
          this.$onAfterCreateEntity(arguments, 'utilisateurs', this.formFields);
        }
        this.$refs.autocomplete.setValue('');
      },

      /**
       * Called after each request. If the request returns an empty list, adds an error to the
       * autocomplete field.
       * Hide the loading image
       */
      onUsersAjaxLoaded(data) {
        if (data.length) {
          this.formErrors.utilisateurs = [];
        } else {
          this.formErrors.utilisateurs = [this.$t('group.error.utilisateur_does_not_exist')];
        }
        this.loading = false;
      },

      find(utilisateur) {
        return _.find(this.formFields.utilisateurs, ['id', utilisateur.id]);
      },


    },
  };
</script>
