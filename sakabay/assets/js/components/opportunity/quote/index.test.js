import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import OpportunityQuoteIndex from './index.vue';
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
    wrapper = shallow(OpportunityQuoteIndex, {
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
      wrapper.vm.currentPage = 1;
      expect(wrapper.vm.params).toEqual({
        company: 1,
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
      expect(spyOnResetSearch).toHaveBeenCalled();
      expect(spyOnGetOpportunities).toHaveBeenCalled();
    });
  });

  describe('methods test', () => {
    describe('getOpportunities test', () => {
      it('success case', async() => {
        axios.get
        .mockImplementationOnce(() => Promise.resolve({data: [
          {
            id:1,
            answers: [{quote: true}],
            besoin_statut: {code: 'PUB'}
          }
        ]}))
        .mockImplementationOnce(() => Promise.resolve({data:  [{
          id: 2,
          answers: [{quote: true}],
          besoin_statut: {code: 'PUB'}
        }]}))
        .mockImplementationOnce(() => Promise.resolve({data:  4}))
        .mockImplementationOnce(() => Promise.resolve({data:  5}));
        await wrapper.vm.getOpportunities();
        expect(wrapper.vm.printedEntities).toEqual([{
          id:1,
          answers: [{quote: true}],
          besoin_statut: {code: 'PUB'}
        }]);
        expect(wrapper.vm.printedCompanyQuote).toEqual([{
          id:2,
          answers: [{quote: true}],
          besoin_statut: {code: 'PUB'}
        }]);
        expect(wrapper.vm.nbResult).toEqual(4);
        expect(wrapper.vm.nbResult2).toEqual(5);
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

    describe('setColorCard test', () => {
      it('with quote == false && statut code == PUB', () => {
        let pendingQuote = {
          answers: [
            {
              quote: false
            }
          ],
          besoin_statut: {code: 'PUB'}
        };
        expect(wrapper.vm.setColorCard(pendingQuote)).toEqual('yellow-light-bg-skb');
      });
      it('with quote == false && statut code !== PUB', () => {
        let pendingQuote = {
          answers: [
            {
              quote: false
            }
          ],
          besoin_statut: {code: 'TEST'}
        };
        expect(wrapper.vm.setColorCard(pendingQuote)).toEqual('blue-light-bg-skb');
      });
      it('with quote == true', () => {
        let pendingQuote = {
          answers: [
            {
              quote: true
            }
          ],
          besoin_statut: {code: 'PUB'}
        };
        expect(wrapper.vm.setColorCard(pendingQuote)).toEqual('green-light-bg-skb');
      });
      it('without answers == true', () => {
        let pendingQuote = {
          answers: [
          ],
          besoin_statut: {code: 'PUB'}
        };
        expect(wrapper.vm.setColorCard(pendingQuote)).toEqual('');
      });
    });
  });
});
