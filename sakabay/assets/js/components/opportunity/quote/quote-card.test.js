import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import QuoteCard from './quote-card.vue';

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
    wrapper = shallow(QuoteCard, {
      i18n,
      localVue,
      propsData: {
        pendingQuote: {
          dt_created: '01/01/2021 10:00:34',
          title: 'test title',
          description: 'test description',
          id: 1,
        },
        companySelected: {
          url_name: 'test url_name'
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

  describe('computed test', () => {
    it('dtCreated test', () => {
      expect(wrapper.vm.dtCreated).toEqual('le 01/01/2021 à 10:00');
    });
  });
});
