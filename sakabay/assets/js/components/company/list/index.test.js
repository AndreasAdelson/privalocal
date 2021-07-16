import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import CompanyList from './index.vue';
import axios from 'axios';


const localVue = createLocalVue();
localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);

let wrapper;

jest.mock('axios', () => ({
  get: jest.fn(() => {
    return Promise.resolve({data: {
    }});
  })
}));

describe('new entity card component', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(CompanyList, {
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

  describe('created test', () => {
    it('creates with id', async() => {
      axios.get
      .mockImplementationOnce(() => Promise.resolve({data:
        [
          {
            id: 1,
            dt_created: '01/01/2021 10:00:34',
            company_subscriptions: [{dt_fin: '01/01/2021 10:00:34'}],
            name: 'test name',
            num_siret: 123456789101112,
            url_name: 'test url name'
          },
          {
            id: 2,
            dt_created: '02/09/2021 10:00:34',
            company_subscriptions: [{dt_fin: '01/01/2221 10:00:34'}],
            name: 'test name2',
            num_siret: 123456789101112,
            url_name: 'test url name2'
          }
        ]
      }));
      wrapper.vm.utilisateurId = 10;
      wrapper.vm.loading = true;
      await CompanyList.created.bind(wrapper.vm)();
      expect(wrapper.vm.companies).toEqual(
        [
          {
            id: 2,
            dt_created: '02/09/2021 10:00:34',
            company_subscriptions: [{dt_fin: '01/01/2221 10:00:34', isActive: true}],
            name: 'test name2',
            num_siret: 123456789101112,
            url_name: 'test url name2',
          },
          {
            id: 1,
            dt_created: '01/01/2021 10:00:34',
            company_subscriptions: [{dt_fin: '01/01/2021 10:00:34', isActive: false}],
            name: 'test name',
            num_siret: 123456789101112,
            url_name: 'test url name',
          }
        ]
      );
      expect(wrapper.vm.loading).toEqual(false);
    });

    it('creates with id error case', async() => {
      axios.get.mockImplementationOnce(() => {
        return Promise.reject(new Error('created() test error'));
      });
      const spyOnError = jest.fn();
      wrapper.vm.utilisateurId = 10;
      wrapper.vm.$handleError = spyOnError;
      await CompanyList.created.bind(wrapper.vm)();
      expect(wrapper.vm.loading).toEqual(false);
      expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
    });
  });

  describe('methods test', () => {
    describe('getStatusClass test', () => {
      it('VAl case', () => {
        let codeStatut = 'VAL';
        expect(wrapper.vm.getStatutsClass(codeStatut)).toEqual('green-skb');
      });

      it('ENC case', () => {
        let codeStatut = 'ENC';
        expect(wrapper.vm.getStatutsClass(codeStatut)).toEqual('blue-skb');
      });

      it('REF case', () => {
        let codeStatut = 'REF';
        expect(wrapper.vm.getStatutsClass(codeStatut)).toEqual('red-skb');
      });
    });
  });
  describe('getStatutLabel test', () => {
    it('VAL case', () => {
      let codeStatut = 'VAL';
      expect(wrapper.vm.getStatutsLabel(codeStatut)).toEqual([wrapper.vm.$t('company_statut.state.validate'), 'check']);
    });

    it('ENC case', () => {
      let codeStatut = 'ENC';
      expect(wrapper.vm.getStatutsLabel(codeStatut)).toEqual([wrapper.vm.$t('company_statut.state.pending'), 'history']);
    });

    it('REF case', () => {
      let codeStatut = 'REF';
      expect(wrapper.vm.getStatutsLabel(codeStatut)).toEqual([wrapper.vm.$t('company_statut.state.refused'), 'times']);
    });
  });
  describe('getSubscriptionDateLabel test', () => {
    it('with active subscriptions', () => {
      let companySubscriptions = [
        {isActive: true, dt_fin: '02/09/2221 10:00:34'},
        {isActive: false, dt_fin: '02/09/2021 10:00:34'}
      ];
      expect(wrapper.vm.getSubscriptionDateLabel(companySubscriptions)).toEqual('le 02/09/2221, à 10:00');
    });
    it('with no active subscriptions', () => {
      let companySubscriptions = [
        {isActive: false, dt_fin: '02/09/2221 10:00:34'},
        {isActive: false, dt_fin: '02/09/2021 10:00:34'}
      ];
      expect(wrapper.vm.getSubscriptionDateLabel(companySubscriptions)).toEqual(wrapper.vm.$t('company.presentation.no_subscriptions'));
    });
  });
});
