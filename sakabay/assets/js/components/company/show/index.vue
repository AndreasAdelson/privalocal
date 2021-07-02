<template>
  <div class="skb-body container">
    <div v-if="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div v-else-if="company">
      <div class="row mt-4 card-skb">
        <div class="col-12">
          <b-img
            v-if="company.image_profil"
            :src="'/build/images/uploads/' + company.url_name + '/' + company.image_profil"
            class="company-image-2"
          />
          <b-img
            v-else
            src="/build/default_company.jpg"
            class="company-image-2"
          />
          <div class="header-main-info">
            <div class="row">
              <div class="col-12">
                <h1
                  class="fontSize32 fontPoppins m-0"
                >
                  {{ company.name }}
                </h1>
              </div>
            </div>
            <div class="row">
              <div class="col-12">
                <v-app>
                  <span
                    v-show="roundedNote && roundedNote != 0"
                    class="fontAlice fontSize22"
                  >{{ round(company.note) }}</span>
                  <v-rating
                    v-if="company.note"
                    :style="{display: 'inline-block'}"
                    class="readonly"
                    half-increments
                    readonly
                    color="orange"
                    half-icon="fas fa-star-half-alt"
                    size="20"
                    length="5"
                    :value="company.note"
                    background-color="grey"
                  />
                </v-app>
              </div>
            </div>

            <div class="row mb-1">
              <div class="col-12">
                <span class="fontSize18 fontAlice">{{ company.category.name }}</span>
              </div>
            </div>
            <div
              v-if="company.sous_categorys.length !=0 && isSubscriptionActive"
              class="row activity-domain"
            >
              <div class="col-12 ">
                <span
                  v-for="(sousCategory, index2) in company.sous_categorys"
                  :key="'sousCategory_' + index2"
                  class="multiselect__tag_no_icon"
                >{{ sousCategory.name }}</span>
              </div>
            </div>
          </div>
        </div>
        <div class="col-2" />
      </div>

      <div class="row navigation-menu card-skb">
        <div class="col-12">
          <a
            href="#presentation"
            class="fontPoppins"
            :class="presentationActive ? 'navigation-link-active': 'navigation-link'"
            @click="activePresentation()"
          >{{ $t("company.nav_title.presentation") }}</a>
          <a
            href="#job-offers"
            class="fontPoppins"
            :class="jobOfferActive ? 'navigation-link-active': 'navigation-link'"
            @click="activeJobOffer()"
          >{{ $t("company.nav_title.job_offers") }}</a>
          <a
            href="#comments"
            class="fontPoppins"
            :class="commentActive ? 'navigation-link-active': 'navigation-link'"
            @click="activeComment()"
          >{{ $t("company.nav_title.comments") }}</a>
        </div>
      </div>
      <div v-show="presentationActive">
        <presentation-page
          :is-subscription-active="isSubscriptionActive"
          :company="company"
        />
      </div>

      <div v-show="jobOfferActive">
        <p>Work in progress ... Listing des offres d'emplois</p>
      </div>

      <div v-show="commentActive">
        <comment-page
          :company="company"
          :utilisateur-id="utilisateurId"
        />
      </div>
    </div>
  </div>
</template>
<script>
  import axios from 'axios';
  import presentationPage from './presentation';
  import commentPage from './comment';
  import _ from 'lodash';
  export default {
    components: {
      presentationPage,
      commentPage
    },
    props: {
      companyUrlName: {
        type: String,
        default: ''
      },
      isSubscriptionActive: {
        type: Boolean,
        default: false
      },
      utilisateurId: {
        type: Number,
        default: null
      }
    },

    data() {
      return {
        loading: true,
        company: null,
        presentationActive: true,
        jobOfferActive: false,
        commentActive: false,
      };
    },
    computed: {
      roundedNote() {
        let note = null;

        if (this.company) {
          note = _.cloneDeep(this.company.note);
          if (note) {
            note = this.round(note);
          }
        }
        return note;
      }
    },
    async created() {
      this.setPageByUrlParameter();
      return axios.get('/api/entreprise/' + this.companyUrlName, {params: {'serial_group': 'api_companies'}})
        .then(response => {
          this.company = _.cloneDeep(response.data);
          this.loading = false;
        }).catch(e => {
          this.loading = false;
          this.$handleError(e);
        });
    },
    methods: {
      activePresentation() {
        this.presentationActive = true;
        this.jobOfferActive = false;
        this.commentActive = false;
      },
      activeJobOffer() {
        this.presentationActive = false;
        this.jobOfferActive = true;
        this.commentActive = false;
      },
      activeComment() {
        this.presentationActive = false;
        this.jobOfferActive = false;
        this.commentActive = true;
      },
      round(note) {
        return _.round(note, 1);
      },
      setPageByUrlParameter() {
        let page = this.getUrlParameter('page');
        if (page === 'comments') {
          this.activeComment();
        } else if (page === 'presentation') {
          this.activePresentation();
        } else if (page === 'joboffer') {
          this.activeJobOffer();
        } else {
          this.activePresentation();
        }
      },
      getUrlParameter(sParam) {
        var sPageURL = window.location.search.substring(1),
            sURLVariables = sPageURL.split('&'),
            sParameterName,
            i;
        for (i = 0; i < sURLVariables.length; i++) {
          sParameterName = sURLVariables[i].split('=');

          if (sParameterName[0] === sParam) {
            return typeof sParameterName[1] === undefined ? true : decodeURIComponent(sParameterName[1]);
          }
        }
        return false;
      }
    },
  };
</script>
