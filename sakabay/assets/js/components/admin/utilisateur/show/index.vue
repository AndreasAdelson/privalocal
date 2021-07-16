<template>
  <div class="skb-body container ">
    <div v-if="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div v-else-if="utilisateur">
      <div
        v-if="canEdit"
        class="row mt-3 "
      >
        <div class="col-6">
          <h1 class="fontUbuntuItalic orange-skb">
            {{ $t('commons.detail') }}
          </h1>
        </div>
        <div class="col-6 justify-content-end">
          <a
            class="float-right"
            :href="'/admin/utilisateur/edit/' + utilisateurId"
          >
            <b-button class="pull-right  button_skb">{{ $t('commons.edit') }}</b-button>
          </a>
        </div>
      </div>
      <a @click="goBack()">
        <button
          :title="$t('commons.go_back')"
          type="button"
          class="w-40px mt-4 p-0 rounded-circle btn-close btn"
        >
          <font-awesome-icon :icon="['fas', 'times']" />
        </button>
      </a>
      <div class="register-card mt-3 w-100 h-100">
        <div class="row my-2">
          <div class="col-6">
            <b-img
              v-if="utilisateur && utilisateur.image_profil != null"
              class="rounded-circle logo-size-75"
              :src="urlImageProfil"
            />
            <avatar
              v-else
              class="p-0"
              :username="utilisateur && utilisateur.first_name + ' ' + utilisateur.last_name"
              :size="75"
            />
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('user.fields.last_name') }}</span>
          </div>
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('user.fields.first_name') }}</span>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ utilisateur.last_name.toUpperCase() }}</span>
          </div>
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ utilisateur.first_name }}</span>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('user.fields.email') }}</span>
          </div>
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('user.fields.username') }}</span>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ utilisateur.email }}</span>
          </div>
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ utilisateur.username.toUpperCase() }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import axios from 'axios';
  import Avatar from 'vue-avatar';

  export default {
    components: {
      Avatar
    },
    props: {
      utilisateurId: {
        type: Number,
        default: null
      },
      canEdit: {
        type: Boolean,
        default: false
      },
      urlPrecedente: {
        type: String,
        default: null
      }
    },
    data() {
      return {
        utilisateur: new Object(),
        loading: true
      };
    },
    async created() {
      this.loading = true;
      return axios.get('/api/admin/utilisateurs/' + this.utilisateurId)
        .then(response => {
          this.utilisateur = response.data;
          this.loading = false;
        }).catch(error => {
          this.$handleError(error);
          this.loading = false;
        });

    },
    methods: {
      goBack() {
        this.$goTo(this.urlPrecedente);
      },
    },
  };
</script>>
