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
          {{ $t('company.title_subscribed') }}
        </h1>
      </div>
      <div class="col-1" />
      <div class="col-6">
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
                :href="'/admin/entreprise/show/' + data.value "
              >
                <b-button><font-awesome-icon :icon="['fas', 'eye']" /></i></b-button>
              </a>
              <a
                v-if="canEdit"
                :href="'/admin/entreprise/edit/' + data.value "
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
      @confirm-modal-yes="$deleteEntity('/api/admin/companies/')"
    />
  </div>
</template>

<script>
  import axios from 'axios';
  import paginationMixin from 'mixins/paginationMixin';
  import ConfirmModal from 'components/commons/confirm-modal';
  import _ from 'lodash';
  import moment from 'moment';

  export default {
    components: {
      ConfirmModal
    },
    mixins: [paginationMixin],
    props: {
      canRead: {
        type: Boolean,
        default: false
      },
      canEdit: {
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
            { key: 'name', label: this.$t('company.table.fields.name'), sortable: true, thClass: 'tableitem' },
            { key: 'num_siret', label: this.$t('company.fields.num_siret'), thClass: 'tableitem', class: 'col-size-10' },
            { key: 'url_name', label: this.$t('company.table.fields.url_name'), thClass: 'tableitem' },
            { key: 'utilisateur', label: this.$t('company.table.fields.utilisateur'), thClass: 'tableitem' },
            { key: 'category', label: this.$t('company.table.fields.category'), thClass: 'tableitem' },
            { key: 'subscription', label: this.$t('company.table.fields.subscription'), thClass: 'tableitem', class: 'col-size-10' },
            (!this.canDelete && !this.canEdit && !this.canRead) ? null : { key: 'actions', label: this.$t('commons.actions'), class: 'col-size-8', thClass: 'tableitem' },
          ],
          sortBy: 'name'
        },
        loading: false
      };
    },
    methods: {
      refreshData() {
        this.loading = true;
        return axios.get('/api/admin/companies', {
          params: {
            filterFields: 'name',
            filter: this.currentFilter,
            sortBy: this.table.sortBy,
            sortDesc: this.table.sortDesc,
            currentPage: this.pager.currentPage,
            perPage: this.pager.perPage,
            codeStatut: 'VAL'
          }
        }).then(response => {
          let items = _.map(response.data, company => _.assign(company, {
            name: company.name,
            num_siret: company.num_siret,
            url_name: company.url_name,
            utilisateur: company.utilisateur.username,
            category: company.category.name,
            subscription: this.isSubscribed(company.company_subscriptions),
            actions: company.id,
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
      isSubscribed(companySubscriptions) {
        let label = '';
        if (companySubscriptions.length != 0) {
          let subscriptions = _.cloneDeep(companySubscriptions);
          subscriptions = _.orderBy(subscriptions, [
            function(subscription) {
              let dtFin = moment(subscription.dt_fin, 'DD/MM/YYYY HH:mm:ss').format('MM/DD/YYYY H:mm:ss');
              return new Date(dtFin.toString());
            }
          ], ['desc']);
          let recentSubscription = _.cloneDeep(subscriptions[0]);

          switch(recentSubscription.subscription_status.code) {
          case 'VAL':
            if (moment(recentSubscription.dt_fin, 'DD/MM/YYYY HH:mm:ss').isAfter() && moment(recentSubscription.dt_debut, 'DD/MM/YYYY HH:mm:ss').isBefore()) {
              label = this.$t('dashboard.history.table.in_progress');
            } else {
              label = this.$t('dashboard.history.table.coming_soon');
            }
            break;
          case 'TER':
            label = this.$t('dashboard.history.table.ended');
            break;
          case 'ENC':
            label = this.$t('dashboard.history.table.in_progress');
            break;
          case 'ANN':
            if (moment(recentSubscription.dt_fin, 'DD/MM/YYYY HH:mm:ss').isAfter() && moment(recentSubscription.dt_debut, 'DD/MM/YYYY HH:mm:ss').isBefore()) {
              label = this.$t('dashboard.history.table.canceled_renewal');
            }
            else {
              if (moment(recentSubscription.dt_fin, 'DD/MM/YYYY HH:mm:ss').isAfter()) {
                label = this.$t('dashboard.history.table.canceled');
              } else {
                label = this.$t('dashboard.history.table.ended');
              }
            }
            break;
          case 'OFF':
            if(moment(recentSubscription.dt_fin, 'DD/MM/YYYY HH:mm:ss').isAfter() && moment(recentSubscription.dt_debut, 'DD/MM/YYYY HH:mm:ss').isBefore()) {
              label = this.$t('dashboard.history.table.offer');
            } else {
              label = this.$t('dashboard.history.table.ended');
            }
            break;
          default:
            label = this.$t('dashboard.history.table.none');
          }
          return label;
        } else {
          label = this.$t('dashboard.history.table.none');
          return label;
        }
      }
    },
  };
</script>
