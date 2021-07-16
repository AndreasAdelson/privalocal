import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import OpportunityCompanyCard from './opportunity-card.vue';
import { EventBus } from 'plugins/eventBus';

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
    wrapper = shallow(OpportunityCompanyCard, {
      i18n,
      localVue,
      propsData: {
        opportunity: {
          title: 'test title',
          dt_created: '01/01/2021',
          description: 'test description',
          isAnswered: true,
          answers: [{id: 1}]
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
    it('isAnswered', () => {
      expect(wrapper.vm.isAnswered).toEqual(true);
    });

    it('cloneOpportunity', () => {
      expect(wrapper.vm.cloneOpportunity).toEqual({
        title: 'test title',
        dt_created: '01/01/2021',
        description: 'test description',
        isAnswered: true,
        answers: [{id: 1}]
      });
    });

    it('nbAnswers', () => {
      expect(wrapper.vm.nbAnswers).toEqual(1);
    });
  });
  describe('created', () => {
    it('on create', async() =>{
      const spyOnEmitBusOn = jest.spyOn(EventBus, '$on');
      await OpportunityCompanyCard.created.bind(wrapper.vm)();
      expect(spyOnEmitBusOn).toHaveBeenCalled();
      expect(wrapper.vm.loading).toEqual(false);
    });
  });

  describe('methods test', () => {
    it('openModal test', () => {
      const spyOnEmit = jest.fn();
      wrapper.vm.$emit = spyOnEmit;
      wrapper.vm.openModal();
      expect(spyOnEmit).toHaveBeenCalledWith('modal-openned');
    });
  });
});
