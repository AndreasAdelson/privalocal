<template>
  <div class="container skb-body">
    <div v-show="loading">
      <div class="loader-container-full">
        <div class="loader" />
      </div>
    </div>
    <button
      :title="$t('commons.go_back')"
      type="button"
      class="w-40px p-0 rounded-circle btn-close btn"
      @click.prevent="goBack()"
    >
      <font-awesome-icon :icon="['fas', 'times']" />
    </button>
    <form>
      <div class="">
        <div class="register-card w-100 h-100">
          <!-- First row  -->
          <div class="row mb-3">
            <div
              v-if="urlImageProfil"
              class="col-2"
            >
              <b-img
                :src="urlImageProfil"
                class="company-image-2"
              />
            </div>
            <div class="col-4 my-auto">
              <div class="form-group mb-0">
                <fieldset
                  id="imageProfil"
                  class="imageProfil"
                >
                  <input
                    ref="imageProfil"
                    name="imageProfil"
                    type="file"
                    @change="onFileSelected"
                  >
                  <div
                    v-for="errorText in formErrors.imageProfil"
                    :key="'imageProfil_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
          <!-- Second row  -->
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="name"
                  class="name"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('company.fields.name') }}</label>
                  <input
                    v-model="formFields.name"
                    v-validate="'required'"
                    disabled
                    type="text"
                    name="name"
                    class="form-control"
                    :placeholder="$t('company.placeholder.name')"
                  >
                  <div
                    v-for="errorText in formErrors.name"
                    :key="'name_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="numSiret"
                  class="numSiret"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('company.fields.num_siret') }}</label>
                  <input
                    v-model="formFields.numSiret"
                    v-validate="'required'"
                    disabled
                    name="numSiret"
                    type="text"
                    class="form-control"
                    :placeholder="$t('company.placeholder.num_siret')"
                  >
                  <div
                    v-for="errorText in formErrors.numSiret"
                    :key="'numSiret_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
          <!-- Third row  -->
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="category"
                  class="category"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('company.table.fields.category') }}</label>

                  <multiselect
                    v-model="formFields.category"
                    v-validate="'required'"
                    disabled
                    :options="category"
                    name="category"
                    placeholder="Selectionner categorie"
                    :searchable="false"
                    :close-on-select="false"
                    :show-labels="false"
                    label="name"
                    track-by="name"
                    open-direction="below"
                  />
                  <div
                    v-for="errorText in formErrors.category"
                    :key="'category_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="urlName"
                  class="urlName"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('company.table.fields.url_name') }}</label>
                  <input
                    v-model="formFields.urlName"
                    v-validate="'required_urlName'"
                    disabled
                    type="text"
                    name="urlName"
                    class="form-control"
                    :placeholder="$t('company.placeholder.url_name')"
                  >
                  <div
                    v-for="errorText in formErrors.urlName"
                    :key="'urlName_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
          <!-- Fourth row -->
          <div class="row">
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="postalAddress"
                  class="postalAddress"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('company.table.fields.address.postal_address') }}</label>
                  <input
                    v-model="formFields.address.postalAddress"
                    v-validate="'required'"
                    name="postalAddress"
                    type="text"
                    :maxlength="255"
                    class="form-control"
                    :placeholder="$t('company.placeholder.postal_address')"
                    @blur="() => {
                      if (formFields.address.postalCode && formFields.address.postalAddress && formFields.city) {
                        getLongLat();
                      }
                    }"
                  >
                  <div
                    v-for="errorText in formErrors.postalAddress"
                    :key="'postalCode_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
            <div class="col-6">
              <div class="form-group">
                <fieldset
                  id="postalCode"
                  class="postalCode"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('company.table.fields.address.postal_code') }}</label>
                  <input
                    v-model="formFields.address.postalCode"
                    v-validate="'required'"
                    type="text"
                    name="postalCode"
                    class="form-control"
                    :placeholder="$t('company.placeholder.postal_code')"
                    onkeypress="return event.charCode === 0 || event.charCode === 47 || (event.charCode >= 48 && event.charCode <= 57)"
                    @blur="() => {
                      if (formFields.address.postalCode && formFields.address.postalAddress && formFields.city) {
                        getLongLat();
                      }
                    }"
                  >
                  <div
                    v-for="errorText in formErrors.postalCode"
                    :key="'postal_name_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>
          <!-- Map row -->
          <div v-if="loadingMap">
            <div class="loader3" />
          </div>
          <div
            v-if="formFields.address.latitude && formFields.address.longitude"
            class="row mb-3"
          >
            <div
              class="col-12"
              style="height:300px"
            >
              <v-map
                :zoom="16"
                :center="[formFields.address.latitude, formFields.address.longitude]"
                style="height:300px"
              >
                <v-marker
                  :draggable="true"
                  :lat-lng.sync="position"
                />
                <v-tile-layer url="http://{s}.tile.osm.org/{z}/{x}/{y}.png" />
              </v-map>
            </div>
            <div class="col-12 warning-message">
              <span class="fontUbuntuItalic fontSize16 red-skb"><font-awesome-icon
                class="mx-1"
                :icon="['fas', 'exclamation-triangle']"
              />{{ $t('company.table.fields.address.warning_message') }}</span>
            </div>
          </div>
          <!-- Fifth row -->
          <div class="row mb-3">
            <div class="col-6">
              <fieldset
                id="city"
                class="city"
              >
                <label class="fontUbuntuItalic fontSize16">{{ $t('company.table.fields.city') }}</label>
                <autocomplete
                  ref="autocomplete"
                  v-model="formFields.city"
                  :required="true"
                  name="city"
                  :min="2"
                  :debounce="500"
                  :on-should-render-child="$getCityLabel"
                  :on-select="setCity"
                  :placeholder="$t('company.placeholder.city')"
                  param="autocomplete"
                  url="/api/admin/cities"
                  anchor="label"
                  :classes="{input: 'form-control'}"
                />
                <div
                  v-for="errorText in formErrors.city"
                  :key="'city_' + errorText"
                >
                  <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                </div>
              </fieldset>
            </div>
            <div
              v-if="sousCategorys && sousCategorys.length != 0"
              class="col-6"
            >
              <div class="form-group">
                <fieldset
                  id="sousCategory"
                  class="sousCategory"
                >
                  <label class="fontUbuntuItalic fontSize16">{{ $t('company.table.fields.sous_category') }}</label>

                  <multiselect
                    v-model="formFields.sousCategorys"
                    :options="sousCategorys"
                    :disabled="!formFields.category"
                    name="sousCategory"
                    placeholder="Selectionner vos activités"
                    :searchable="false"
                    :close-on-select="false"
                    :show-labels="false"
                    :multiple="true"
                    label="name"
                    track-by="name"
                    open-direction="below"
                  />
                  <div
                    v-for="errorText in formErrors.sousCategorys"
                    :key="'sousCategory_' + errorText"
                  >
                    <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                  </div>
                </fieldset>
              </div>
            </div>
          </div>

          <!-- Sixth row -->
          <div class="row mb-3">
            <div class="col-12">
              <fieldset
                id="descriptionClean"
                class="descriptionClean"
              >
                <label class="fontUbuntuItalic fontSize16">{{ $t('company.table.fields.description') }}</label>

                <vue-editor
                  v-if="isSubscriptionActive"
                  id="descriptionFull"
                  v-model="formFields.descriptionFull"
                  class="white-bg-skb"
                  :placeholder="$tc('commons.maximum_n_characters', 2000)"
                  :maxlength="2000"
                  :editor-toolbar="customToolBar"
                />
                <textarea
                  v-else
                  v-model="formFields.descriptionClean"
                  class="form-control"
                  :placeholder="$tc('commons.maximum_n_characters', 2000)"
                  type="text"
                  :maxlength="2000"
                  :rows="10"
                />
                <div class="text-left pt-2 fontSize14">
                  <span>{{ $t('company.information.short_description') }}</span>
                </div>
                <div
                  :class="!$getNbCharactersLeft(formFields.descriptionClean, 2000) ? 'red-skb' : 'black-skb'"
                  class="text-right pt-2 fontSize12"
                >
                  {{ $tc('commons.n_charaters_left_short_description', $getNbCharactersLeft(formFields.descriptionClean, 150)) }}
                </div>
                <div
                  :class="!$getNbCharactersLeft(formFields.descriptionClean, 2000) ? 'red-skb' : 'black-skb'"
                  class="text-right pt-2 fontSize12"
                >
                  {{ $tc('commons.n_charaters_left', $getNbCharactersLeft(formFields.descriptionClean, 2000)) }}
                </div>

                <div
                  v-for="errorText in formErrors.descriptionClean"
                  :key="'descriptionClean_' + errorText"
                >
                  <span class="fontUbuntuItalic fontSize13 red-skb">{{ errorText }}</span>
                </div>
              </fieldset>
            </div>
          </div>

          <div class="row mt-5">
            <div class="col-6 offset-3">
              <button
                type="button"
                class="btn button_skb fontUbuntu italic"
                @click="$validateForm()"
              >
                {{ $t('commons.edit') }}
              </button>
            </div>
          </div>
        </div>
      </div>
    </form>
  </div>
  </form>
  </div>
</template>
<script>
  import axios from 'axios';
  import _ from 'lodash';
  import Autocomplete from 'vue2-autocomplete-js';
  import { VueEditor } from 'vue2-editor/dist/vue2-editor.core.js';

  export default {
    components: {
      Autocomplete,
      VueEditor
    },
    props: {
      utilisateurId: {
        type: Number,
        default: null
      },
      urlPrecedente: {
        type: String,
        default: null
      },
      companyUrlName: {
        type: String,
        default: null
      },
      isSubscriptionActive: {
        type: Boolean,
        default: false
      },
      token: {
        type: String,
        default: null
      }
    },
    data() {
      return {
        loading: true,
        loadingMap: false,
        category: [],
        sousCategorys: [],
        position: {
          lng: null,
          lat: null
        },
        urlImageProfil: null,
        imageProfilSelected: null,
        imageName: null,
        currentFullDescription: null,
        formFields: {
          name: null,
          category: null,
          numSiret: null,
          urlName: null,
          address: {
            postalAddress: null,
            postalCode: null,
            latitude: null,
            longitude: null,
          },
          city: null,
          descriptionFull: null,
          descriptionClean: null,
          imageProfil: null,
          sousCategorys: null,
          _token: null
        },
        formErrors: {
          name: [],
          category: [],
          numSiret: [],
          urlName: [],
          postalAddress: [],
          postalCode: [],
          longitude: [],
          latitude: [],
          city: [],
          descriptionFull: [],
          descriptionClean: [],
          imageProfil: [],
          sousCategorys: []
        },
        customToolBar: [
          [{header: [false,1,2,3,4,5,6]}],
          ['bold', 'italic', 'underline', 'strike'],
          [
            {align: '' },
            {align: 'center' },
            {align: 'right' },
            {align: 'justify' },

          ],
          [{list: 'ordered'}, {list: 'bullet'},{ list: 'check' }],
          [{ indent: '-1' }, { indent: '+1' }],
          ['blockquote'],
          [{ color: [] }],
          ['clean']
        ]
      };
    },
    computed: {
      fullDescription() {
        return this.formFields.descriptionFull;
      }
    },
    watch: {
      fullDescription(newValue) {
        if (newValue != this.currentFullDescription) {
          let cleanText = (document.getElementsByClassName('ql-editor') && document.getElementsByClassName('ql-editor').item(0)) ? document.getElementsByClassName('ql-editor').item(0).textContent : newValue;
          this.formFields.descriptionClean = cleanText.replace(/\n/g, ' ');
        }
      },
    },
    created() {
      let promises = [];
      promises.push(axios.get('/api/admin/categories'));
      if (this.companyUrlName) {
        promises.push(axios.get('/api/entreprise/' + this.companyUrlName, {params: {'serial_group': 'api_admin_companies'}}));
      }
      return Promise.all(promises).then(res => {
        this.category = res[0].data;
        if (this.companyUrlName) {
          let company = _.cloneDeep(res[1].data);
          this.currentFullDescription = company.description_full;
          if (company.image_profil) {
            this.urlImageProfil = '/build/images/uploads/' + this.companyUrlName + '/' + company.image_profil;
            this.formFields.imageProfil = company.image_profil;
          }
          this.$removeFieldsNotInForm(company, Object.keys(this.formFields));
          this.$setEditForm(company);
          this.setPosition(company.address);
          this.setCity(company.city, false);
          axios.get('/api/admin/sous-categories', {
            params: {
              category: this.formFields.category.id
            }
          }).then(res => {
            this.sousCategorys =  res.data;
          }).catch(e => {
            this.$handleError(e);
            this.loading = false;
          });
        }
        this.loading = false;
      }).catch(e => {
        this.$handleError(e);
        this.loading = false;
      });
    },
    methods: {
      setCity(city, callApi=true) {
        if (this.formFields.address.postalAddress && this.formFields.address.postalCode && callApi) {
          this.getLongLat();
        }
        this.formFields.city = city;
        this.$refs.autocomplete.setValue(city.name);
      },

      getLongLat() {
        let query = this.formFields.address.postalAddress + ' ' + this.formFields.city;
        let postalCode = this.formFields.address.postalCode;
        this.loadingMap = true;
        return axios.get('https://api-adresse.data.gouv.fr/search/', {
          params: {
            q: query,
            postcode: postalCode,
            limit: 10
          }
        }).then(response => {
          if (response.data.features) {
            this.$set(this.formFields.address, 'longitude', response.data.features[0].geometry.coordinates[0]);
            this.$set(this.formFields.address, 'latitude', response.data.features[0].geometry.coordinates[1]);
            this.position.lng = response.data.features[0].geometry.coordinates[0];
            this.position.lat = response.data.features[0].geometry.coordinates[1];
          }
          this.loadingMap = false;
        }).catch(e => {
          this.$handleError(e);
          this.loadingMap = false;
        });
      },

      setPosition(address) {
        this.position.lat = address.latitude;
        this.position.lng = address.longitude;
      },

      onFileSelected() {
        this.imageProfilSelected = this.$refs.imageProfil.files[0];
        this.imageName = this.$refs.imageProfil.files[0].name;
        this.urlImageProfil = URL.createObjectURL(this.imageProfilSelected);
      },

      submitForm() {
        this.loading = true;
        if (this.position.lat !== this.formFields.address.latitude && this.position.lng !== this.formFields.address.longitude) {
          this.formFields.address.longitude = this.position.lng.toFixed(6);
          this.formFields.address.latitude = this.position.lat.toFixed(6);
        }
        //Gestion lorsque le navigateur propose de compléter automatiquement. Ici on s'assure que la ville existe en base avant de la set.
        if(this.$refs.autocomplete.json.length > 0) {
          this.formFields.city = this.$refs.autocomplete.json[0];
        }

        this.formFields.utilisateur = _.cloneDeep(this.utilisateurId);
        this.formFields._token = _.cloneDeep(this.token);
        let formData = this.$getFormFieldsData(this.formFields);
        if (this.imageProfilSelected) {
          formData.append('file', this.imageProfilSelected);
        }
        return axios.post('/api/companies/edit/' + this.companyUrlName, formData)
          .then(response => {
            window.location.assign(response.headers.location);
          }).catch(e => {
            if (e.response && e.response.status && e.response.status == 400) {
              this.$handleFormError(e.response.data);
            } else {
              this.$handleError(e);
            }
            this.loading = false;
          });
      },
      goBack() {
        this.$goTo(this.urlPrecedente);
      },
    },
  };
</script>

