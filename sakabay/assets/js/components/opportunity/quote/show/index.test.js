import { shallow, createLocalVue } from 'vue-test-utils';
import axios from 'axios';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import QuoteShow from './index.vue';

const loggerOptions = {
  isEnabled: true,
  logLevel : 'debug',
  stringifyArguments : false,
  showLogLevel : true,
  showMethodName : true,
  separator: '|',
  showConsoleColors: true
};
const localVue = createLocalVue();

localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);
localVue.use(VueLogger, loggerOptions);

let wrapper;

jest.mock('axios', () => ({
  get: jest.fn(() => {
    return Promise.resolve({data: {
      answers: [{quote: true}],
      besoin_statut: {code: 'TEST'}
    }});
  })
}));

describe('Answer to an opportunity modale', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(QuoteShow, {
      propsData: { companyId: 0 },
      i18n,
      localVue,
    });
  });

  it('initializes', () => {
    expect(wrapper.vm).toBeDefined();
  });

  describe('computed on', () => {
    describe('pdfUrl', () => {
      it('with if ok', async() => {
        wrapper.vm.opportunity = {
          title: 'test title',
          answers: [{quote: true}]
        };
        wrapper.vm.company = {
          name: 'test company name',
          url_name: 'urlname test'
        };
        await wrapper.vm.$nextTick();
        expect(wrapper.vm.pdfUrl).toEqual('/build/images/uploads/urlname test/true');
      });

      it('with if false', async() => {
        wrapper.vm.opportunity = {
          title: 'test title',
          answers: [{quote: false}],
          besoin_statut: {code: 'TEST'}
        };
        wrapper.vm.company = {
          name: 'test company name',
          url_name: 'urlname test'
        };
        await wrapper.vm.$nextTick();
        expect(wrapper.vm.pdfUrl).toEqual('');
      });
    });
  });

  describe('creates', () => {
    it('success case', async() => {
      axios.get
      .mockImplementationOnce(() => Promise.resolve({data: {
        title: 'test title',
        answers: [{quote: false}],
        besoin_statut: {code: 'TEST'}
      }}))
      .mockImplementationOnce(() => Promise.resolve({data: {
        name: 'test company name',
        url_name: 'urlname test'
      }}))
      ;
      wrapper.vm.loading = true;
      await QuoteShow.created.bind(wrapper.vm)();
      expect(wrapper.vm.opportunity).toEqual({
        title: 'test title',
        answers: [{quote: false}],
        besoin_statut: {code: 'TEST'}
      });
      expect(wrapper.vm.company).toEqual({
        name: 'test company name',
          url_name: 'urlname test'
      });
      expect(wrapper.vm.loading).toEqual(false);
    });

    it('error case', async() => {
      axios.get.mockImplementationOnce(() => {
        return Promise.reject(new Error('created() test error'));
      });
      const spyOnError = jest.fn();
      wrapper.vm.$handleError = spyOnError;
      await QuoteShow.created.bind(wrapper.vm)();
      expect(wrapper.vm.loading).toEqual(false);
      expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
    });
  });
});

