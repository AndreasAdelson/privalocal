import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import CompanyCard from './company-card.vue';

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
    wrapper = shallow(CompanyCard, {
      i18n,
      localVue,
      propsData: {
        company: {
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
    it('roundedNote test', () => {
      wrapper.vm.company.note = 4.1234;
      expect(wrapper.vm.roundedNote).toEqual(4.1);
    });
  });

  describe('methods test', () => {
    describe('sortSubscription test with active susbcription', () => {
      it('with active susbcription', () => {
        wrapper.vm.company.company_subscriptions = [
          {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34'},
          {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34'},
          {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
          {dt_debut: '01/01/2021', dt_fin: '03/09/2221 10:00:34'}
        ];
        wrapper.vm.sortSubscriptions();
        expect(wrapper.vm.company.company_subscriptions).toEqual([
          {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '03/09/2221 10:00:34'}
        ]);
        expect(wrapper.vm.isSubscriptionActive).toEqual(true);

      });

      it('without active susbcription', () => {
        wrapper.vm.company.company_subscriptions = [
          {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34'},
          {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34'},
          {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
        ];
        wrapper.vm.sortSubscriptions();
        expect(wrapper.vm.company.company_subscriptions).toEqual([
          {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34'},
          {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34'},
          {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
        ]);
        expect(wrapper.vm.isSubscriptionActive).toEqual(false);
      });

      it('without subscritions', () => {
        wrapper.vm.company.company_subscriptions = [];
        wrapper.vm.sortSubscriptions();
        expect(wrapper.vm.company.company_subscriptions).toEqual([]);
        expect(wrapper.vm.isSubscriptionActive).toEqual(false);
      });
    });


  });
});
