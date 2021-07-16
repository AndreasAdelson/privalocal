import { shallow, createLocalVue } from 'vue-test-utils';
import axios from 'axios';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import GroupForm from './index';

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
    wrapper = shallow(GroupForm, {
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
    .mockImplementationOnce(() => Promise.resolve({data: [
      {name: 'role name test'}
    ]}))
    .mockImplementationOnce(() => Promise.resolve({data: {
      name: 'name test',
      code: 'TEST',
      roles: [{name: 'test name role'}]
    }}));
    wrapper.vm.groupId = 10;
    wrapper.vm.loading = true;
    await GroupForm.created.bind(wrapper.vm)();
    expect(wrapper.vm.formFields.name).toEqual('name test');
    expect(wrapper.vm.loading).toEqual(false);
    expect(wrapper.vm.roles).toEqual([{name: 'role name test'}]);
    expect(wrapper.vm.rolesAtCreation).toEqual([{name: 'test name role'}]);
  });

  it('creates with id error case', async() => {
    axios.get.mockImplementationOnce(() => {
      return Promise.reject(new Error('created() test error'));
    });
    const spyOnError = jest.fn();
    wrapper.vm.$handleError = spyOnError;
    await GroupForm.created.bind(wrapper.vm)();
    expect(wrapper.vm.loading).toEqual(false);
    expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
  });
});

describe('methods test', () => {
  it('setUser test', () => {
    wrapper.vm.formFields.utilisateurs = {0:{ a: 1 }};
    wrapper.vm.setUser({ b: 2 });
    expect(wrapper.vm.formFields.utilisateurs).toEqual({0:{ a: 1 }, 1:{ b: 2 }});
  });

  describe('onUsersAjaxLoaded(data)', () => {
    it('with data length = 0', () => {
      wrapper.vm.loading = true;
      wrapper.vm.formErrors.users = ['a'];
      wrapper.vm.onUsersAjaxLoaded('');
      wrapper.vm.formErrors.users = [];
      expect(wrapper.vm.loading).toEqual(false);
    });
    it('with data length > 0', () => {
      wrapper.vm.formErrors.users = ['a'];
      wrapper.vm.loading = true;
      wrapper.vm.onUsersAjaxLoaded('test');
      wrapper.vm.formErrors.users = [wrapper.vm.$t('group.error.utilisateur_does_not_exist')];
      expect(wrapper.vm.loading).toEqual(false);
    });
  });
});

