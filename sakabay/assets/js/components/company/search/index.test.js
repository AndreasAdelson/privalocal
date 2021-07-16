import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import CompanySearch from './index.vue';
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
    wrapper = shallow(CompanySearch, {
      i18n,
      localVue,

    });

    wrapper.vm.$refs.autocomplete.setValue = jest.fn();

  });

  /**
   * Test the initialization of the Vue component.
   */
  it('initializes', () => {
    expect(wrapper.vm).toBeDefined();
  });

  describe('computed test', () => {
    it('categoryIsSet test', () => {
      wrapper.vm.filters.category = {name: 'category name test', code: 'TEST'};
      expect(wrapper.vm.categoryIsSet).toEqual({name: 'category name test', code: 'TEST'});
    });
  });

  describe('created test', () => {
    it('creates', async() => {
      axios.get
      .mockImplementationOnce(() => Promise.resolve({data: {name: 'category test name', code: 'TEST'}}));
      wrapper.vm.loading = true;
      await CompanySearch.created.bind(wrapper.vm)();
      expect(wrapper.vm.category).toEqual({name: 'category test name', code: 'TEST'});
      expect(wrapper.vm.loading).toEqual(false);
    });

    it('creates error case', async() => {
      axios.get.mockImplementationOnce(() => {
        return Promise.reject(new Error('created() test error'));
      });
      const spyOnError = jest.fn();
      wrapper.vm.$handleError = spyOnError;
      await CompanySearch.created.bind(wrapper.vm)();
      expect(wrapper.vm.loading).toEqual(false);
      expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
    });
  });

  describe('methods test', () => {
    describe('applyFilter test', () => {
      it('success case', async() => {
        axios.get
        .mockImplementationOnce(() => Promise.resolve({
          data: [{name: 'company test name', url_name: 'TEST'}],
          headers: {'x-total-count': 4}
        }));
        const spyOnSetFilter = jest.spyOn(wrapper.vm, 'setFilter');
        await wrapper.vm.applyFilter();
        expect(spyOnSetFilter).toHaveBeenCalled();
        expect(wrapper.vm.loading2).toEqual(false);
        expect(wrapper.vm.pager.totalRows).toEqual(4);
        expect(wrapper.vm.companies).toEqual([{name: 'company test name', url_name: 'TEST'}]);
      });

      it('error case', async() => {
        axios.get.mockImplementationOnce(() => {
          return Promise.reject(new Error('applyFilter() test error'));
        });
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        await wrapper.vm.applyFilter();
        expect(wrapper.vm.loading).toEqual(false);
        expect(spyOnError).toHaveBeenCalledWith(new Error('applyFilter() test error'));
      });
    });

    it('setCity test', () => {
      const city = {name: 'Cayenne'};
      wrapper.vm.setCity(city);
      expect(wrapper.vm.filters.city).toEqual(city);
    });

    it('resetCity test', () => {
      wrapper.vm.resetCity();
      expect(wrapper.vm.filters.city).toEqual(null);
    });
  });
});
