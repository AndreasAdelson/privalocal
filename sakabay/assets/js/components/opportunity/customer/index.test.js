import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import OpportunityCustomerIndex from './index.vue';
import { EventBus } from 'plugins/eventBus';
import axios from 'axios';

const localVue = createLocalVue();
localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);

let wrapper;

jest.mock('axios', () => ({
  get: jest.fn(() => {
    return Promise.resolve({data: [{
    }]});
  }),
  post: jest.fn(() => {
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
    wrapper = shallow(OpportunityCustomerIndex, {
      i18n,
      localVue,
      propsData: {
        companySelected: {
          id: 1,
          category: {code: 'TEST'},
          sous_categorys: [{name: 'test sous cat', code: 'TESOU'}],
        }
      }
    });
  });

  /**
   * Test the initialization of the Vue component.
   */
  it('initializes', () => {
    expect(wrapper.vm).toBeDefined();
  });

  describe('computed attributes', () => {
    it('params', () => {
      wrapper.vm.category = {name: 'test category'};
      wrapper.vm.sousCategories = [{name: 'test sous category'}];
      wrapper.vm.currentPage = 1;
      expect(wrapper.vm.params).toEqual({
        category: {name: 'test category'},
        sousCategory: [{name: 'test sous category'}],
        currentPage: 1
      });
    });
  });

  describe('watcher on',() => {
    it('companySelected', async() => {
      wrapper.vm.companySelected = {};
      wrapper.vm.companySelected = {
        id: 1,
        category: {code: 'TEST'},
        sous_categorys: [{name: 'test sous cat', code: 'TESOU'}],
      };
      const spyOnResetSearch = jest.spyOn(wrapper.vm, 'resetSearch');
      const spyOnGetOpportunities = jest.spyOn(wrapper.vm, 'getOpportunities');
      await wrapper.vm.$nextTick();
      expect(wrapper.vm.category).toEqual('TEST');
      expect(wrapper.vm.sousCategories).toEqual(['TESOU']);
      expect(spyOnResetSearch).toHaveBeenCalled();
      expect(spyOnGetOpportunities).toHaveBeenCalled();
    });
  });

  describe('created', () => {
    it('on create', async() =>{
      const spyOnEmitBusOn = jest.spyOn(EventBus, '$on');
      await OpportunityCustomerIndex.created.bind(wrapper.vm)();
      expect(spyOnEmitBusOn).toHaveBeenCalled();
      expect(wrapper.vm.loading).toEqual(false);
    });
  });

  describe('methods test', () => {
    describe('getOpportunities test', () => {
      it('success case', async() => {
        axios.get
        .mockImplementationOnce(() => Promise.resolve({data: [
          {
            id:1,
            name: 'name category test',
            code: 'TEST'
          }
        ]}))
        .mockImplementationOnce(() => Promise.resolve({data: 4}));
        await wrapper.vm.getOpportunities();
        expect(wrapper.vm.printedEntities).toEqual([{
          id:1,
          name: 'name category test',
          code: 'TEST',
          isAnswered: false
        }]);
        expect(wrapper.vm.nbResult).toEqual(4);
        expect(wrapper.vm.firstAttempt).toEqual(false);
      });

      it('error case', async() => {
        axios.get.mockImplementationOnce(() => {
          return Promise.reject(new Error('created() test error'));
        });
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        await wrapper.vm.getOpportunities();
        expect(wrapper.vm.loading).toEqual(false);
        expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
      });
    });

    describe('checkAnswer', () => {
      it('with a company answer', () => {
        wrapper.vm.companySelected.id = 10;
        let opportunities = [
          {
            id:1,
            name: 'name category test',
            code: 'TEST',
            answers: [{company: {id: 10}}]
          }
        ];
        expect(wrapper.vm.checkAnswer(opportunities)).toEqual([
          {
            id:1,
            name: 'name category test',
            code: 'TEST',
            answers: [{company: {id: 10}}],
            isAnswered: true
          }
        ]);
      });

      it('without company answer',() => {
        wrapper.vm.companySelected.id = 10;
        let opportunities = [
          {
            id:1,
            name: 'name category test',
            code: 'TEST',
            answers: [{company: {id: 9}}]
          }
        ];
        expect(wrapper.vm.checkAnswer(opportunities)).toEqual([
          {
            id:1,
            name: 'name category test',
            code: 'TEST',
            answers: [{company: {id: 9}}],
            isAnswered: false
          }
        ]);
      });
    });
  });
});
