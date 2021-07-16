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
          {{ $t('fonction.title') }}
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
        <a href="/admin/fonction/new">
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
      @confirm-modal-yes="$deleteEntity('/api/admin/fonctions/')"
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
      canDelete: {
        type: Boolean,
        default: false
      },
    },
    data() {
      return {
        DELETE_CONFIRM_MODAL_ID: 'delete_confirmModal',
        currentId: null,
        currentFilter: '',
        table: {
          field: [
            { key: 'description', label: this.$t('fonction.fields.description'), sortable: true, thClass: 'tableitem' },
            (!this.canDelete) ? null : { key: 'actions', label: this.$t('commons.actions'), class: 'col-size-4', thClass: 'tableitem' },
          ],
          sortBy: 'description'
        },
        loading: false
      };
    },
    methods: {
      refreshData() {
        this.loading = true;
        return axios.get('/api/admin/fonctions', {
          params: {
            filterFields: 'description',
            filter: this.currentFilter,
            sortBy: this.table.sortBy,
            sortDesc: this.table.sortDesc,
            currentPage: this.pager.currentPage,
            perPage: this.pager.perPage
          }
        }).then(response => {
          let items = _.map(response.data, fonction => _.assign(fonction, {
            description: fonction.description,
            actions: fonction.id,
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
