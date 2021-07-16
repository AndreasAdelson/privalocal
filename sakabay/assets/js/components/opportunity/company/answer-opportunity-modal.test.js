import { shallow, createLocalVue } from 'vue-test-utils';
import axios from 'axios';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import AnswerOpportunityModal from './answer-opportunity-modal.vue';

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
    }});
  }),
  post: jest.fn(() => {
    return Promise.resolve({data: {
    }});
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
    wrapper = shallow(AnswerOpportunityModal, {
      propsData: { companyId: 0 },
      i18n,
      localVue,
    });
  });

  it('initializes', () => {
    expect(wrapper.vm).toBeDefined();
  });

  describe('watcher on', () => {
    it('opportunity', async() => {
      wrapper.vm.opportunity = {
        title: 'test title',
      };
      wrapper.vm.company = {
        name: 'test company name',
      };
      await wrapper.vm.$nextTick();
      expect(wrapper.vm.formFields.message).toEqual(wrapper.vm.$t('opportunity.customer.default_message', [
        'test title',
        'test company name'
      ]));
    });

    it('company', async() => {
      wrapper.vm.opportunity = {
        title: 'test title',
      };
      wrapper.vm.company = {
        name: 'test company name',
      };
      await wrapper.vm.$nextTick();
      expect(wrapper.vm.formFields.message).toEqual(wrapper.vm.$t('opportunity.customer.default_message', [
        'test title',
        'test company name'
      ]));
    });
  });

  describe('methods test', () => {
    describe('resetForm test', () => {
      it('beofre 150 ms', () => {
        const spyOnTimeout = jest.spyOn(global, 'setTimeout');
        const spyOnRemoveErrors = jest.fn();
        wrapper.vm.$removeErrors = spyOnRemoveErrors;
        const spyOnEmit = jest.fn();
        wrapper.vm.$emit = spyOnEmit;
        wrapper.vm.resetForm();
        expect(spyOnTimeout).toHaveBeenCalled();
        expect(spyOnRemoveErrors).not.toHaveBeenCalled();
        expect(spyOnEmit).not.toHaveBeenCalledWith('cancel-form');
      });

      it('after 150 ms', () => {
        const spyOnRemoveFormErrors = jest.fn();
        wrapper.vm.$removeFormErrors = spyOnRemoveFormErrors;
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
        expect(wrapper.vm.formFields).toEqual(wrapper.vm.emptyFormFields);
        expect(spyOnRemoveFormErrors).toHaveBeenCalled();
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
        wrapper.vm.formFields = {
          message: 'message test',
          besoin: {title: 'test title'},
          company: {name: 'test name'},
          _token: 'azefadas'
        };
        wrapper.vm.submitForm();
        expect(spyOnPost).toHaveBeenCalledWith('/api/answer', wrapper.vm.$getFormFieldsData(wrapper.vm.formFields));
      });
      describe('error case', () => {
        it('server error (500) : logs the error’s message', async() => {
          axios.post.mockImplementationOnce(() => {
            return Promise.reject(new Error('erreur 500 test'));
          });
          const spy = jest.fn();
          wrapper.vm.$handleError = spy;
          await wrapper.vm.submitForm();
          expect(spy).toHaveBeenCalledWith(new Error('erreur 500 test'));
        });
        it('constraint validation violation error (400)', async() => {
          wrapper.vm.$handleFormErrors = jest.fn();
          const spyOnHandleFormErrors = jest.spyOn(wrapper.vm, '$handleFormError');
          axios.post.mockImplementationOnce(() => Promise.reject({
            response: {
              status: 400,
              data: { errors: { children: 'test error' } }
            }
          }));
          await wrapper.vm.submitForm();
          expect(wrapper.vm.loading).toEqual(false);
          expect(spyOnHandleFormErrors).toHaveBeenCalled( );
        });
      });
    });
  });
});

