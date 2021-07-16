import { shallow, createLocalVue } from 'vue-test-utils';
import axios from 'axios';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import ValidatedCompanyIndex from './index';

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
    return Promise.resolve({
      data: {},
      headers: {'x-total-count': 4}
    });
  })
}));

describe('ValidatedCompany entity index', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(ValidatedCompanyIndex, {
      i18n,
      localVue,
    });
  });

  it('initializes', () => {
    expect(wrapper.vm).toBeDefined();
  });
});


describe('methods test', () => {
  it('refreshData() test', () => {
    wrapper.vm.filterFields = 'name';
    wrapper.vm.currentFilter = 'testfilter';
    wrapper.vm.table.sortBy = 'still a test';
    wrapper.vm.pager.currentPage = 1;
    wrapper.vm.pager.perPage = 1;
    const spyOnGet = jest.spyOn(axios, 'get');
    const expectedData = {
      params: {
        codeStatut: 'VAL',
        currentPage: 1,
        filter: 'testfilter',
        filterFields: 'name',
        perPage: 1, sortBy: 'still a test',
        sortDesc: false
      }
    };
    wrapper.vm.refreshData();
    expect(spyOnGet).toHaveBeenCalledWith('/api/admin/companies',expectedData);
  });

  it('refreshData request success', async() => {
    axios.get.mockImplementationOnce(() => {
      return Promise.resolve({
        data: [{
          id: 1,
          name: 'test1',
          num_siret: 123456789101112,
          url_name: 'url name',
          utilisateur: {username: 'username'},
          category: {name: 'category name'},
          company_subscriptions: [{subscription_status: {code: 'TER'}}],
        }],
        headers: { 'x-total-count': 4 }
      });
    });
    expect(await wrapper.vm.refreshData()).toEqual([{
      id: 1,
      name: 'test1',
      num_siret: 123456789101112,
      url_name: 'url name',
      utilisateur: 'username',
      category: 'category name',
      subscription: 'Terminé',
      company_subscriptions: [{subscription_status: {code: 'TER'}}],
      actions: 1
    }]);
    expect(wrapper.vm.pager.totalRows).toEqual(4);
  });

  it('refreshData request error', async() => {
    axios.get.mockImplementationOnce(() => {
      return Promise.reject(new Error('refreshData() test error'));
    });
    const spyOnError = jest.fn();
    wrapper.vm.$handleError = spyOnError;
    await wrapper.vm.refreshData();
    expect(spyOnError).toHaveBeenCalledWith(new Error('refreshData() test error'));
  });

  describe('isSubscribed test', () => {
    it('VAL case with active subscription', () => {
      let companySubscription = [
        {dt_debut: '01/01/2021 17:00', dt_fin: '01/01/2221 17:00', subscription_status: {code: 'VAL'}}
      ];
      expect(wrapper.vm.isSubscribed(companySubscription)).toEqual(wrapper.vm.$t('dashboard.history.table.in_progress'));
    });

    it('VAL case with incoming subscription', () => {
      let companySubscription = [
        {dt_debut: '01/01/2121 17:00', dt_fin: '01/01/2221 17:00', subscription_status: {code: 'VAL'}}
      ];
      expect(wrapper.vm.isSubscribed(companySubscription)).toEqual(wrapper.vm.$t('dashboard.history.table.coming_soon'));
    });

    it('TER case', () => {
      let companySubscription = [
        {subscription_status: {code: 'TER'}}
      ];
      expect(wrapper.vm.isSubscribed(companySubscription)).toEqual(wrapper.vm.$t('dashboard.history.table.ended'));
    });

    it('ENC case', () => {
      let companySubscription = [
        {subscription_status: {code: 'ENC'}}
      ];
      expect(wrapper.vm.isSubscribed(companySubscription)).toEqual(wrapper.vm.$t('dashboard.history.table.in_progress'));
    });

    it('ANN case with active subscription', () => {
      let companySubscription = [
        {dt_debut: '01/01/2021 17:00', dt_fin: '01/01/2221 17:00', subscription_status: {code: 'ANN'}}
      ];
      expect(wrapper.vm.isSubscribed(companySubscription)).toEqual(wrapper.vm.$t('dashboard.history.table.canceled_renewal'));
    });

    it('ANN case with only dtFin after to actual date', () => {
      let companySubscription = [
        {dt_debut: '01/01/2121 17:00', dt_fin: '01/01/2221 17:00', subscription_status: {code: 'ANN'}}
      ];
      expect(wrapper.vm.isSubscribed(companySubscription)).toEqual(wrapper.vm.$t('dashboard.history.table.canceled'));
    });

    it('ANN case with ended subscription', () => {
      let companySubscription = [
        {dt_debut: '01/01/2019 17:00', dt_fin: '01/01/2020 17:00', subscription_status: {code: 'ANN'}}
      ];
      expect(wrapper.vm.isSubscribed(companySubscription)).toEqual(wrapper.vm.$t('dashboard.history.table.ended'));
    });

    it('OFF case with active subscription', () => {
      let companySubscription = [
        {dt_debut: '01/01/2021 17:00', dt_fin: '01/01/2221 17:00', subscription_status: {code: 'OFF'}}
      ];
      expect(wrapper.vm.isSubscribed(companySubscription)).toEqual(wrapper.vm.$t('dashboard.history.table.offer'));
    });

    it('OFF case with ended subscription', () => {
      let companySubscription = [
        {dt_debut: '01/01/2019 17:00', dt_fin: '01/01/2020 17:00', subscription_status: {code: 'OFF'}}
      ];
      expect(wrapper.vm.isSubscribed(companySubscription)).toEqual(wrapper.vm.$t('dashboard.history.table.ended'));
    });

    it('default switch case', () => {
      let companySubscription = [
        {dt_debut: '01/01/2019 17:00', dt_fin: '01/01/2020 17:00', subscription_status: {code: 'ALALAL'}}
      ];
      expect(wrapper.vm.isSubscribed(companySubscription)).toEqual(wrapper.vm.$t('dashboard.history.table.none'));
    });

    it('witgh empty companySubscription array', () => {
      let companySubscription = [
      ];
      expect(wrapper.vm.isSubscribed(companySubscription)).toEqual(wrapper.vm.$t('dashboard.history.table.none'));
    });
  });

});
