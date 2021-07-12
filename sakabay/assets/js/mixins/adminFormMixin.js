import axios from 'axios';
import _ from 'lodash';

export default {
  watch: {
    /**
     * Remove errors of the field when something is typed.
     */
    formFields: {
      handler() {
        Object.keys(this.formErrors).forEach(field => {
          if (this.formFields[field]) {
            if (this.formErrors[field].length > 0) {
              this.$removeFieldErrors(field);
            }
          } else if (this.formFields.utilisateur && this.formFields.utilisateur[field]) {
            if (this.formErrors[field].length > 0) {
              this.$removeFieldErrors(field);
            }
          } else if (this.formFields.address && this.formFields.address[field]) {
            if (this.formErrors[field].length > 0) {
              this.$removeFieldErrors(field);
            }
          }
        });
      },
      deep: true
    },
  },

  data() {
    return {
      formFields: new Object(),
    };
  },

  methods: {
    /**
     * Submit the form and handle the response.
     * @returns {Promise}
     */
    submitForm() {
      this.formFields._token = _.cloneDeep(this.token);
      let fieldsDataFunction = this.getFormFieldsData || this.$getFormFieldsData;
      return axios
        .post(this.API_URL, fieldsDataFunction.bind(this)(this.formFields))
        .then(response => {
          window.location.assign(response.headers.location);
        })
        .catch(e => {
          this.loading = false;
          if (e.response && e.response.status && e.response.status === 400) {
            if (e.response.headers['x-message']) {
              this.errorMessage = decodeURIComponent(e.response.headers['x-message']);
            } else  {
              this.$handleFormError(e.response.data);
            }
          } else {
            this.$handleError(e);
          }
        });
    },

    /**
     * Call API to delete the edited entity.
     * @returns {Promise}
     */
    deleteEntity() {
      this.loading = true;
      return axios
        .delete(this.API_URL)
        .then(response => {
          window.location.assign(response.headers.location);
        }).catch(e => {
          if (_.get(e, 'response.data.code') === 409 && this.mainError !== undefined) {
            this.mainError = e.response.data.message;
          }
          this.loading = false;
          this.$handleError(e);
        });
    },
  },
};
