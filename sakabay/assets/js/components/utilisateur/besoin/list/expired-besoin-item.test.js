import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import ExpiredBesoinItem from './expired-besoin-item.vue';

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
    wrapper = shallow(ExpiredBesoinItem, {
      i18n,
      localVue,
      propsData: {
        expiredBesoin: {
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
  describe('compuetd', () => {
    it ('dtCreated test', () =>{
      expect(wrapper.vm.dtCreated).toEqual('le 01/01/2021 à 10:00');
    });
  });
  describe('methods test', () => {
    it('cancelRequest test', () => {
      const spyOnEmit = jest.fn();
      wrapper.vm.$emit = spyOnEmit;
      wrapper.vm.cancelRequest();
      expect(spyOnEmit).toHaveBeenCalledWith('delete-modal-opened');
    });
  });
});
