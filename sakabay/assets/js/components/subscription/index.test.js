import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import SubscriptionIndex from './index.vue';
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
    wrapper = shallow(SubscriptionIndex, {
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
    const spyOnGetAllSubscriptions = jest.spyOn(wrapper.vm, 'getAllSubscriptions');
    await SubscriptionIndex.created.bind(wrapper.vm)();
    expect(spyOnGetAllSubscriptions).toHaveBeenCalled();
  });

  describe('methods test', () => {
    describe('getAllSubscriptions', () => {
      it('success case', async() => {
        axios.get
        .mockImplementationOnce(() => Promise.resolve({data: [
          {
            id:1,
            answers: [{quote: true}],
            besoin_statut: {code: 'PUB'},
            name: 'test name',
            code: 'TEST'
          }
        ]}));
        wrapper.vm.loading = true;
        await wrapper.vm.getAllSubscriptions();
        expect(wrapper.vm.subscriptions).toEqual([
          {
            id:1,
            answers: [{quote: true}],
            besoin_statut: {code: 'PUB'},
            name: 'test name',
            code: 'TEST'
          }
        ]);
      });

      it('error case', async() => {
        const spyOnHandleError = jest.fn();
        wrapper.vm.$handleError = spyOnHandleError;
        axios.get.mockImplementationOnce(() => {
          return Promise.reject(new Error('test error case'));
        });
        wrapper.vm.loading = true;
        await wrapper.vm.getAllSubscriptions();
        expect(spyOnHandleError).toHaveBeenCalledWith(new Error('test error case'));
        expect(wrapper.vm.loading).toEqual(false);
      });
    });

    describe('getImage', () => {
      it('with code == FRE', () => {
        let subscription = {
          code: 'FRE'
        };
        expect(wrapper.vm.getImage(subscription)).toEqual('build/gratuite.jpg');
      });
      it('with code == PRE', () => {
        let subscription = {
          code: 'PRE'
        };
        expect(wrapper.vm.getImage(subscription)).toEqual('build/premium.jpg');
      });

      it('other case', () => {
        let subscription = {
          code: 'TEST'
        };
        expect(wrapper.vm.getImage(subscription)).toEqual('build/');
      });
    });
  });
});
