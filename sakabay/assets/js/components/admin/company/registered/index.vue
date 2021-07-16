<template>
  <div class="container-fluid skb-body">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div>
      <div class="row my-4">
        <div class="col-4">
          <h1 class="fontUbuntuItalic orange-skb">
            {{ $t('company.title_registered') }}
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
                  <font-awesome-icon :icon="['fas', 'search']" />
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
                  :href="'/admin/registered/entreprise/show/' + data.value "
                >
                  <b-button><font-awesome-icon :icon="['fas', 'eye']" /></b-button>
                </a>
                <a
                  v-if="canEdit"
                  :href="'/admin/registered/entreprise/edit/' + data.value "
                  class="mx-1"
                >
                  <b-button><font-awesome-icon :icon="['fas', 'edit']" /></b-button>
                </a>
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
    </div>
  </div>
</template>

<script>
  import axios from 'axios';
  import paginationMixin from 'mixins/paginationMixin';
  import _ from 'lodash';


  export default {
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
    },
    data() {
      return {
        currentFilter: '',
        table: {
          field: [
            { key: 'name', label: this.$t('company.table.fields.name'), sortable: true, thClass: 'tableitem' },
            { key: 'num_siret', label: this.$t('company.fields.num_siret'), thClass: 'tableitem', class: 'col-size-10' },
            { key: 'url_name', label: this.$t('company.table.fields.url_name'), thClass: 'tableitem' },
            { key: 'utilisateur', label: this.$t('company.table.fields.utilisateur'), thClass: 'tableitem' },
            { key: 'category', label: this.$t('company.table.fields.category'), thClass: 'tableitem' },
            { key: 'dt_created', label: this.$t('company.table.fields.dt_created'), thClass: 'tableitem', sortable: true },
            (!this.canDelete && !this.canEdit && !this.canRead) ? null : { key: 'actions', label: this.$t('commons.actions'), class: 'col-size-6', thClass: 'tableitem' },
          ],
          sortBy: 'dtCreated',
          sortDesc: true
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
            codeStatut: 'ENC'
          }
        }).then(response => {
          let items = _.map(response.data, company => _.assign(company, {
            name: company.name,
            num_siret: company.num_siret,
            url_name: company.url_name,
            utilisateur: company.utilisateur.username,
            category: company.category.name,
            dt_created: this.$getDateLabel2(company.dt_created),
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
    },
  };
</script>
