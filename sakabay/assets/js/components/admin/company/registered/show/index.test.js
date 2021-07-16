import { shallow, createLocalVue } from 'vue-test-utils';
import axios from 'axios';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import RegisteredCompanyShow from './index';

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
    return Promise.resolve({company_statut: {code: 'VAL'}});
  }),
  post: jest.fn(() =>{
    return Promise.resolve({});
  })
}));

describe('Show a refused company entity', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(RegisteredCompanyShow, {
      i18n,
      localVue,
    });
  });

  it('initializes', () => {
    expect(wrapper.vm).toBeDefined();
  });
});

describe('created test', () => {
  it('creates with id', async() => {
    axios.get
    .mockImplementationOnce(() => Promise.resolve({data: {name: 'name test', company_statut: {code: 'VAL'}}}));
    wrapper.vm.companyId = 10;
    wrapper.vm.loading = true;
    let expectedData = {
      name: 'name test',
      company_statut: {code: 'VAL'}
    };
    await RegisteredCompanyShow.created.bind(wrapper.vm)();
    expect(wrapper.vm.company).toEqual(expectedData);
    expect(wrapper.vm.loading).toEqual(false);
  });

  it('creates with id error case', async() => {
    wrapper.vm.companyId = 10;
    axios.get.mockImplementationOnce(() => {
      return Promise.reject(new Error('created() test error'));
    });
    const spyOnError = jest.fn();
    wrapper.vm.$handleError = spyOnError;
    await RegisteredCompanyShow.created.bind(wrapper.vm)();
    expect(wrapper.vm.loading).toEqual(false);
    expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
  });
});

describe('methods test', () => {
  it('goBack test', () => {
    wrapper.vm.urlPrecedente = '/sakabay';
    const spyOnGoBack = jest.fn();
    wrapper.vm.$goTo = spyOnGoBack;
    wrapper.vm.goBack();
    expect(spyOnGoBack).toHaveBeenCalledWith('/sakabay');
  });

  describe('validateCompany test', () => {
    it('success case', async() => {
      axios.post.mockImplementationOnce(() => {
        return Promise.resolve({
          headers: {
            location: 'location from headers'
          }
        });
      });
      await wrapper.vm.validateCompany();
      expect(wrapper.vm.loading).toEqual(false);
      expect(window.location.assign).toHaveBeenCalledWith('location from headers');
    });

    describe('error cases', () => {
      it('error with x-message error', async() => {
        axios.post.mockImplementationOnce(() => {
          return Promise.reject({
            response: {
              status: 400,
              headers: {
                'x-message': 'error message in headers'
              },
              data: {
              }
            }
          });
        });
        await wrapper.vm.validateCompany();
        expect(wrapper.vm.loading).toBe(false);
        expect(wrapper.vm.errorMessage).toEqual('error message in headers');
      });

      it('form validation error', async() => {
        wrapper.vm.$handleFormError = jest.fn();
        const spyOnFormError = wrapper.vm.$handleFormError;
        axios.post.mockImplementationOnce(() => {
          return Promise.reject({
            response: {
              status: 400,
              data: {
              }
            }
          });
        });
        await wrapper.vm.validateCompany();
        expect(wrapper.vm.loading).toBe(false);
        expect(spyOnFormError).toHaveBeenCalled();
      });

      it('any other cases', async() => {
        wrapper.vm.$handleError = jest.fn();
        const spyOnError = wrapper.vm.$handleError;
        axios.post.mockImplementationOnce(() => {
          return Promise.reject(new Error('test'));
        });
        await wrapper.vm.validateCompany();
        expect(wrapper.vm.loading).toBe(false);
        expect(spyOnError).toHaveBeenCalledWith(new Error('test'));
      });
    });
  });

  describe('declineCompany test', () => {
    it('success case', async() => {
      axios.post.mockImplementationOnce(() => {
        return Promise.resolve({
          headers: {
            location: 'location from headers'
          }
        });
      });
      await wrapper.vm.declineCompany();
      expect(wrapper.vm.loading).toEqual(false);
      expect(window.location.assign).toHaveBeenCalledWith('location from headers');
    });

    it('error case', async() => {
      wrapper.vm.$handleError = jest.fn();
      const spyOnError = wrapper.vm.$handleError;
      axios.post.mockImplementationOnce(() => {
        return Promise.reject(new Error('test'));
      });
      await wrapper.vm.declineCompany();
      expect(wrapper.vm.loading).toBe(false);
      expect(spyOnError).toHaveBeenCalledWith(new Error('test'));
    });
  });
});
