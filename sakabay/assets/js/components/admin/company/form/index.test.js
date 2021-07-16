import { shallow, createLocalVue } from 'vue-test-utils';
import axios from 'axios';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import CompanyForm from './index';

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
      name: 'name test',
      city: {name: 'city name test'},
      address: {postalAddress: 'test'}
    }});
  })
}));

describe('Create or edit a company entity', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(CompanyForm, {
      propsData: { companyId: 0 },
      i18n,
      localVue,
    });

    wrapper.vm.$refs.autocomplete.setValue = jest.fn();

  });

  it('initializes', () => {
    expect(wrapper.vm).toBeDefined();
  });
});

describe('created test', () => {
  it('creates with id', async() => {
    axios.get
    .mockImplementationOnce(() => Promise.resolve({data: {}}))
    .mockImplementationOnce(() => Promise.resolve({data: {
      name: 'name test',
      city: {name: 'city name test'},
      address: {postalAddress: 'test'}
    }}));
    wrapper.vm.companyId = 10;
    await CompanyForm.created.bind(wrapper.vm)();
    expect(wrapper.vm.formFields.name).toEqual('name test');
    expect(wrapper.vm.loading).toEqual(false);
  });

  it('creates with id error case', async() => {
    wrapper.vm.companyId = 10;
    axios.get.mockImplementationOnce(() => {
      return Promise.reject(new Error('created() test error'));
    });
    const spyOnError = jest.fn();
    wrapper.vm.$handleError = spyOnError;
    await CompanyForm.created.bind(wrapper.vm)();
    expect(wrapper.vm.loading).toEqual(false);
    expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
  });
});

describe('methods test', () => {
  it('setCity test', () => {
    let city = {
      name: 'test name'
    };
    wrapper.vm.setCity(city);
    expect(wrapper.vm.formFields.city).toEqual(city);
    expect(wrapper.vm.$refs.autocomplete.setValue).toHaveBeenCalled();
  });

  it('goBack test', () => {
    wrapper.vm.urlPrecedente = '/sakabay';
    const spyOnGoBack = jest.fn();
    wrapper.vm.$goTo = spyOnGoBack;
    wrapper.vm.goBack();
    expect(spyOnGoBack).toHaveBeenCalledWith('/sakabay');
  });
});
