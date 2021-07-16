<template>
  <a :href="'/entreprise/' + company.url_name ">
    <div class="card mb-3">
      <div class="card-body">
        <div class="row">
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
            <div class="card-main-info">
              <div class="row">
                <div class="col-9">
                  <span class="fontPoppins fontSize20 fontW600">{{ company.name }}</span>
                </div>
                <div
                  v-show="roundedNote && roundedNote != 0"
                  class="col-3 text-right"
                >
                  <span
                    class="fontAlice fontSize18"
                  >{{ round(company.note) }}</span>
                  <v-app
                    :style="{display: 'inline-block'}"
                  >
                    <v-rating
                      v-if="company.note"
                      class="readonly"
                      half-increments
                      readonly
                      color="orange"
                      half-icon="fas fa-star-half-alt"
                      size="10"
                      length="5"
                      :value="company.note"
                      background-color="grey"
                    />
                  </v-app>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <font-awesome-icon
                    class="orange-login-skb"
                    :icon="['fas', 'map-marker-alt']"
                  />
                  <span class="fontPoppins fontSize14 fontW600 ">{{ company.address.postal_address }}, {{ company.city.name }} </span>
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <span class="fontPoppins fontSize14 italic">{{ company.category.name }} </span>
                </div>
              </div>
              <div
                v-if="isSubscriptionActive && company.sous_categorys.length != 0"
                class="row"
              >
                <div class="col-12">
                  <span
                    v-for="(sousCategory, index) in company.sous_categorys"
                    :key="'sousCategory_' + index"
                    class="multiselect__tag_no_icon"
                  >{{ sousCategory.name }}</span>
                </div>
              </div>
              <div
                v-if="company.description_clean"
                class="row"
              >
                <div class="col-12">
                  <p class="mb-0 fontAlice fontSize16">{{ company.description_clean | truncate(150, '...') }}</p>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </a>
</template>
<script>
  import moment from 'moment';
  import _ from 'lodash';
  export default {
    props: {
      company: {
        type: Object,
        default: () => new Object()
      },

    },
    data() {
      return {
        isSubscriptionActive: false
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
    created() {
      this.sortSubscriptions();

    },
    methods: {
      sortSubscriptions() {
        if (this.company.company_subscriptions.length != 0) {
          let subscriptions = _.cloneDeep(this.company.company_subscriptions);
          subscriptions = _.orderBy(subscriptions, [
            function(subscription) {
              let dtFin = moment(subscription.dt_fin, 'DD/MM/YYYY HH:mm:ss').format('DD/MM/YYYY H:mm:ss');
              return new Date(dtFin.toString());
            }
          ], ['desc']);
          let lastDtFinSubscription = moment(subscriptions[0].dt_fin, 'DD/MM/YYYY HH:mm:ss').format('DD/MM/YYYY H:mm:ss');
          if (moment(lastDtFinSubscription, 'DD/MM/YYYY HH:mm:ss').isAfter()) {
            this.isSubscriptionActive = true;
          } else {
            this.isSubscriptionActive = false;
          }
        } else {
          this.isSubscriptionActive = false;
        }
      },
      round(note) {
        return _.round(note, 1);
      }
    },
  };
</script>
