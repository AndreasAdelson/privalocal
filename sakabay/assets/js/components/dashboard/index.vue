<template>
  <div class="skb-body container-fluid p-4 dashboard">
    <div v-show="!utilisateur">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <!-- Content -->
    <div class="row py-3">
      <div class="col-3">
        <font-awesome-icon
          class="grey-skb fontSize20 mr-2"
          :icon="['fas', 'home']"
        />
        <h1 class="fontPoppins fontSize20 dashboard-title">
          {{ $t('dashboard.title') }}
        </h1>
      </div>
    </div>
    <div
      v-if="utilisateur && companies.length > 0"
      class="row"
    >
      <div class="col-3">
        <link-item
          icon-label="city"
          :button-text="$tc('dashboard.link.n_company', companies.length)"
          class-color="flat-color-5"
          :small-text="false"
          :disabled="false"
          url="/entreprises/list"
        />
      </div>
      <div class="col-3">
        <link-item
          icon-label="gem"
          :button-text="$t('dashboard.link.subscription')"
          class-color="yellow-login-skb"
          :small-text="true"
          url="/subscription"
          :disabled="!hasOneValidated"
        />
      </div>
      <div class="col-3">
        <link-item
          icon-label="copy"
          :button-text="$t('dashboard.link.opportunity')"
          class-color="flat-color-6"
          :small-text="true"
          url="/opportunities/list"
          :disabled="!hasOneSusbcribed"
        />
      </div>
      <div class="col-3">
        <link-item
          icon-label="paste"
          :button-text="$t('dashboard.link.recruit')"
          class-color="blue-skb"
          :small-text="true"
          :disabled="!hasOneValidated"
        />
      </div>
    </div>
    <div class="row">
      <div class="col-3">
        <link-item
          icon-label="edit"
          :button-text="$t('dashboard.link.request_service')"
          class-color="flat-color-1"
          url="/services/list"
          :small-text="false"
        />
      </div>
      <div class="col-3">
        <link-item
          icon-label="book-open"
          :button-text="$t('dashboard.link.documents')"
          class-color="flat-color-4"
          :small-text="true"
        />
      </div>
      <div class="col-3">
        <link-item
          icon-label="address-card"
          :button-text="$t('dashboard.link.nomination')"
          class-color="flat-color-2"
          :small-text="true"
        />
      </div>
    </div>
    <!-- Notifications center -->
    <div class="orders">
      <div class="row">
        <div class="mx-auto col-10">
          <div class="card">
            <div class="card-body navigation-menu">
              <div class="row">
                <!-- Gestion affichage information de la carte -->
                <div class="col-2">
                  <ul class="list-unstyled text-center scroll-h300">
                    <!-- Recent Notification bouton -->
                    <vuescroll :ops="opsButton">
                      <a
                        :class="notificationActive ? 'navigation-dashboard-link-active': 'navigation-dashboard-link'"
                        class="w-90"
                        @click="activeNotification()"
                      >
                        <li>
                          <span class="box-title notification-title">
                            {{ $t('dashboard.recent_activity') }}
                          </span>
                          <span
                            v-if="numberUnseen != 0"
                            class="fontPoppins fontSize12 py-1 px-2 orange-gradiant white-skb rounded"
                          >{{ numberUnseen }}</span>
                        </li>
                      </a>
                      <!-- Subscription History bouton -->
                      <a
                        v-if="utilisateur && utilisateur.companys.length > 0"
                        :class="historyActive ? 'navigation-dashboard-link-active': 'navigation-dashboard-link'"
                        href="#"
                        class="w-90"
                        @click="activeHistory()"
                      >
                        <li>
                          <span class="box-title notification-title">
                            {{ $t('dashboard.susbcription_history') }}
                          </span>
                        </li>
                      </a>
                    </vuescroll>
                  </ul>
                </div>
                <div class="col-10">
                  <!-- Notifications List -->
                  <div
                    v-if="notificationActive"
                    class="row scroll-h300"
                  >
                    <vuescroll>
                      <div class="col-12">
                        <ul class="notification-item mb-0">
                          <notification-item
                            v-for="(notif, index) in notifications"
                            :key="notif.id"
                            :notif="notif"
                            @notification-seen="onNotificationSeen(index)"
                            @notification-unseen="onNotificationUnSeen(index)"
                            @notification-deleted="onNotificationDeleted(index)"
                          />
                        </ul>
                      </div>
                    </vuescroll>
                  </div>
                  <div
                    v-else-if="historyActive && utilisateur && companies"
                    class="row scroll-h300"
                  >
                    <!-- History SUbscription List -->
                    <vuescroll>
                      <div class="col-12">
                        <history-item
                          :company-subscriptions="companySubscriptions"
                          :with-company-name="true"
                        />
                      </div>
                    </vuescroll>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <!-- Info line -->
    <div class="row">
      <div class="col-sm-12 mb-4">
        <div class="card-group">
          <info-item
            title="87500"
            sub-title="Visitors"
            icon-class="users"
            color-class="bg-flat-color-1"
          />
          <info-item
            title="385"
            sub-title="New Clients"
            icon-class="user-plus"
            color-class="bg-flat-color-2"
          />
          <info-item
            title="1238"
            sub-title="Products sold"
            icon-class="cart-plus"
            color-class="bg-flat-color-3"
          />
          <info-item
            title="28%"
            sub-title="Returning Visitors"
            icon-class="undo-alt"
            color-class="bg-flat-color-4"
          />
          <info-item
            title="5:34:11"
            sub-title="Avg. Time"
            icon-class="clock"
            color-class="bg-flat-color-5"
          />
          <info-item
            title="973"
            sub-title="COMMENTS"
            icon-class="users"
            color-class="bg-flat-color-1"
          />
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import LinkItem from './link-item.vue';
  import NotificationItem from './notification-item.vue';
  import HistoryItem from '../commons/history-item.vue';
  import InfoItem from './info-item.vue';
  import vuescroll from 'vuescroll';
  import axios from 'axios';
  import _ from 'lodash';
  import moment from 'moment';

  export default {
    components: {
      LinkItem,
      NotificationItem,
      HistoryItem,
      InfoItem,
      vuescroll
    },
    props:
      {
        utilisateurId: {
          type: Number,
          default: null
        }
      },
    data() {
      return {
        opsButton: {
          bar: {
            keepShow: false,
            minSize: 0.3,
          },
          rail: {
            background: '#c5c9cc',
            opacity: 0.4,
            size: '6px',
            specifyBorderRadius: false,
            gutterOfEnds: '1px',
            gutterOfSide: '2px',
            keepShow: false
          },
        },
        notifications: [
        ],
        companies: [],
        utilisateur: null,
        notificationActive: true,
        historyActive: false,
        hasOneSusbcribed: false
      };
    },
    computed: {
      numberUnseen() {
        let unseen = 0;
        this.notifications.forEach(notif => {
          if (!notif.seen) {
            unseen += 1;
          }
        });
        return unseen;
      },

      companySubscriptions() {
        let history = [];
        this.companies.forEach(company => {
          company.company_subscriptions.forEach(subscription => {
            subscription.company_name = company.name;
          });
          history = _.concat(history, _.cloneDeep(company.company_subscriptions));
        });
        return  history;
      },

      hasOneValidated() {
        let validated;
        validated = _.filter(this.companies, company => {
          return company.company_statut.code === 'VAL';
        });
        if (validated.length > 0) {
          this.getSubscribedCompany();
          return true;
        }
        return false;
      }
    },
    created() {
      this.getUser();
      this.getNotifications();
    },
    mounted() {
      this.$forceUpdate();

    },
    methods: {
      getNotifications() {
        return axios.get('/notification/all/' + this.utilisateurId)
          .then(response => {
            this.notifications = response.data;
          }).catch(e => {
            this.$handleError(e);
          });
      },

      onNotificationSeen(index) {
        this.notifications[index].seen = true;
        this.$forceUpdate();
      },

      onNotificationUnSeen(index) {
        this.notifications[index].seen = false;
      },

      onNotificationDeleted(index) {
        this.loading = true;
        this.notifications.splice(index, 1);
        this.loading = false;
      },

      sortNotifications() {
        this.notifications.sort(function(a, b) {
          return a.notification.date - b.notification.date;
        });
      },

      getUser() {
        return axios.get('api/admin/utilisateurs/' + this.utilisateurId, {params: { 'serial_group': 'api_dashboard_utilisateur' }})
          .then(response => {
            this.utilisateur = _.cloneDeep(response.data);
            this.companies = _.cloneDeep(this.utilisateur.companys);
          }).catch(e => {
            this.$handleError(e);
          });
      },

      onNotificationMarked(index) {
        return this.notifications[index].seen = true;
      },

      activeNotification() {
        this.notificationActive = true;
        this.historyActive = false;
      },
      activeHistory() {
        this.notificationActive = false;
        this.historyActive = true;
      },

      getSubscribedCompany() {
        if (this.companySubscriptions.length > 0) {
          let subscription = this.companySubscriptions.find((item) => {
            return moment(item.dt_fin, 'DD/MM/YYYY HH:mm:ss').isAfter();
          });
          if(subscription) {
            this.hasOneSusbcribed = true;
          }
          else {
            this.hasOneSusbcribed = false;
          }
        }
      }
    },
  };
</script>
