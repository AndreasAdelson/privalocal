import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import PendingBesoin from './index.vue';

const localVue = createLocalVue();
localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);

let wrapper;

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
    wrapper = shallow(PendingBesoin, {
      i18n,
      localVue,
      propsData: {
        pendingBesoin: {
          id:1,
          dt_created: '01/01/2021 10:00:34',
          title: 'title test',
          category: {name: 'test category name'},
          sous_categorys: [{name: 'test sous category name'}],
          description: 'description test',
          answers: []
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
  describe('computed', () => {
    it ('dtCreated test', () =>{
      expect(wrapper.vm.dtCreated).toEqual('le 01/01/2021 à 10:00');
    });
    it ('nbAnswer test', () =>{
      expect(wrapper.vm.nbAnswer).toEqual(0);
    });
  });
  describe('methods test', () => {
    it('manageRequest test', () => {
      let besoin = {
        id:1,
        dt_created: '01/01/2021 10:00:34',
        title: 'title test',
        category: {name: 'test category name'},
        sous_categorys: [{name: 'test sous category name'}],
        description: 'description test',
        answers: []
      };

      const spyOnEmit = jest.fn();
      wrapper.vm.$emit = spyOnEmit;
      wrapper.vm.manageRequest(besoin);
      expect(spyOnEmit).toHaveBeenCalledWith('manage-modal-opened', {besoin: besoin});
    });

    it('requestQuote test', () => {
      const spyOnEmit = jest.fn();
      wrapper.vm.$emit = spyOnEmit;
      wrapper.vm.requestQuote('test company name', 0, 1);
      expect(spyOnEmit).toHaveBeenCalledWith('request-quote-modal-opened', {company_name: 'test company name', answer_id: 0, answer_index: 1});
    });

    describe('toggleIcon', () => {
      it('with iconArrow == chevron right', () => {
        wrapper.vm.iconArrow = 'chevron-right';
        wrapper.vm.toggleIcon();
        expect(wrapper.vm.iconArrow).toEqual('chevron-down');
      });
      it('with iconArrow == chevron down', () => {
        wrapper.vm.iconArrow = 'chevron-down';
        wrapper.vm.toggleIcon();
        expect(wrapper.vm.iconArrow).toEqual('chevron-right');
      });
    });

    describe('setStatutsAnswer', () => {
      it('with request_quote && quote == false',() => {
        let answer = {
          request_quote: false,
          quote: false
        };
        expect(wrapper.vm.setStatutsAnswer(answer)).toEqual('yellow-light-bg-skb');
      });
      it('with request_quote && quote == true',() => {
        let answer = {
          request_quote: true,
          quote: true
        };
        expect(wrapper.vm.setStatutsAnswer(answer)).toEqual('green-light-bg-skb');
      });
      it('with request_quote == true && quote == false',() => {
        let answer = {
          request_quote: true,
          quote: false
        };
        expect(wrapper.vm.setStatutsAnswer(answer)).toEqual('blue-light-bg-skb');
      });
    });
  });
});
