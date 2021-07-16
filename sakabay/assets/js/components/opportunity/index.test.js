import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import Opportunity from './index.vue';
import axios from 'axios';

const localVue = createLocalVue();
localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);

let wrapper;

jest.mock('axios', () => ({
  get: jest.fn(() => {
    return Promise.resolve({
      data: {},
      });
  })
}));
document.body.innerHTML = '<div id="headerMain"/>';
window.location = {
  search: '?page=quote'
};
describe('opportunity index component', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(Opportunity, {
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
    it('creates with one company', async() => {
      const spyOnSetPageByUrlParameter = jest.spyOn(wrapper.vm, 'setPageByUrlParameter');
      window.location = {
        search: '?page=quote'
      };
      axios.get
      .mockImplementationOnce(() => Promise.resolve({
        data:
        [
          {
            id: 1,
            url_name: 'url name test',
            image_profil: 'image profil test',
            name: 'test name',
            note: 4.235,
            address: {
              postal_address: 'postal address test',
              postal_code: 12345
            },
            city: {name: 'city name test'},
            category: {name: 'category name test'},
            sous_categorys: [{name: 'Beauté', code: 'BEAU'}],
            description_clean: 'description clean test',
            company_subscriptions: [
              {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '03/09/2221 10:00:34'}
            ]
          }
        ]
      }));
      wrapper.vm.loading = true;
      await Opportunity.created.bind(wrapper.vm)();
      expect(wrapper.vm.companies).toEqual([
        {
          id: 1,
          url_name: 'url name test',
          image_profil: 'image profil test',
          name: 'test name',
          note: 4.235,
          address: {
            postal_address: 'postal address test',
            postal_code: 12345
          },
          city: {name: 'city name test'},
          category: {name: 'category name test'},
          sous_categorys: [{name: 'Beauté', code: 'BEAU'}],
          description_clean: 'description clean test',
          company_subscriptions: [
            {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '03/09/2221 10:00:34'}
          ]
        }
      ]);
      expect(wrapper.vm.loading).toEqual(false);
      expect(spyOnSetPageByUrlParameter).toHaveBeenCalled();
    });

    it('creates with two company', async() => {
      const spyOnSetPageByUrlParameter = jest.spyOn(wrapper.vm, 'setPageByUrlParameter');
      window.location = {
        search: '?page=quote'
      };
      axios.get
      .mockImplementationOnce(() => Promise.resolve({
        data:
        [
          {
            id: 1,
            url_name: 'url name test',
            image_profil: 'image profil test',
            name: 'test name',
            note: 4.235,
            address: {
              postal_address: 'postal address test',
              postal_code: 12345
            },
            city: {name: 'city name test'},
            category: {name: 'category name test'},
            sous_categorys: [{name: 'Beauté', code: 'BEAU'}],
            description_clean: 'description clean test',
            company_subscriptions: [
              {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '03/09/2221 10:00:34'}
            ]
          },
          {
            id: 2,
            url_name: 'url name test2',
            image_profil: 'image profil test2',
            name: 'test name2',
            note: 4.235,
            address: {
              postal_address: 'postal address test2',
              postal_code: 12345
            },
            city: {name: 'city name test2'},
            category: {name: 'category name test2'},
            sous_categorys: [{name: 'Beauté', code: 'BEAU'}],
            description_clean: 'description clean test',
            company_subscriptions: [
              {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '01/09/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '01/04/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '03/10/2221 10:00:34'}
            ]
          }
        ]
      }));
      wrapper.vm.loading = true;
      await Opportunity.created.bind(wrapper.vm)();
      expect(wrapper.vm.companies).toEqual([
        {
          id: 1,
          url_name: 'url name test',
          image_profil: 'image profil test',
          name: 'test name',
          note: 4.235,
          address: {
            postal_address: 'postal address test',
            postal_code: 12345
          },
          city: {name: 'city name test'},
          category: {name: 'category name test'},
          sous_categorys: [{name: 'Beauté', code: 'BEAU'}],
          description_clean: 'description clean test',
          company_subscriptions: [
            {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '03/09/2221 10:00:34'}
          ]
        },
        {
          id: 2,
          url_name: 'url name test2',
          image_profil: 'image profil test2',
          name: 'test name2',
          note: 4.235,
          address: {
            postal_address: 'postal address test2',
            postal_code: 12345
          },
          city: {name: 'city name test2'},
          category: {name: 'category name test2'},
          sous_categorys: [{name: 'Beauté', code: 'BEAU'}],
          description_clean: 'description clean test',
          company_subscriptions: [
            {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '01/09/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '01/04/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '03/10/2221 10:00:34'}
          ]
        }
      ]);
      expect(wrapper.vm.loading).toEqual(false);
      expect(spyOnSetPageByUrlParameter).toHaveBeenCalled();
    });

    it('creates error case', async() => {
      axios.get.mockImplementationOnce(() => {
        return Promise.reject(new Error('created() test error'));
      });
      const spyOnError = jest.fn();
      wrapper.vm.$handleError = spyOnError;
      await Opportunity.created.bind(wrapper.vm)();
      expect(wrapper.vm.loading).toEqual(false);
      expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
    });
  });

  describe('methods test', () => {
    it('customerListCliqued test', () => {
      wrapper.vm.customerListCliqued();
      expect(wrapper.vm.customerList).toEqual(true);
      expect(wrapper.vm.companyList).toEqual(false);
      expect(wrapper.vm.quoteList).toEqual(false);
    });

    it('companyListCliqued test', () => {
      wrapper.vm.companyListCliqued();
      expect(wrapper.vm.customerList).toEqual(false);
      expect(wrapper.vm.companyList).toEqual(true);
      expect(wrapper.vm.quoteList).toEqual(false);
    });

    it('quoteListCliqued test', () => {
      wrapper.vm.quoteListCliqued();
      expect(wrapper.vm.customerList).toEqual(false);
      expect(wrapper.vm.companyList).toEqual(false);
      expect(wrapper.vm.quoteList).toEqual(true);
    });

    describe('setPageByUrlParameter test', () => {
      it('with page = quote', () => {
        window.location = {
          search: '?page=quote'
        };
        const spyOnGetUrlParameter = jest.spyOn(wrapper.vm, 'getUrlParameter');
        const spyOnQuoteList = jest.spyOn(wrapper.vm, 'quoteListCliqued');
        const spyOnCompanyList = jest.spyOn(wrapper.vm, 'companyListCliqued');
        const spyOnCustomerList = jest.spyOn(wrapper.vm, 'customerListCliqued');
        wrapper.vm.setPageByUrlParameter();
        expect(spyOnGetUrlParameter).toHaveBeenCalledWith('page');
        expect(spyOnQuoteList).toHaveBeenCalled();
        expect(spyOnCompanyList).not.toHaveBeenCalledWith();
        expect(spyOnCustomerList).not.toHaveBeenCalledWith();

      });

      it('with page = company', () => {
        window.location = {
          search: '?page=company'
        };
        const spyOnGetUrlParameter = jest.spyOn(wrapper.vm, 'getUrlParameter');
        const spyOnQuoteList = jest.spyOn(wrapper.vm, 'quoteListCliqued');
        const spyOnCompanyList = jest.spyOn(wrapper.vm, 'companyListCliqued');
        const spyOnCustomerList = jest.spyOn(wrapper.vm, 'customerListCliqued');
        wrapper.vm.setPageByUrlParameter();
        expect(spyOnGetUrlParameter).toHaveBeenCalledWith('page');
        expect(spyOnQuoteList).not.toHaveBeenCalled();
        expect(spyOnCompanyList).toHaveBeenCalledWith();
        expect(spyOnCustomerList).not.toHaveBeenCalledWith();

      });

      it('with page = customer', () => {
        window.location = {
          search: '?page=customer'
        };
        const spyOnGetUrlParameter = jest.spyOn(wrapper.vm, 'getUrlParameter');
        const spyOnQuoteList = jest.spyOn(wrapper.vm, 'quoteListCliqued');
        const spyOnCompanyList = jest.spyOn(wrapper.vm, 'companyListCliqued');
        const spyOnCustomerList = jest.spyOn(wrapper.vm, 'customerListCliqued');
        wrapper.vm.setPageByUrlParameter();
        expect(spyOnGetUrlParameter).toHaveBeenCalledWith('page');
        expect(spyOnQuoteList).not.toHaveBeenCalled();
        expect(spyOnCompanyList).not.toHaveBeenCalledWith();
        expect(spyOnCustomerList).toHaveBeenCalledWith();

      });

      it('default case', () => {
        window.location = {
          search: '?page=comments'
        };
        const spyOnGetUrlParameter = jest.spyOn(wrapper.vm, 'getUrlParameter');
        const spyOnQuoteList = jest.spyOn(wrapper.vm, 'quoteListCliqued');
        const spyOnCompanyList = jest.spyOn(wrapper.vm, 'companyListCliqued');
        const spyOnCustomerList = jest.spyOn(wrapper.vm, 'customerListCliqued');
        wrapper.vm.setPageByUrlParameter();
        expect(spyOnGetUrlParameter).toHaveBeenCalledWith('page');
        expect(spyOnQuoteList).not.toHaveBeenCalled();
        expect(spyOnCompanyList).not.toHaveBeenCalledWith();
        expect(spyOnCustomerList).toHaveBeenCalledWith();

      });
    });
  });
});
