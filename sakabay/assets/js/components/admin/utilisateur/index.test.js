import { shallow, createLocalVue } from 'vue-test-utils';
import axios from 'axios';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import UtilisateurIndex from './index';

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
      headers: {'x-total-count': 4}
    });
  })
}));

describe('Utilisateur entity index', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(UtilisateurIndex, {
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
    wrapper.vm.filterFields = 'firstName,lastName,email,username';
    wrapper.vm.currentFilter = 'testfilter';
    wrapper.vm.table.sortBy = 'still a test';
    wrapper.vm.pager.currentPage = 1;
    wrapper.vm.pager.perPage = 1;
    const spyOnGet = jest.spyOn(axios, 'get');
    const expectedData = {
      params: {currentPage: 1, filter: 'testfilter', filterFields: 'firstName,lastName,email,username', perPage: 1, sortBy: 'still a test', sortDesc: false}
    };
    wrapper.vm.refreshData();
    expect(spyOnGet).toHaveBeenCalledWith('/api/admin/utilisateurs',expectedData);
  });

  it('refreshData request success', async() => {
    axios.get.mockImplementationOnce(() => {
      return Promise.resolve({
        data: [{
          id: 1,
          username: 'username test',
          last_name: 'last name test',
          first_name: 'first name test',
          email: 'email test'
        }],
        headers: { 'x-total-count': 4 }
      });
    });
    expect(await wrapper.vm.refreshData()).toEqual([{
      id: 1,
      username: 'username test',
      last_name: 'last name test',
      first_name: 'first name test',
      email: 'email test',
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
});
