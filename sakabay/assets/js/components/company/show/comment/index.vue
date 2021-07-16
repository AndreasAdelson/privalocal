<template>
  <div class="row mt-3 card-skb">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div class="col-12">
      <div class="row no-gutters mx-2 title-card-skb mb-2 justify-content-between">
        <div class="col-4">
          <font-awesome-icon
            class="yellow-login-skb"
            :icon="['fas', 'info-circle']"
          />
          <span class="fontPoppins underline">{{ $t('company.comment.title') }}</span><span class="pl-1">({{ nbMaxComments }})</span>
        </div>
        <div
          v-if="utilisateurId !== company.utilisateur.id"
          class="col-3"
        >
          <button
            class="btn button_skb_blue btn py-2"
            @click="$goTo('/comment/new/' + company.url_name)"
          >
            {{ $t('comment.write_comment') }}
          </button>
        </div>
        <div
          v-else
          class="col-6"
        >
          <span>{{ $t('comment.your_company') }}</span>
        </div>
      </div>
      <div class="row no-gutters mx-2">
        <div class="col-12 border-bottom">
          <div class="row">
            <div class="col-6">
              <div class="row">
                <div class="col-3 pr-0">
                  <input
                    id="1"
                    v-model="filters.note"
                    type="checkbox"
                    value="5"
                    @change="applyFilter()"
                  ></input>
                  <label class="fontSize16 ml-2">{{ $t('comment.note.excellent') }}</label>
                </div>
                <div class="col-7 p-0">
                  <k-progress
                    :class="configCheckbox.className"
                    :percent="configCheckbox.excellent"
                    :line-height="configCheckbox.lineHeight"
                    :color="configCheckbox.color"
                    :bg-color="configCheckbox.bgColor"
                  />
                </div>
              </div>
              <div class="row">
                <div class="col-3 pr-0">
                  <input
                    id="2"
                    v-model="filters.note"
                    type="checkbox"
                    value="4"
                    @change="applyFilter()"
                  ></input>
                  <label class="fontSize16 ml-2">{{ $t('comment.note.very_good') }}</label>
                </div>
                <div class="col-7 p-0">
                  <k-progress
                    :class="configCheckbox.className"
                    :percent="configCheckbox.veryGood"
                    :line-height="configCheckbox.lineHeight"
                    :color="configCheckbox.color"
                    :bg-color="configCheckbox.bgColor"
                  />
                </div>
              </div>
              <div class="row">
                <div class="col-3 pr-0">
                  <input
                    id="3"
                    v-model="filters.note"
                    type="checkbox"
                    value="3"
                    @change="applyFilter()"
                  ></input>
                  <label class="fontSize16 ml-2">{{ $t('comment.note.medium') }}</label>
                </div>
                <div class="col-7 p-0">
                  <k-progress
                    :class="configCheckbox.className"
                    :percent="configCheckbox.medium"
                    :line-height="configCheckbox.lineHeight"
                    :color="configCheckbox.color"
                    :bg-color="configCheckbox.bgColor"
                  />
                </div>
              </div>
              <div class="row">
                <div class="col-3 pr-0">
                  <input
                    id="4"
                    v-model="filters.note"
                    type="checkbox"
                    value="2"
                    @change="applyFilter()"
                  ></input>
                  <label class="fontSize16 ml-2">{{ $t('comment.note.low') }}</label>
                </div>
                <div class="col-7 p-0">
                  <k-progress
                    :class="configCheckbox.className"
                    :percent="configCheckbox.low"
                    :line-height="configCheckbox.lineHeight"
                    :color="configCheckbox.color"
                    :bg-color="configCheckbox.bgColor"
                  />
                </div>
              </div>
              <div class="row">
                <div class="col-3 pr-0">
                  <input
                    id="5"
                    v-model="filters.note"
                    type="checkbox"
                    value="1"
                    @change="applyFilter()"
                  ></input>
                  <label class="fontSize16 ml-2">{{ $t('comment.note.horrible') }}</label>
                </div>
                <div class="col-7 p-0">
                  <k-progress
                    :class="configCheckbox.className"
                    :percent="configCheckbox.horrible"
                    :line-height="configCheckbox.lineHeight"
                    :color="configCheckbox.color"
                    :bg-color="configCheckbox.bgColor"
                  />
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>

      <div class="row no-gutters">
        <div
          v-for="(comment, index) in printedComments"
          :key="'comment_' + index"
          class="col-12 comment-card border-bottom mx-2"
        >
          <div class="row pt-2 mb-3 align-items-center">
            <div class="col-1 text-center px-0">
              <b-img
                :src="urlImageProfil(comment)"
                class="rounded-circle logo-size-75"
              />
              <span>{{ getName(comment) }}</span>
            </div>
            <div class="col-11">
              <div class="row justify-content-between">
                <div
                  class="col-2"
                >
                  <v-app
                    id="rating"
                  >
                    <v-rating
                      v-model="comment.note"
                      class="readonly"
                      half-increments
                      readonly
                      color="orange"
                      half-icon="fas fa-star-half-alt"
                      size="10"
                      length="5"
                      background-color="grey"
                    />
                  </v-app>
                </div>
                <div class="col-3">
                  <span class="fontSize14 grey-bold-skb"> {{ $t('comment.dt_created', [$getDateLabelForComments(comment.dt_created)]) }}</span>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <span class="fontSize22 bold">{{ comment.title }}</span>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <div v-if="comment.isCommentTruncated && comment.message && comment.message.length > 300">
                    <span class="grey-bold-skb">{{ comment.message|truncate(300) }}</span>
                    <span
                      class="link fontSize12 cursor-pointer"
                      @click="comment.isCommentTruncated = false"
                    >
                      {{ $t("commons.see_more") }}
                    </span>
                  </div>
                  <div v-else>
                    <span class="grey-bold-skb">{{ comment.message }}</span>
                    <span
                      v-if="comment.message && comment.message.length > 300"
                      class="link fontSize12 cursor-pointer"
                      @click="comment.isCommentTruncated = true"
                    >
                      {{ $t("commons.see_less") }}
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <b-col
          cols="12"
          class="mt-3"
        >
          <b-pagination
            v-if="printedComments && printedComments.length != 0"
            v-model="filters.currentPage"
            :total-rows="pager.totalRows"
            :per-page="filters.perPage"
            align="center"
            @input="applyFilter()"
          />
        </b-col>
        <div v-if="printedComments && printedComments.length == 0 && nbMaxComments == 0">
          <span>{{ $t('comment.no_comments') }}</span>
        </div>
        <div v-if="printedComments && printedComments.length == 0 && nbMaxComments != 0">
          <span>{{ $t('comment.no_result_found') }}</span>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import axios from 'axios';
  import _ from 'lodash';
  import searchMixin from 'mixins/searchMixin';

  export default {
    components: {
    },
    mixins: [
      searchMixin,
    ],
    props: {
      company: {
        type: Object,
        default: null
      },
      utilisateurId: {
        type: Number,
        default: null
      }
    },
    data() {
      return {
        loading: false,
        printedComments: null,
        configCheckbox: {
          className: 'fontSize18',
          lineHeight: 7,
          color: '#f39c12',
          bgColor:'#c5c9cc',
          excellent: 0,
          veryGood: 0,
          medium: 0,
          low: 0,
          horrible: 0,
        },
        filters: {
          filter: '',
          filterFields: 'title, message',
          sortBy: 'dtCreated',
          sortDesc: true,
          currentPage: 1,
          perPage: 10,
          note: [],
          company: this.company.id,
          firstAttempt: true
        },
        pager: {
          pageOptions: [10, 50, 100],
          totalRows: 0
        },
        nbMaxComments: null,
        excellentResults: 0,
        veryGoodResults: 0,
        mediumResults: 0,
        lowResults: 0,
        horribleResults: 0,
      };
    },
    async created() {
      await this.applyFilter();
    },
    methods: {
      urlImageProfil(comment) {
        let url = '';
        if (comment.author_company && comment.author_company.image_profil) {
          url += '/build/images/uploads/' + comment.author_company.url_name + '/' + comment.author_company.image_profil;
        } else if (!comment.author_company && comment.utilisateur.image_profil) {
          url += '/build/images/uploads/' + comment.utilisateur.image_profil;
        } else {
          url += '/build/logo.png';
        }
        return url;
      },
      getName(comment) {
        let name = '';
        if (comment.author_company) {
          name += comment.author_company.name[0].toUpperCase() + comment.author_company.name.slice(1).toLowerCase();
        } else {
          name += comment.utilisateur.username[0].toUpperCase() + comment.utilisateur.username.slice(1).toLowerCase();
        }
        return name;
      },
      async applyFilter() {
        let sentFilter = this.setFilter();
        this.loading = true;
        this.printedComments = null;
        return axios.get('/api/comments', {
          params: sentFilter
        }).then(response => {
          let comments = _.cloneDeep(response.data);
          this.pager.totalRows = parseInt(response.headers['x-total-count']);
          _.each(comments, comment => {
            comment.isCommentTruncated = true;
          });
          this.printedComments = comments;
          if (this.filters.firstAttempt == true) {
            this.nbMaxComments = parseInt(response.headers['x-total-count']);
            this.excellentResults = parseInt(response.headers['x-excellent-count']);
            this.veryGoodResults = parseInt(response.headers['x-very-good-count']);
            this.mediumResults = parseInt(response.headers['x-medium-count']);
            this.lowResults = parseInt(response.headers['x-low-count']);
            this.horribleResults = parseInt(response.headers['x-horrible-count']);
            this.getPercent(this.nbMaxComments, this.excellentResults, 'excellent');
            this.getPercent(this.nbMaxComments, this.veryGoodResults, 'veryGood');
            this.getPercent(this.nbMaxComments, this.mediumResults, 'medium');
            this.getPercent(this.nbMaxComments, this.lowResults, 'low');
            this.getPercent(this.nbMaxComments, this.horribleResults, 'horrible');
          }
          this.loading = false;
          this.filters.firstAttempt = false;
        }).catch(error => {
          this.$handleError(error);
          this.loading = false;
        });
      },
      getPercent(nbResultMax, nbResultsNote, fieldName) {
        if (this.nbMaxComments !== 0) {
          this.configCheckbox[fieldName] = _.round(((nbResultsNote/nbResultMax) * 100), 0);
        } else {
          this.configCheckbox[fieldName] = 0;
        }
      },
      toggleComment(comment, isTruncated) {
        this.$nextTick(() => {
          comment.isCommentTruncated = isTruncated;
        });
      }
    }
  };
</script>
