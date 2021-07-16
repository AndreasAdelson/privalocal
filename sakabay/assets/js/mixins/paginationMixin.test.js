import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import PaginationMixin from 'mixins/paginationMixin';

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

beforeEach(() => {
  const i18n = new VueI18n({
    locale: 'fr',
    fallbackLocale: 'fr',
    messages: {
      'en': require('i18n/en.json'),
      'fr': require('i18n/fr.json')
    }
  });
  wrapper = shallow(PaginationMixin, {
    i18n,
    localVue,
  });
});

describe('methods', () => {
  it('applyFilter', () => {
    wrapper.vm.currentFilter = 'test';
    wrapper.vm.applyFilter();
    expect(wrapper.vm.table.filter).toEqual('test');
    expect(wrapper.vm.pager.currentPage).toEqual(1);
    wrapper.vm.pager.currentPage = 2;
    wrapper.vm.currentFilter = 'new filter';
    wrapper.vm.applyFilter();
    expect(wrapper.vm.pager.currentPage).toEqual(1);
    expect(wrapper.vm.table.filter).toEqual('new filter');
  });
});
