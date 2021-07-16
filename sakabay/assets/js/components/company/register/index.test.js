import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import CompanyRegisterIndex from './index.vue';
import axios from 'axios';

const localVue = createLocalVue();
localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);

let wrapper;

jest.mock('axios', () => ({
  get: jest.fn(() => {
    return Promise.resolve({data: [{
      id:1,
      dt_debut: '02/09/2021',
      dt_fin: '02/09/2021'
  }]});
  })
}));

describe('company register page', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(CompanyRegisterIndex, {
      i18n,
      localVue,
    });
  });

  /**
   * Test the initialization of the Vue component.
   */
  it('initializes', () => {
    expect(wrapper.vm).toBeDefined();
  });

  describe('creates with companyUrlName', () => {
    it('success case', async() => {
      axios.get
        .mockImplementationOnce(() => Promise.resolve({data: [{
          id:1,
          dt_debut: '02/09/2021',
          dt_fin: '02/09/2021'
      }]}));
      wrapper.vm.loading = true;
      await CompanyRegisterIndex.created.bind(wrapper.vm)();
      expect(wrapper.vm.subscriptions).toEqual([{
        id:1,
        dt_debut: '02/09/2021',
        dt_fin: '02/09/2021'
      }]);
    });

    it('error case', async() => {
      axios.get.mockImplementationOnce(() => {
        return Promise.reject(new Error('created() test error'));
      });
      const spyOnError = jest.fn();
      wrapper.vm.$handleError = spyOnError;
      await CompanyRegisterIndex.created.bind(wrapper.vm)();
      expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
    });

  });

  describe('methods test', () => {
    describe('getImage', () => {
      it('with FRE code', () => {
        let subscription = {code: 'FRE'};
        expect(wrapper.vm.getImage(subscription)).toEqual('build/gratuite.jpg');
      });

      it('with PRE code', () => {
        let subscription = {code: 'PRE'};
        expect(wrapper.vm.getImage(subscription)).toEqual('build/premium.jpg');
      });
    });
  });
});
