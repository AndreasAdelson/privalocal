<template>
  <div class="container skb-body">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div
      v-if="subscriptions.length > 0"
      class="row mt-4"
    >
      <div class="col-12">
        <b-card-group deck>
          <b-card
            v-for="(subscription, index) in subscriptions "
            :key="'subscription_'+index"
            :img-src="getImage(subscription)"
            img-alt="Image"
            img-height="400"
            img-width="150"
            style="max-width: 30rem;"
            img-top
          >
            <b-card-text v-if="subscription.advantages">
              <ul class="fa-ul ml-2">
                <li
                  v-for="(advantage, indexAdvantage) in subscription.advantages"
                  :key="'advantage_' + indexAdvantage"
                  class="mb-2"
                >
                  <font-awesome-icon
                    :icon="['fas', 'check']"
                    class="green-skb mr-2"
                  />
                  <span>{{ advantage.message }}</span>
                </li>
              </ul>
            </b-card-text>
            <a
              v-if="subscription.code !== 'FRE'"
              class="btn button_skb_yellow"
              :href="'subscription/'+subscription.name.toLowerCase()"
            >
              {{ $t('subscription.subscribe') }}
            </a>
          </b-card>
        </b-card-group>
      </div>
    </div>
  </div>
</template>

<script>

  import axios from 'axios';

  export default {
    props: {

      subscriptionId: {
        type: Number,
        default: null
      },
    },
    data() {

      return {
        loading: false,
        subscriptions: [],
        currentName: null,
      };
    },
    created() {
      this.getAllSubscriptions();

    },
    methods: {
      getAllSubscriptions() {
        this.loading = true;
        return axios.get('/api/subscribes')
          .then(response => {
            this.subscriptions = response.data;
            this.loading = false;
          }).catch(error => {
            this.$handleError(error);
            this.loading = false;
          });

      },
      getImage(subscription) {
        let url = 'build/';
        if (subscription.code === 'FRE') {
          url += 'gratuite.jpg';
        } else if (subscription.code === 'PRE') {
          url += 'premium.jpg';
        }
        return url;
      }
    },
  };
</script>


<style>
</style>
