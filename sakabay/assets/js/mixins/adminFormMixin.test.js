import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import adminFormMixin from 'mixins/adminFormMixin';
import axios from 'axios';

const loggerOptions = {
  isEnabled: true,
  logLevel : 'debug',
  stringifyArguments : false,
  showLogLevel : true,
  showMethodName : true,
  separator: '|',
  showConsoleColors: true
};
const localVue = createLocalVue();

localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);
localVue.use(VueLogger, loggerOptions);

jest.mock('axios', () => ({
  post: jest.fn(() => {
    return Promise.resolve();
  }),
  delete: jest.fn(() => {
    return Promise.resolve();
  }),
}));

const i18n = new VueI18n({
  locale: 'fr',
  fallbackLocale: 'fr',
  messages: {
    'en': require('i18n/en.json'),
    'fr': require('i18n/fr.json')
  }
});

let wrapper;
beforeEach(() => {
  wrapper = shallow(adminFormMixin, {
    i18n, localVue
  });
});

describe('adminFormMixin', () => {
  describe('watch function', () => {

    describe('formFields', () => {
      it('with form errors', async() => {
        const spy = jest.spyOn(wrapper.vm, '$removeFieldErrors');
        wrapper.vm.formErrors = {
          test: ['a'],
        };
        wrapper.vm.formFields = {
          test: '',
        };
        await wrapper.vm.$nextTick();
        expect(spy).not.toHaveBeenCalledWith('test');
        wrapper.vm.formFields.test = 'a';
        await wrapper.vm.$nextTick();
        expect(spy).toHaveBeenCalledWith('test');
      });

      it('without form errors', async() => {
        const spy = jest.spyOn(wrapper.vm, '$removeFieldErrors');
        spy.mockClear();
        wrapper.vm.formErrors = { test: [] };
        wrapper.vm.formFields.test = '';
        await wrapper.vm.$nextTick();
        expect(spy).not.toHaveBeenCalledWith('test');
        wrapper.vm.formFields.test = 'a';
        await wrapper.vm.$nextTick();
        expect(spy).not.toHaveBeenCalledWith('test');
      });
    });

  });

  describe('methods', () => {

    describe('submitForm()', () => {
      it('during/before the request', () => {
        axios.post.mockImplementationOnce(() => {
          return Promise.resolve({headers: {}});
        });
        wrapper.vm.API_URL = 'ma petite api';
        const spyOnAxios = jest.spyOn(axios, 'post');
        wrapper.vm.submitForm();
        expect(spyOnAxios).toHaveBeenCalledWith('ma petite api', wrapper.vm.$getFormFieldsData(wrapper.vm.formFields));
      });

      it('success case', async() => {
        axios.post.mockImplementationOnce(() => {
          return Promise.resolve({ headers: { location: 'ma nouvelle maison' } });
        });
        await wrapper.vm.submitForm();
        expect(window.location.assign).toHaveBeenCalledWith('ma nouvelle maison');
      });

      it('errorcase 1 : 500', async() => {
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        axios.post.mockImplementationOnce(() => {
          return Promise.reject(new Error('oups'));
        });
        await wrapper.vm.submitForm();
        expect(spyOnError).toHaveBeenCalledWith(new Error('oups'));
      });

      it('errorcase 2 : 400', async() => {
        const spyOnErrors = jest.fn();
        wrapper.vm.$handleFormError = spyOnErrors;
        const error = new Error('Error Test');
        axios.post.mockImplementationOnce(() => {
          const fields = Object.keys(wrapper.vm.formFields);
          error.response = {
            status: 400,
            data: { errors: { children: {} } },
          };
          error.response.data.errors.children[fields[1]] = {
            errors: ['Test form error']
          };
          for (let i = 2; i < fields.length; i++) {
            error.response.data.errors.children[fields[i]] = {};
          }
          error.response.headers = {};
          return Promise.reject(error);
        });
        await wrapper.vm.submitForm();
        expect(spyOnErrors).toHaveBeenCalledWith(error.response.data);
      });

      it('errorcase 3 : 400', async() => {
        const error = new Error('Error Test');
        axios.post.mockImplementationOnce(() => {
          const fields = Object.keys(wrapper.vm.formFields);
          error.response = {
            status: 400,
            data: { errors: { children: {} } },
          };
          error.response.data.errors.children[fields[1]] = {
            errors: ['Test form error']
          };
          for (let i = 2; i < fields.length; i++) {
            error.response.data.errors.children[fields[i]] = {};
          }
          error.response.headers = {'x-message': 'test error'};
          return Promise.reject(error);
        });
        await wrapper.vm.submitForm();
        expect(wrapper.vm.errorMessage).toEqual(decodeURIComponent('test error'));
      });
    });

    describe('deleteEntity()', () => {
      it('before/during the request', () => {
        axios.delete.mockImplementationOnce(() => {
          return Promise.resolve({headers: {}});
        });
        wrapper.vm.API_URL = 'ma petite api';
        const spy = jest.spyOn(axios, 'delete');
        wrapper.vm.deleteEntity();
        expect(wrapper.vm.loading).toEqual(true);
        expect(spy).toHaveBeenCalledWith('ma petite api');
      });

      it('succes case', async() => {
        axios.delete.mockImplementationOnce(() => {
          return Promise.resolve({headers: {location: 'changement d url'}});
        });
        wrapper.vm.axeId = 1;
        await wrapper.vm.deleteEntity();
        expect(window.location.assign).toHaveBeenCalledWith('changement d url');
      });

      it('error case', async() => {
        axios.delete.mockImplementationOnce(() => {
          return Promise.reject(new Error('my bad'));
        });
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        await wrapper.vm.deleteEntity();
        expect(wrapper.vm.loading).toEqual(false);
        expect(spyOnError).toHaveBeenCalledWith(new Error('my bad'));
      });
    });

  });
});
