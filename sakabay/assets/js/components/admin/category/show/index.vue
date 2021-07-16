<template>
  <div class="skb-body container">
    <div v-if="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <div v-else>
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
            :href="'/admin/category/edit/' + categoryId"
          >
            <b-button class="button_skb">{{ $t('commons.edit') }}</b-button>
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
        <div class="row">
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('category.fields.name') }}</span>
          </div>
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('category.fields.code') }}</span>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ category.name.toUpperCase() }}</span>
          </div>
          <div class="col-6">
            <span class="fontHelveticaOblique fontSize18">{{ category.code }}</span>
          </div>
        </div>
        <div class="row">
          <div class="col-6">
            <span class="fontPatua fontSize20">{{ $t('category.fields.sous_category') }}</span>
          </div>
        </div>
        <div class="row mb-2">
          <div class="col-6">
            <ul>
              <li
                v-for="(sousCategory, index) in category.sous_categorys"
                :key="'sousCategory_' + index"
                class="fontHelveticaOblique fontSize18"
              >
                {{ sousCategory.name }}
              </li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
  import axios from 'axios';

  export default {
    components: {
    },
    props: {
      categoryId: {
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
        category: null,
        loading: true
      };
    },
    async created() {
      if (this.categoryId) {
        return axios.get('/api/admin/categories/' + this.categoryId)
          .then(response => {
            this.category = response.data;
            this.loading = false;
          }).catch(error => {
            this.$handleError(error);
            this.loading = false;
          });
      }
    },
    methods: {
      goBack() {
        this.$goTo(this.urlPrecedente);
      },
    },
  };
</script>>
