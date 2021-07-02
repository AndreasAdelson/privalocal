import _ from 'lodash';
import axios from 'axios';
import moment from 'moment';

const CnsFormUtils = {
  install(Vue, options) {
    /**
     * Remove all fields of given object which are not in the form fields, except the id.
     * Called when editing an etude
     * @param {object} entity
     * @param {array} keptFields
     */
    Vue.prototype.$removeFieldsNotInForm = function(entity, keptFields) {
      let finalKeptFields = [];
      keptFields.forEach(keptField => {
        finalKeptFields.push(this.$camelCaseToUnderscoreCase(keptField));
      });
      Object.keys(entity).forEach(field => {
        if (!finalKeptFields.includes(field)) {
          delete entity[field];
        }
      });
    };

    /**
     * Complete form error when Symfony Validator throw error on forms
     * @param {object} errorsData
     */
    Vue.prototype.$handleFormError = function(errorsData) {
      let receivedFormError = errorsData.errors.children;
      Object.keys(receivedFormError).forEach(field => {
        const fieldErrors = receivedFormError[field].errors;
        if (fieldErrors) {
          this.$addErrorInFormError(field, fieldErrors);
        } else {
          // Gestion du cas de création d'une entité enfant
          let receivedFormErrorChildren = receivedFormError[field].children;
          if (receivedFormErrorChildren) {
            Object.keys(receivedFormErrorChildren).forEach(children => {
              const childErrors = receivedFormErrorChildren[children].errors;
              if (childErrors) {
                this.$addErrorInFormError(children, childErrors);
              }
            });
          }
        }
      });
    };

    Vue.prototype.$addErrorInFormError = function(field, error) {
      if (error) {
        this.formErrors[field] = error;
      }
      let fieldElementHtml = document.getElementsByClassName(field)[0];
      let formElementByTagName =
        fieldElementHtml.getElementsByTagName('input')[0] ||
        fieldElementHtml.getElementsByTagName('textarea')[0];
      if (formElementByTagName) {
        formElementByTagName.classList.add('border-danger-skb');
      }
      let formElementByClassName =
      fieldElementHtml.getElementsByClassName('vue-rate-it-rating')[0] ||
      fieldElementHtml.getElementsByClassName('multiselect__tags')[0];
      if(formElementByClassName) {
        formElementByClassName.classList.add('border-danger-skb');
      }
    };

    Vue.prototype.$validateForm = function(newLocationHash) {
      this.$removeFormErrors();
      return this.$validator.validate().then(result => {
        if (result) {
          this.submitForm();
        } else {
          this.$validator.errors.items.forEach(error => {
            let fieldErrors = _.get(this.formErrors, error.field);

            if (fieldErrors === undefined) {
              fieldErrors = [];
            }

            //add custom rules gestion here -> see connectus
            const customRulesList = [];
            let errorMessage = error.msg;
            if (!customRulesList.includes(error.rule)) {
              errorMessage = this.$t('error_message_' + error.rule);
            }

            if (fieldErrors instanceof Array) {
              fieldErrors.push(errorMessage);
            } else {
              this.$set(fieldErrors, '-1', errorMessage);
            }
            let formErrors = _.cloneDeep(this.formErrors);
            _.set(formErrors, error.field, fieldErrors);
            this.formErrors = _.cloneDeep(formErrors);
            this.$addErrorInFormError(error.field);
          });
          if (newLocationHash) {
            window.location.hash = newLocationHash;
          }
        }
      });
    };

    Vue.prototype.$removeFormErrors = function() {
      Object.keys(this.formErrors).forEach(field => {
        this.$removeFieldErrors(field);
      });
      this.errorMessage = null;
    };

    Vue.prototype.$removeFieldErrors = function(field) {
      _.set(this.formErrors, field, []);
      let fieldsetElement = document.getElementsByClassName(field)[0];
      if (fieldsetElement) {
        let formElementByTagName =
        fieldsetElement.getElementsByTagName('input')[0] ||
        fieldsetElement.getElementsByTagName('textarea')[0];
      if (formElementByTagName) {
        formElementByTagName.classList.remove('border-danger-skb');
      }
      let formElementByClassName =
      fieldsetElement.getElementsByClassName('vue-rate-it-rating')[0] ||
      fieldsetElement.getElementsByClassName('multiselect__tags')[0];
      if(formElementByClassName) {
        formElementByClassName.classList.remove('border-danger-skb');
      }
      }
    };

    /**
     * Transform date fields of an object into parsed date fields.
     * Works recursively, so it also transforms date fields of type object of the given object.
     * @param {object} object
     */
     Vue.prototype.$transformDateFields = function(object) {
      if (object instanceof Array) {
        object.forEach(obj => {
          if (typeof obj === 'object') {
            this.$transformDateFields(obj);
          }
        });
      } else {
        Object.keys(object).forEach(field => {
          if (object[field]) {
            if (field.startsWith('dt')) {
              object[field] = moment(object[field], 'DD/MM/YYYY H:i:s').toDate();
            } else if (typeof object[field] === 'object') {
              this.$transformDateFields(object[field]);
            }
          }
        });
      }
    };

    Vue.prototype.$setEditForm = function(data) {
      let transformedData = _.cloneDeep(data);
      this.$transformDateFields(transformedData);
      Object.keys(this.formFields).forEach(field => {
        let underscoreField = this.$camelCaseToUnderscoreCase(field);
        if (this.formFields[field] instanceof Array) {
          this.formFields[field] = transformedData[underscoreField];
        } else if (this.formFields[field] instanceof Object) {
          Object.keys(this.formFields[field]).forEach(key => {
            let underscoreKey = this.$camelCaseToUnderscoreCase(key);
            if (transformedData[field][key] || transformedData[field][key] === 0 || transformedData[underscoreField][underscoreKey]) {
              this.formFields[field][key] = transformedData[underscoreField][underscoreKey];
            }
          });
        }
        else if (transformedData[field] || transformedData[field] === 0 || transformedData[underscoreField]) {
          this.formFields[field] = transformedData[underscoreField];
        }
      });
    };

    Vue.prototype.$camelCaseToUnderscoreCase = function(field) {
      return field.replace(/([A-Z])/g, '_$1').toLowerCase();
    };

    /**
     * Redirect to given URL
     * @param {String} url
     */
    Vue.prototype.$goTo = function(url) {
      window.location.assign(url);
    };

    Vue.prototype.$getTransformedValue = function(value) {
      let transformedValue = undefined;
      if (value instanceof Date) {
        transformedValue = moment(value).format('YYYY/MM/DD HH:mm:ss');
      } else if (value instanceof Array) {
        if (value.length) {
          let newValueArray = new Array();
          value.forEach(elem => {
            const tsf = this.$getTransformedValue(elem);
            if (tsf !== undefined) {
              newValueArray.push(tsf);
            }
          });
          if (newValueArray.length) {
            transformedValue = newValueArray;
          }
        }
      } else if (value instanceof Object) {
        if (Object.keys(value).length) {
          if (value.id) {
            transformedValue = value.id;
          } else {
            let newValueObject = new Object();
            for (let key in value) {
              const tsf = this.$getTransformedValue(value[key]);
              if (tsf !== undefined) {
                newValueObject[key] = tsf;
              }
            }
            if (Object.keys(newValueObject).length) {
              transformedValue = newValueObject;
            }
          }
        }
      } else if (![null, undefined, '', false].includes(value)) {
        transformedValue = value;
      }
      return transformedValue;
    };

    /**
     * Put formFields values into a formData object, in order to sent it to a PHP form.
     * @param {Object} formFields
     * @param {FormData} formData
     */
    Vue.prototype.$getFormFieldsData = function(formFields = this.formFields, formData = new FormData()) {
      let transformedFormFields = this.$getTransformedValue(formFields);
      _.forEach(_.keys(transformedFormFields), field => {
        if (field === 'csrfProtection') {
          formData.append(this.csrfProtection, transformedFormFields[field]);
        }
        if (transformedFormFields[field] instanceof Array) {
          transformedFormFields[field].forEach(elem => {
            formData.append(field + '[]', elem);
          });
        } else if (transformedFormFields[field] instanceof Object) {
          for (let key in transformedFormFields[field]) {
            formData.append(`${field}[${key}]`, transformedFormFields[field][key]);
          }
        } else {
          formData.append(field, transformedFormFields[field]);
        }
      });
      return formData;
    };

    /**
     * Called when the event "selectedItemsChange" is triggered from DualList component.
     * Set the formFields[fieldName] variable to the value given in parameter.
     * @param {Object} eventArguments Event arguments and other VueJS properties about the event. Must be passed so 'this' is defined.
     * @param {Array} eventArguments[0] Selected items.
     * @param {String} fieldName
     */
    Vue.prototype.$onSelectedItemsChange = function(eventArguments, fieldName) {
      this.formFields[fieldName] = _.cloneDeep(eventArguments[0]);
    };

    /**
     * Called whenever receiving the « after-create » event from the any new entity modal component.
     * Add the given entity to the formFields[fieldName] list
     * @param {Object} eventArguments Event arguments and other VueJS properties about the event. Must be passed so 'this' is defined.
     * @param {Object} eventArguments[0] The entity object
     * @param {String} fieldName The name of the entity
     * @param {Object} formFields The object containing fieldName entities  The name of the entity
     */
    Vue.prototype.$onAfterCreateEntity = function(eventArguments, fieldName, formFields) {
      const nextKey = Math.max(-1, ...Object.keys(formFields[fieldName])) + 1;
      Vue.set(formFields[fieldName], nextKey, eventArguments[0]);
    };

    /**
     * Called whenever receiving the « deleteEntity » event from any new card component.
     * Delete the given entity from formFields[fieldName] list.
     * @param {object} indexOfDeletedItem Index of the entity in formFields[fieldName]
     * @param {string} fieldName The name of the entity
     * @param {object} formFields
     */
    Vue.prototype.$onDeleteEntity = function(indexOfDeletedItem, fieldName, formFields) {
      let refEntity = _.cloneDeep(formFields[fieldName][indexOfDeletedItem].refEntity);
      if (refEntity) {
        refEntity.hasDeletedRevision = true;
        this.refsOfDeletedEntities[fieldName].push(refEntity);
      }
      formFields[fieldName] = _.pickBy(formFields[fieldName], (entity, index) => {
        return index != indexOfDeletedItem;
      });
    };

    /**
     * Hide/show the modal of confirmModal.
     * @param {string} id  The identifiant of the confirmModal
     */
    Vue.prototype.$toggleModal = function(id, testId) {
      $('#' + id).modal('toggle');
    };

    /**
     *  Delete an entity on confirm Modal. Need currentId to be defined in component datas
     * @param {string} url the delete url api to request
     */
    Vue.prototype.$deleteEntity = function(url) {
      return axios.delete(url + this.currentId)
        .then(response => {
          window.location.assign(response.headers.location);
        });
    };

    Vue.prototype.$requestAndSet = async function(queries) {
      let promises = [];

      queries.forEach(async query => {
        const promise = axios.get(query.url, { params: query.params }).then(res => {
          _.set(this, query.path, res.data);
        }).catch(e => {
          this.$handleError(e);
        });
        promises.push(promise);
      });

      return Promise.all(promises);
    };


  }
};

export default CnsFormUtils;
