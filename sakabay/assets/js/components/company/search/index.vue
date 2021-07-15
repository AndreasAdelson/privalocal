<template>
  <div class="skb-body container-fluid">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div class="row py-3 ">
      <div class="container">
        <div class="row search mb-3">
          <div class="col-12">
            <div class="row pt-3 pb-4">
              <div class="col">
                <b-form-group
                  horizontal
                  class="mb-0"
                >
                  <label class="fontSize16">{{ $t('commons.label.key_words') }}</label>
                  <b-input-group>
                    <b-form-input
                      v-model="filters.filter"
                      class="fontAlice"
                      :placeholder="$t('commons.placeholder.search')"
                      @keydown.enter.native="applyFilter()"
                    />
                  </b-input-group>
                </b-form-group>
              </div>
              <div class="col">
                <label class="fontSize16">{{ $t('commons.label.category') }}</label>
                <multiselect
                  v-model="filters.category"
                  v-validate="'required'"
                  :options="category"
                  :options-limit="300"
                  name="category"
                  :placeholder="$t('commons.placeholder.category')"
                  :searchable="false"
                  :close-on-select="true"
                  :show-labels="false"
                  label="name"
                  track-by="name"
                  class="fontAlice"
                />
              </div>
              <div class="col">
                <b-form-group
                  horizontal
                  class="mb-0"
                >
                  <label class="fontSize16">{{ $t('commons.label.city') }}</label>
                  <b-input-group>
                    <autocomplete
                      ref="autocomplete"
                      :min="3"
                      :debounce="500"
                      :on-should-render-child="$getCityLabel"
                      :on-select="setCity"
                      :placeholder="$t('commons.placeholder.city')"
                      :on-blur="resetCity"
                      param="autocomplete"
                      url="/api/admin/cities"
                      anchor="label"
                      style="width:85%"
                      :classes="{input: 'form-control'}"
                      class="fontAlice"
                    />
                    <b-input-group-append>
                      <b-btn
                        disabled
                        style="height:min-content"
                      >
                        <font-awesome-icon :icon="['fas', 'map-marker-alt']" />
                      </b-btn>
                    </b-input-group-append>
                  </b-input-group>
                </b-form-group>
              </div>
              <div class="col align-self-end pb-1">
                <b-button
                  :disabled="!filters.filter && !filters.category && !filters.city"
                  class="w-100 button_skb_yellow"
                  :class="!filters.filter && !filters.category && !filters.city ? 'no-cursor' : ''"
                  @click="applyFilter()"
                >
                  <span class="mr-2 fontAlice">{{ $t('commons.search_button') }}</span><font-awesome-icon :icon="['fas', 'search']" />
                </b-button>
              </div>
            </div>
            <div
              v-if="filters.category && filters.category.sous_categorys && filters.category.sous_categorys.length != 0"
              class="row pb-3"
            >
              <div class="col-3 offset-3">
                <label class="fontSize16">{{ $t('commons.label.activity') }}</label>
                <multiselect
                  v-model="filters.sousCategory"
                  v-validate="'required'"
                  :options="sousCategory"
                  :options-limit="300"
                  name="category"
                  :placeholder="$t('commons.placeholder.sous_category')"
                  :searchable="false"
                  :close-on-select="false"
                  :show-labels="false"
                  label="name"
                  track-by="name"
                  class="fontAlice"
                />
              </div>
            </div>
          </div>
        </div>
        <div
          v-if="loading2"
          class="loader2"
        />
        <div v-else-if="companies && companies.length > 0">
          <!-- Results here -->
          <div
            v-for="(company, index) in companies"
            :key="'card_' + index"
            class="row"
          >
            <div class="col-11 mx-auto">
              <company-card :company="company" />
            </div>
          </div>
          <b-row align-h="center">
            <b-col cols="12">
              <b-pagination
                v-model="filters.currentPage"
                :total-rows="pager.totalRows"
                :per-page="filters.perPage"
                align="center"
                @input="applyFilter()"
              />
            </b-col>
          </b-row>
        </div>
        <div v-else-if="companies && companies.length == 0">
          <!-- No Results founds here -->
          <div class="col-11 mx-auto text-center mt-5">
            <span class="fontPoppins fontSize16">{{ $t('commons.no_results_found') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import axios from 'axios';
  import searchMixin from 'mixins/searchMixin';
  import CompanyCard from './company-card';
  import Autocomplete from 'vue2-autocomplete-js';
  import _ from 'lodash';

  export default {
    components: {
      CompanyCard,
      Autocomplete
    },
    mixins: [
      searchMixin,
    ],
    props: {
    },
    data() {
      return {
        currentFilter: '',
        filters: {
          filter: '',
          filterFields: 'name, descriptionClean',
          category: null,
          sortBy: 'name',
          sortDesc: false,
          codeStatut: 'VAL',
          city: null,
          sousCategory: null,
          currentPage: 1,
          perPage: 10,
        },
        pager: {
          pageOptions: [10, 50, 100],
          totalRows: 0
        },
        sousCategory: [],
        category: [],
        companies: null,
        loading: false,
        loading2: false,

      };
    },
    computed: {
      categoryIsSet() {
        return this.filters.category;
      }
    },
    watch: {
      /**
       * When category is set to start a research, reset sousCategory field.
       * @param {Object} newValue
       */
      categoryIsSet(newValue) {
        if (newValue) {
          this.sousCategory = newValue.sous_categorys;
          this.filters.sousCategory = [];
        }
      },
    },
    created() {
      this.loading = true;
      let promises = [];
      promises.push(axios.get('/api/admin/categories'));
      return Promise.all(promises).then(res => {
        this.category = res[0].data;
        this.loading = false;
      });
    },
    methods: {
      applyFilter() {
        let sentFilter = this.setFilter();
        this.loading2 = true;
        return axios.get('/api/companies', {
          params: sentFilter
        }).then(response => {
          this.companies = _.cloneDeep(response.data);
          this.pager.totalRows = parseInt(response.headers['x-total-count']);
          this.loading2 = false;
        }).catch(error => {
          this.$handleError(error);
          this.loading2 = false;
        });
      },
      setCity(city) {
        this.filters.city = city;
        this.$refs.autocomplete.setValue(city.name);
      },
      resetCity() {
        if (!this.$refs.autocomplete.value) {
          this.filters.city = null;
        }
      },
      // companysList() {
      //   return this.companies.slice((this.pager.currentPage - 1) * this.pager.perPage,
      //                               this.pager.currentPage * this.pager.perPage);
      // }
    },
  };
</script>
