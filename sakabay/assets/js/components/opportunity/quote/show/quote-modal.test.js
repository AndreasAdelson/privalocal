import { shallow, createLocalVue } from 'vue-test-utils';
import axios from 'axios';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import QuoteModal from './quote-modal.vue';

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

let wrapper;

jest.mock('axios', () => ({
  get: jest.fn(() => {
    return Promise.resolve({data: {
      answers: [{quote: true}],
      besoin_statut: {code: 'TEST'}
    }});
  }),
  post: jest.fn(() => {
    return Promise.resolve({data: {}, headers: {location: '/sakabay'}});
  })
}));

describe('Answer to an opportunity modale', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(QuoteModal, {
      i18n,
      localVue,
      propsData: {
        opportunity: {
          title: 'test title',
          answers: [{quote: true, id: 10}]
        },
        company: {
          name: 'test company name',
          url_name: 'urlname test'
        }
      }
    });
  });

  it('initializes', () => {
    expect(wrapper.vm).toBeDefined();
  });

  describe('watcher on', () => {
    it('opportunity', async() => {
      wrapper.vm.opportunity = {
        title: 'test title',
        answers: [{quote: true, id: 10}]
      };
      wrapper.vm.company = {
        name: 'test company name',
        url_name: 'urlname test'
      };
      await wrapper.vm.$nextTick();
      expect(wrapper.vm.formFields.messageEmail).toEqual(wrapper.vm.$t('opportunity.customer.default_message', [
        'test title',
        'test company name'
      ]));
    });

    it('company', async() => {
      wrapper.vm.opportunity = {
        title: 'test title',
        answers: [{quote: true, id: 10}]
      };
      wrapper.vm.company = {
        name: 'test company name',
        url_name: 'urlname test'
      };
      await wrapper.vm.$nextTick();
      expect(wrapper.vm.formFields.messageEmail).toEqual(wrapper.vm.$t('opportunity.customer.default_message', [
        'test title',
        'test company name'
      ]));
    });
  });

  describe('methods test', () => {
    describe('resetForm test', () => {
      it('beofre 150 ms', () => {
        const spyOnTimeout = jest.spyOn(global, 'setTimeout');
        const spyOnEmit = jest.fn();
        wrapper.vm.$emit = spyOnEmit;
        wrapper.vm.resetForm();
        expect(spyOnTimeout).toHaveBeenCalled();
        expect(spyOnEmit).not.toHaveBeenCalledWith('cancel-form');
      });

      it('after 150 ms', () => {
        const spyOnEmit = jest.fn();
        wrapper.vm.$emit = spyOnEmit;
        wrapper.vm.loading = true;
        wrapper.vm.formFields = {
          message: 'message test',
          besoin: {title: 'test title'},
          company: {name: 'test name'},
          _token: 'azefadas'
        };
        jest.useFakeTimers();
        wrapper.vm.resetForm();
        jest.runAllTimers();
        expect(spyOnEmit).toHaveBeenCalledWith('cancel-form');
      });
    });

    it('onCancelButton', () => {
      const spyOnResetForm = jest.spyOn(wrapper.vm, 'resetForm');
      wrapper.vm.onCancelButton();
      expect(spyOnResetForm).toHaveBeenCalled();
    });

    describe('submitForm test', () => {
      it('during/before the request to API with first if true', () => {
        const spyOnPost = jest.spyOn(axios, 'post');
        wrapper.vm.utilisateurId = 1;
        wrapper.vm.token = 'tokentest';
        // wrapper.vm.imageProfilSelected = true;
        wrapper.vm.submitForm();
        expect(spyOnPost).toHaveBeenCalledWith('/api/answers/quote/10/send/1', wrapper.vm.$getFormFieldsData(wrapper.vm.formFields));
        // expect(window.location.assign).toHaveBeenCalledWith('/sakabay');
      });
      describe('error case', () => {
        it('server error (500) : logs the error’s message', async() => {
          axios.post.mockImplementationOnce(() => {
            return Promise.reject(new Error('erreur 500 test'));
          });
          const spy = jest.fn();
          wrapper.vm.$handleError = spy;
          await wrapper.vm.submitForm();
          expect(wrapper.vm.loading).toEqual(false);
          expect(spy).toHaveBeenCalledWith(new Error('erreur 500 test'));
        });
        it('constraint validation violation error (400) with x-message', async() => {
          axios.post.mockImplementationOnce(() => Promise.reject({
            response: {
              status: 400,
              headers: {
                'x-message': 'error message in headers'
              },
              data: { errors: { children: 'test error' } }
            }
          }));
          await wrapper.vm.submitForm();
          expect(wrapper.vm.loading).toEqual(false);
          expect(wrapper.vm.errorMessage).toEqual('error message in headers' );
        });

        it('constraint validation violation error (400) without x-message', async() => {
          const spyOnHandleFormError = jest.spyOn(wrapper.vm, '$handleFormError');
          axios.post.mockImplementationOnce(() => Promise.reject({
            response: {
              status: 400,
              headers: {
              },
              data: { errors: { children: 'test error' } }
            }
          }));
          await wrapper.vm.submitForm();
          expect(wrapper.vm.loading).toEqual(false);
          expect(spyOnHandleFormError).toHaveBeenCalled();
        });
      });
    });
  });
});

