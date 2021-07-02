<template>
  <div class="skb-body container">
    <div class="container-fluid">
      <h3 class="fontUbuntuItalic orange-skb text-center p-1">
        {{ $t('company.title_create_company') }}
      </h3>
      <div v-show="loading">
        <div class="loader-container-full">
          <div class="loader" />
        </div>
      </div>
      <div>
        <b-card
          overlay
          img-src="https://picsum.photos/900/250/?image=3"
          img-alt="Card Image"
          text-variant="black"
          title="Que signifie référencer son entreprise sur Sakabay ?"
          class="reference-card"
        >
          <b-card-text
            align="justify"
            class="fontSize16"
          >
            Référencer son entreprise sur Sakabay est un moyen rapide, efficace et gratuit d'améliorer votre visibilité tout en promouvant son activité.
            Au-delà du référencement, il vous sera possible d'agrandir vos équipes en recrutant, ainsi que de renforcer vos partenariats avec d'autres entreprises en réalisant des demandes de services depuis l'application.
            </br>
            Une partie payante permet en outre, de vous mettre en relation avec les consommateurs, en vous donnant accès à leurs besoins ainsi qu'avec les entreprises inscrites sur Sakabay.
            Le but étant d'accroître vos activités ainsi que de renforcer les liens qui unissent les entrepreneurs au sein d'un même territoire.
            </br>
            Si vous détenez plusieurs entreprises, il n'y aura pas besoin de créer 'n' comptes, il vous suffit de remplir plusieurs fois le formulaire qui suit. Ainsi vous pourrez gérer la totalité de vos entreprises depuis un seul et même compte.
            </br>
            Afin de vous faire profiter de la totalité de l'expérience Sakabay, nous vous offrons l'abonnement Premium pendant 2 mois à la suite de votre inscription.
            </br>
            N'attendez plus, n'hésitez plus. Inscrivez-vous !
          </b-card-text>
        </b-card>
      </div>
      <div
        v-if="subscriptions.length > 0"
        class="row my-4"
      >
        <div class="col-12">
          <b-card-group
            deck
            class="justify-content-between"
          >
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
            </b-card>
          </b-card-group>
        </div>
      </div>
      <button
        class="btn button_skb_yellow"
        @click="$goTo('/entreprise/new')"
      >
        {{ $t('company.title_create_company') }}
      </button>
    </div>
  </div>
</template>
<script>
  import axios from 'axios';
  export default {
    data() {
      return {
        subscriptions: [],
        loading: false
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
