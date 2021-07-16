<template>
  <div class="container-fluid skb-body">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div class="row my-4">
      <div class="col-4">
        <h1 class="fontUbuntuItalic orange-skb">
          {{ $t('group.title') }}
        </h1>
      </div>
      <div class="col-1" />
      <div class="col-5">
        <b-form-group
          horizontal
          class="mb-0"
        >
          <b-input-group>
            <b-form-input
              v-model="currentFilter"
              :placeholder="$t('commons.rechercher')"
              @keydown.enter.native="applyFilter()"
            />
            <b-input-group-append>
              <b-btn
                :disabled="!currentFilter"
                @click="applyFilter()"
              >
                <font-awesome-icon :icon="['fas', 'search']" /></i>
              </b-btn>
            </b-input-group-append>
          </b-input-group>
        </b-form-group>
      </div>
      <div
        v-if="canCreate"
        class="col-1"
      >
        <a href="/admin/group/new">
          <b-button class="button_skb">{{ $t('commons.create') }}</b-button>
        </a>
      </div>
    </div>
    <!-- Table -->
    <b-row>
      <b-col cols="12">
        <b-table
          ref="table"
          class="tablestyle"
          :items="refreshData"
          :fields="table.field"
          :current-page="pager.currentPage"
          :per-page="pager.perPage"
          :busy.sync="table.isBusy"
          :filter="table.filter"
          :sort-by.sync="table.sortBy"
          :sort-desc.sync="table.sortDesc"
          small
          bordered
          responsive
          fixed
        >
          <template #cell(actions)="data">
            <b-button-group>
              <a
                v-if="canRead"
                :href="'/admin/group/show/' + data.value "
              >
                <b-button><font-awesome-icon :icon="['fas', 'eye']" /></i></b-button>
              </a>
              <a
                v-if="canEdit"
                :href="'/admin/group/edit/' + data.value "
                class="mx-1"
              >
                <b-button><font-awesome-icon :icon="['fas', 'edit']" /></i></b-button>
              </a>
              <b-button
                v-if="canDelete"
                data-toggle="modal"
                :data-target="'#' + DELETE_CONFIRM_MODAL_ID"
                @click="currentId = data.value"
              >
                <font-awesome-icon :icon="['fas', 'trash']" /></i>
              </b-button>
            </b-button-group>
          </template>
          <template #cell(roles)="data">
            <div v-html="data.value" />
          </template>
          <template #cell(utilisateurs)="data">
            <div v-html="data.value" />
          </template>
        </b-table>
      </b-col>
    </b-row>
    <b-row align-h="center">
      <b-col cols="12">
        <b-pagination
          v-model="pager.currentPage"
          :total-rows="pager.totalRows"
          :per-page="pager.perPage"
          align="center"
        />
      </b-col>
    </b-row>
    <confirm-modal
      :id="DELETE_CONFIRM_MODAL_ID"
      :title-text="$t('commons.confirm_modal.delete.title')"
      :body-text="$t('commons.confirm_modal.delete.text')"
      :button-yes-text="$t('commons.yes')"
      :button-no-text="$t('commons.no')"
      :are-buttons-on-same-line="true"
      @confirm-modal-yes="$deleteEntity('/api/admin/groups/')"
    />
  </div>
</template>

<script>
  import axios from 'axios';
  import paginationMixin from 'mixins/paginationMixin';
  import ConfirmModal from 'components/commons/confirm-modal';
  import _ from 'lodash';

  export default {
    components: {
      ConfirmModal
    },
    mixins: [paginationMixin],
    props: {
      canCreate: {
        type: Boolean,
        default: false
      },
      canEdit: {
        type: Boolean,
        default: false
      },
      canRead: {
        type: Boolean,
        default: false
      },
      canDelete: {
        type: Boolean,
        default: false
      },
    },
    data() {
      return {
        DELETE_CONFIRM_MODAL_ID: 'delete_confirmModal',
        currentId: null,
        NB_MAX_DISPLAYED: 5,
        currentFilter: '',
        table: {
          field: [
            { key: 'name', label: this.$t('group.fields.name'), sortable: true, thClass: 'tableitem' },
            { key: 'code', label: this.$t('group.fields.code'), sortable: true, thClass: 'tableitem' },
            { key: 'roles', label: this.$t('group.fields.roles'), sortable: true, thClass: 'tableitem' },
            { key: 'utilisateurs', label: this.$t('group.fields.utilisateurs'), thClass: 'tableitem' },
            (!this.canDelete & !this.canEdit & !this.canRead) ? null : { key: 'actions', label: this.$t('commons.actions'), class: 'col-size-9', thClass: 'tableitem' },
          ],
          sortBy: 'name'
        },
        loading: false
      };
    },
    methods: {
      refreshData() {
        this.loading = true;
        return axios.get('/api/admin/groups', {
          params: {
            filterFields: 'name,code',
            filter: this.currentFilter,
            sortBy: this.table.sortBy,
            sortDesc: this.table.sortDesc,
            currentPage: this.pager.currentPage,
            perPage: this.pager.perPage
          }
        }).then(response => {
          let items = _.map(response.data, group => _.assign(group, {
            code: group.code,
            name: group.name,
            roles: _.take(
              _.sortBy(
                _.map(group.roles, 'name'), (name) => name), this.NB_MAX_DISPLAYED)
              .join('<br />')
              + (group.roles.length > this.NB_MAX_DISPLAYED ? '<br />' + this.$tc('commons.see_more', group.roles.length - this.NB_MAX_DISPLAYED) : ''),
            utilisateurs: _.take(
              _.sortBy(
                _.map(group.utilisateurs, 'username'), (username) => username), this.NB_MAX_DISPLAYED)
              .join('<br />')
              + (group.utilisateurs.length > this.NB_MAX_DISPLAYED ? '<br />' + this.$tc('commons.see_more', group.utilisateurs.length - this.NB_MAX_DISPLAYED) : ''),
            actions: group.id,
          }));
          this.pager.totalRows = parseInt(response.headers['x-total-count']);
          this.loading = false;
          return items;
        }).catch(error => {
          this.$handleError(error);
          this.loading = false;
          return [];
        });
      },
    },
  };
</script>
