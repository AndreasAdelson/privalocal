import { shallow, createLocalVue } from 'vue-test-utils';
import axios from 'axios';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import CompanySubscriptionForm from './index';
import moment from 'moment';
import { iteratee } from 'lodash';

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
    wrapper = shallow(CompanySubscriptionForm, {
      propsData: { companyId: 0 },
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
    .mockImplementationOnce(() => Promise.resolve({data: {name: 'test', code: 'TEST'}}))
    .mockImplementationOnce(() => Promise.resolve({data: {dt_debut: '02/09/2021 10:00', dt_fin: '02/10/2021 10:00'}}));
    let expectedData = {
      dtDebut: '02/09/2021 10:00',
      dtFin: '02/10/2021 10:00',
      subscriptionStatus: null,
      stripeId: null,
      _token: null
    };
    const spyOnSetEditForm = jest.spyOn(wrapper.vm,'$setEditForm');
    wrapper.vm.companySubscriptionId = 10;
    wrapper.vm.loading = true;
    await CompanySubscriptionForm.created.bind(wrapper.vm)();
    expect(wrapper.vm.subscriptionStatus.name).toEqual('test');
    expect(wrapper.vm.loading).toEqual(false);
    expect(spyOnSetEditForm).toHaveBeenCalled();
    expect(wrapper.vm.formFields.dtDebut).toEqual(moment('02/09/2021 10:00','DD/MM/YYYY H:i:s').toDate());
  });

  it('creates with id error case', async() => {
    wrapper.vm.companySubscriptionId = 10;
    axios.get.mockImplementationOnce(() => {
      return Promise.reject(new Error('created() test error'));
    });
    const spyOnError = jest.fn();
    wrapper.vm.$handleError = spyOnError;
    await CompanySubscriptionForm.created.bind(wrapper.vm)();
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
  it('customFormatter test', () => {
    expect(wrapper.vm.customFormatter('2021-09-02 10:00:00')).toEqual('02/09/2021, 10:00');
  });
  it('dateFormat test', () => {
    wrapper.vm.formFields.dtFin = null;
    wrapper.vm.dateFormat();
    expect(wrapper.vm.formFields.dtFin).not.toBe(null);
  });
});
