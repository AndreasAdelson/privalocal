import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import HistoryItem from './history-item.vue';
import { iteratee } from 'lodash';

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
    wrapper = shallow(HistoryItem, {
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

  it('created test', async() => {
    const spyOnSortHistory = jest.fn();
    wrapper.vm.sortHistory = spyOnSortHistory;
    await HistoryItem.created.bind(wrapper.vm)();
    expect(spyOnSortHistory).toHaveBeenCalled();
  });

  describe('methods test', () => {
    it('getDtDebutLabel test', () => {
      let date = '02/09/2021 10:00:34';
      expect( wrapper.vm.getDtDebutLabel(date)).toEqual('le 02/09/2021, à 10:00');
    });


    it('sortHistory test', () =>{
      wrapper.vm.companySubscriptions = [
        {dt_debut: '02/01/2021 10:00:34', dt_fin: '02/01/2221 10:00:34', subscription: {name: 'test1'}, subscription_status: {code: 'TEST'}},
        {dt_debut: '01/01/2020 10:00:34', dt_fin: '01/01/2021 10:00:34', subscription: {name: 'test2'}, subscription_status: {code: 'TEST'}},
        {dt_debut: '01/01/2021 10:00:34', dt_fin: '02/01/2021 10:00:34', subscription: {name: 'test3'},subscription_status: {code: 'TEST'}}
      ];
      wrapper.vm.sortHistory();
      expect(wrapper.vm.printedHistory).toEqual([
        {dt_debut: '02/01/2021 10:00:34', dt_fin: '02/01/2221 10:00:34', isActive: true, subscription: {name: 'test1'}, subscription_status: {code: 'TEST'}},
        {dt_debut: '01/01/2021 10:00:34', dt_fin: '02/01/2021 10:00:34', isActive: false, subscription: {name: 'test3'}, subscription_status: {code: 'TEST'}},
        {dt_debut: '01/01/2020 10:00:34', dt_fin: '01/01/2021 10:00:34', isActive: false, subscription: {name: 'test2'}, subscription_status: {code: 'TEST'}}
      ]);
    });

    describe('isHistoryActive test', () => {
      it('with VAL code and isActive true', () => {
        let history = {dt_debut: '02/01/2021 10:00:34', dt_fin: '02/01/2221 10:00:34', subscription: {name: 'test1'}, subscription_status: {code: 'VAL'}, isActive: true};
        expect(wrapper.vm.isHistoryActive(history)).toEqual(wrapper.vm.$t('dashboard.history.table.in_progress'));
      });

      it('with VAL code and isActive false', () => {
        let history = {dt_debut: '02/01/2021 10:00:34', dt_fin: '02/01/2221 10:00:34', subscription: {name: 'test1'}, subscription_status: {code: 'VAL'}, isActive: false};
        expect(wrapper.vm.isHistoryActive(history)).toEqual(wrapper.vm.$t('dashboard.history.table.coming_soon'));
      });

      it('with TER code', () => {
        let history = {dt_debut: '02/01/2021 10:00:34', dt_fin: '02/01/2221 10:00:34', subscription: {name: 'test1'}, subscription_status: {code: 'TER'}, isActive: false};
        expect(wrapper.vm.isHistoryActive(history)).toEqual(wrapper.vm.$t('dashboard.history.table.ended'));
      });

      it('with ENC code', () => {
        let history = {dt_debut: '02/01/2021 10:00:34', dt_fin: '02/01/2221 10:00:34', subscription: {name: 'test1'}, subscription_status: {code: 'ENC'}, isActive: true};
        expect(wrapper.vm.isHistoryActive(history)).toEqual(wrapper.vm.$t('dashboard.history.table.in_progress'));
      });

      it('with ANN code and isActive true', () => {
        let history = {dt_debut: '02/01/2021 10:00:34', dt_fin: '02/01/2221 10:00:34', subscription: {name: 'test1'}, subscription_status: {code: 'ANN'}, isActive: true};
        expect(wrapper.vm.isHistoryActive(history)).toEqual(wrapper.vm.$t('dashboard.history.table.canceled_renewal'));
      });

      it('with ANN code and isActive false and dtFin isAfter', () => {
        let history = {dt_debut: '02/01/2021 10:00:34', dt_fin: '02/01/2221 10:00:34', subscription: {name: 'test1'}, subscription_status: {code: 'ANN'}, isActive: false};
        expect(wrapper.vm.isHistoryActive(history)).toEqual(wrapper.vm.$t('dashboard.history.table.canceled'));
      });

      it('with ANN code and isActive false and dtFin is not after now', () => {
        let history = {dt_debut: '01/01/2021 10:00:34', dt_fin: '02/01/2021 10:00:34', subscription: {name: 'test1'}, subscription_status: {code: 'ANN'}, isActive: false};
        expect(wrapper.vm.isHistoryActive(history)).toEqual(wrapper.vm.$t('dashboard.history.table.ended'));
      });

      it('with OFF code and isActive true', () => {
        let history = {dt_debut: '02/01/2021 10:00:34', dt_fin: '02/01/2221 10:00:34', subscription: {name: 'test1'}, subscription_status: {code: 'OFF'}, isActive: true};
        expect(wrapper.vm.isHistoryActive(history)).toEqual(wrapper.vm.$t('dashboard.history.table.offer'));
      });

      it('with OFF code and isActive false', () => {
        let history = {dt_debut: '02/01/2021 10:00:34', dt_fin: '02/01/2221 10:00:34', subscription: {name: 'test1'}, subscription_status: {code: 'OFF'}, isActive: false};
        expect(wrapper.vm.isHistoryActive(history)).toEqual(wrapper.vm.$t('dashboard.history.table.ended'));
      });

    });

    describe('getPriceLabel test', () => {
      it('with OFF code status', () => {
        let history = { subscription: {name: 'test1', price: 10}, subscription_status: {code: 'OFF'}, isActive: true};
        expect(wrapper.vm.getPriceLabel(history)).toEqual('0');
      });

      it('with other code status', () => {
        let history = { subscription: {name: 'test1', price: 10}, subscription_status: {code: 'VAL'}, isActive: true};
        expect(wrapper.vm.getPriceLabel(history)).toEqual(10);
      });
    });
  });
});
