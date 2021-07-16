<template>
  <li class="row align-items-center notification-item">
    <div
      class="col-11"
    >
      <div
        :class="notif.seen ? '' : 'orange-skb'"
        class="row align-items-center py-3 "
      >
        <div class="col-9">
          <a
            v-if="notif.notification.link !== null"
            class="fontPoppins fontSize14 cursor-pointer"
            @click.prevent="goToNotificationSubject()"
          >{{ notif.notification.message }}</a>
          <span
            v-else
            class="fontPoppins fontSize14"
          >{{ notif.notification.message }}</span>
        </div>
        <div class="col-2 text-center">
          <span class="fontPoppins fontSize14">{{ notificationDate }}</span>
        </div>
      </div>
    </div>
    <div class="col-1 text-center">
      <a
        v-if="!notif.seen"
        data-toggle="tooltip"
        :title="$t('dashboard.notification.tooltip.mark_as_seen')"
        class="text-center badge badge-complete"
        href="#"
        @click.prevent="markAsSeen()"
      >
        <font-awesome-icon :icon="['fas', 'check']" />
      </a>
      <a
        v-else
        data-toggle="tooltip"
        :title="$t('dashboard.notification.tooltip.mark_as_unseen')"
        class="text-center badge grey-bg-skb"
        href="#"
        @click.prevent="markAsUnSeen()"
      >
        <font-awesome-icon :icon="['fas', 'check']" />
      </a>
      <a
        class="text-center badge orange-gradiant"
        data-toggle="tooltip"
        :title="$t('dashboard.notification.tooltip.delete')"
        href="#"
        @click.prevent="deleteNotification()"
      >
        <font-awesome-icon :icon="['fas', 'times']" />
      </a>
    </div>
  </li>
</template>
<script>
  import axios from 'axios';
  import moment from 'moment';
  export default {
    props: {
      notif: {
        type: Object,
        default: null
      }
    },
    data() {
      return {
        loading: false
      };
    },
    computed: {
      notificationDate() {
        let dtCreated = moment(this.notif.notification.date, 'YYYY-MM-DD H:m:s').format('DD/MM/YYYY H:m:ss');
        if (moment().diff(moment(this.notif.notification.date), 'days') >  7) {
          return 'le ' + moment(this.notif.notification.date, 'YYYY-MM-DD H:m:s').format('D MMMM, H:mm');
        }
        else {
          return moment(dtCreated, 'DD/MM/YYYY H:m:ss').fromNow();
        }
      },
    },
    methods: {
      /**
       * Marks the notification as read asynchronously.
       * Immediately followed by a redirection to the subject of the notification. No further handling required.
       */
      goToNotificationSubject() {
        if (this.notif.notification.link) {
          this.markAsSeen(this.notif.notification.link);
        }
      },

      markAsSeen(location = '') {
        return axios.post('/notification/' + this.notif.notifiable.id + '/mark_as_seen/' + this.notif.notification.id)
          .then(r => {
            this.$emit('notification-seen');
            if (location) {
              window.location.assign(location);
            }
          })
          .catch(e => {
            this.$handleError(e);
          });
      },

      markAsUnSeen() {
        return axios.post('/notification/' + this.notif.notifiable.id + '/mark_as_unseen/' + this.notif.notification.id)
          .then(r => {
            this.$emit('notification-unseen');
          })
          .catch(e => {
            this.$handleError(e);
          });
      },

      deleteNotification() {
        return axios.post('/notification/' + this.notif.notifiable.id + '/delete/' + this.notif.notification.id)
          .then(r => {
            this.$emit('notification-deleted');
          }).catch(e => {
            this.$handleError(e);
          });
      }
    },
  };
</script>
