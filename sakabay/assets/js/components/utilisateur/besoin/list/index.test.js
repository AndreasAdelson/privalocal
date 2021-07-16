import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import BesoinList from './index.vue';
import axios from 'axios';
import { EventBus } from 'plugins/eventBus';

const localVue = createLocalVue();
localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);

let wrapper;

jest.mock('axios', () => ({
  get: jest.fn(() => {
    return Promise.resolve({data: 4});
  }),
  post: jest.fn(() => {
    return Promise.resolve({data: {
    }});
  })
}));

describe('besoin form component', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(BesoinList, {
      i18n,
      localVue,
      propsData: {
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
    it('paramsPending', () => {
      wrapper.vm.currentPage = 1;
      expect(wrapper.vm.paramsPending).toEqual({
        codeStatut: 'PUB',
        currentPage: 1,
      });
    });

    it('paramsExpired', () => {
      wrapper.vm.currentPage2 = 1;
      expect(wrapper.vm.paramsExpired).toEqual({
        codeStatut: ['ARC', 'EXP'],
        currentPage: 1,
      });
    });

    describe('isCompany', () => {
      it('with entitySelected and entity is an user', () => {
        wrapper.vm.entitySelected = {
          first_name: 'Toto'
        };
        expect(wrapper.vm.isCompany).toEqual(false);
      });

      it('with entitySelected and entity is a company', () => {
        wrapper.vm.entitySelected = null;
        expect(wrapper.vm.isCompany).toEqual(true);
      });
    });
  });

  describe('creates test', () => {
    it('with besoinId', async() => {
      const spyOnEventBus = jest.spyOn(EventBus, '$on');
      const spyOnGetBesoins = jest.spyOn(wrapper.vm, 'getBesoins');
      axios.get
      .mockImplementationOnce(() => Promise.resolve({data:
        [
          {id: 1, name: 'besoin statut name test', code: 'TEST'}
        ]
      }))
      .mockImplementationOnce(() => Promise.resolve({data:[{id: 1, name: 'company name test', url_name: 'url name test', utilisateur: {id: 1, username: 'username test'}}]}));
      wrapper.vm.besoinId = 10;
      wrapper.vm.loading = true;
      await BesoinList.created.bind(wrapper.vm)();
      expect(wrapper.vm.besoinStatuts).toEqual([{id: 1, name: 'besoin statut name test', code: 'TEST'}]);
      expect(wrapper.vm.companies).toEqual([{id: 1, name: 'company name test', url_name: 'url name test',utilisateur: {id: 1,username: 'username test'}}, {id: 1, username: 'username test'}]);
      expect(wrapper.vm.loading).toEqual(false);
      expect(spyOnEventBus).toHaveBeenCalled();
      expect(spyOnGetBesoins).toHaveBeenCalled();
    });

    it('error case', async() => {
      axios.get
      .mockImplementationOnce(() => Promise.reject(new Error('error test')));
      const spyOnHandleError = jest.fn();
      wrapper.vm.$handleError = spyOnHandleError;
      wrapper.vm.loading = true;
      await BesoinList.created.bind(wrapper.vm)();
      expect(spyOnHandleError).toHaveBeenCalledWith(new Error('error test'));
      expect(wrapper.vm.loading).toEqual(false);

    });
  });

  describe('methods test', () => {
    it('onManagePendingService test', () => {
      let pendingBesoin = {
        id: 1,
        title: 'test besoin title',
        description: 'description test name',
        author: {id: 1}
      };
      wrapper.vm.currentPendingBesoin = null;
      wrapper.vm.onManagePendingService(0,pendingBesoin);
      expect(wrapper.vm.currentPendingBesoin).toEqual(pendingBesoin);
      expect(wrapper.vm.indexPending).toEqual(0);
    });

    it('onRequestQuotePending test', () => {
      let pendingBesoin = {
        id: 1,
        title: 'test besoin title',
        description: 'description test name',
        author: {id: 1}
      };
      let event = {
        company_name: 'test company name',
        answer_id: 2,
        answer_index: 3
      };
      wrapper.vm.currentPendingBesoinTitle = null;
      wrapper.vm.onRequestQuotePending(0,pendingBesoin,event);
      expect(wrapper.vm.indexPending).toEqual(0);
      expect(wrapper.vm.currentPendingBesoinTitle).toEqual('test besoin title');
      expect(wrapper.vm.currentCompanyName).toEqual('test company name');
      expect(wrapper.vm.currentAnswerId ).toEqual(2);
      expect(wrapper.vm.indexAnswer).toEqual(3);

    });

    describe('submitRequestQuote test', () => {
      it('success case', async() => {
        axios.post
        .mockImplementationOnce(() => {
          return Promise.resolve({
            headers: {
              location: 'location from headers'
            }
          });
        });
        wrapper.vm.pendingBesoins = [
          {
            answers: [{request_quote: false}]
          }
        ];
        wrapper.vm.indexPending = 0;
        wrapper.vm.indexAnswer = 0;
        wrapper.vm.loadingModal = true;
        await wrapper.vm.submitRequestQuote();
        expect(wrapper.vm.indexPending).toEqual(null);
        expect(wrapper.vm.indexAnswer).toEqual(null);
        expect(wrapper.vm.loadingModal).toEqual(false);
        expect(wrapper.vm.pendingBesoins[0].answers[0].request_quote).toEqual(true);
      });
    });

    describe('getBesoins', () => {
      it('success case', async() => {
        axios.get
        .mockImplementationOnce(() => Promise.resolve({data: [
          {
            id:1,
            answers: [{quote: true, request_quote: false}],
          }
        ]}))
        .mockImplementationOnce(() => Promise.resolve({data:  [{
          id: 2,
          answers: [{quote: true, request_quote: true}],
        }]}))
        .mockImplementationOnce(() => Promise.resolve({data: 4}))
        .mockImplementationOnce(() => Promise.resolve({data: 4}));
        wrapper.vm.entityId = 1;
        await wrapper.vm.getBesoins();
        expect(wrapper.vm.pendingBesoins).toEqual([{
          id:1,
          answers: [{quote: true, request_quote: false}],
        }]);
        expect(wrapper.vm.expiredBesoins).toEqual([{
          id: 2,
          answers: [{quote: true, request_quote: true}],
        }]);
        expect(wrapper.vm.nbResult1).toEqual(4);
        expect(wrapper.vm.nbResult2).toEqual(4);
        expect(wrapper.vm.firstAttempt).toEqual(false);
        expect(wrapper.vm.loading).toEqual(false);
      });

      it('error case', async() => {
        axios.get.mockImplementationOnce(() => {
          return Promise.reject(new Error('created() test error'));
        });
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        await wrapper.vm.getBesoins();
        expect(wrapper.vm.loading).toEqual(false);
        expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
      });
    });

    describe('onSelectEntity', () => {
      it('with entitySelected and utilisateur different', async() => {
        wrapper.vm.utilisateur = {
          id:1,
          username: 'username 1',
          first_name:'toto',
          last_name: 'titi'
        };
        wrapper.vm.entitySelected = {
          id:2,
          username: 'username 2',
          first_name:'titi',
          last_name: 'toto'
        };
        wrapper.vm.entityId = null;
        const spyOnResetSearch = jest.spyOn(wrapper.vm, 'resetSearch');
        const spyOnGetBesoins = jest.spyOn(wrapper.vm, 'getBesoins');
        await wrapper.vm.onSelectEntity();
        expect(wrapper.vm.entityId).toEqual(2);
        expect(spyOnGetBesoins).toHaveBeenCalled();
        expect(spyOnResetSearch).toHaveBeenCalled();

      });

      it('with entitySelected and utilisateur are same', async() => {
        wrapper.vm.utilisateur = {
          id:1,
          username: 'username 1',
          first_name:'toto',
          last_name: 'titi'
        };
        wrapper.vm.entitySelected = {
          id:1,
          username: 'username 1',
          first_name:'toto',
          last_name: 'titi'
        };
        wrapper.vm.entityId = null;
        const spyOnResetSearch = jest.spyOn(wrapper.vm, 'resetSearch');
        const spyOnGetBesoins = jest.spyOn(wrapper.vm, 'getBesoins');
        await wrapper.vm.onSelectEntity();
        expect(wrapper.vm.entityId).toEqual('');
        expect(spyOnGetBesoins).toHaveBeenCalled();
        expect(spyOnResetSearch).toHaveBeenCalled();

      });

      it('without entitySelected and utilisateur', async() => {
        wrapper.vm.utilisateur = null;
        wrapper.vm.entitySelected = null;
        wrapper.vm.entityId = null;
        const spyOnResetSearch = jest.spyOn(wrapper.vm, 'resetSearch');
        const spyOnGetBesoins = jest.spyOn(wrapper.vm, 'getBesoins');
        await wrapper.vm.onSelectEntity();
        expect(wrapper.vm.entityId).toEqual(null);
        expect(spyOnGetBesoins).toHaveBeenCalled();
        expect(spyOnResetSearch).toHaveBeenCalled();

      });
    });

    it('archiveBesoin', () => {
      wrapper.vm.pendingBesoins = [
        {
          id: 1,
          answers: [{quote: true, request_quote: true}],
        },
        {
          id: 2,
          answers: [{quote: false, request_quote: false}],
        },
      ];
      wrapper.vm.expiredBesoins = [];
      wrapper.vm.nbResult1 = 5;
      wrapper.vm.nbResult2 = 5;
      wrapper.vm.indexPending = 0;
      wrapper.vm.archiveBesoin();
      expect(wrapper.vm.pendingBesoins).toEqual(
        [
          {
            id: 2,
            answers: [{quote: false, request_quote: false}],
          },
        ]
      );
      expect(wrapper.vm.expiredBesoins  ).toEqual(
        [
          {
            id: 1,
            answers: [{quote: true, request_quote: true}],
          },
        ]
      );
      expect(wrapper.vm.nbResult1).toEqual(4);
      expect(wrapper.vm.nbResult2).toEqual(6);
    });

    it('deleteBesoin', () => {
      wrapper.vm.pendingBesoins = [
        {
          id: 1,
          answers: [{quote: true, request_quote: true}],
        },
        {
          id: 2,
          answers: [{quote: false, request_quote: false}],
        },
      ];
      wrapper.vm.currentPendingId  = 0;
      wrapper.vm.indexPending  = 0;
      wrapper.vm.nbResult1  = 5;
      wrapper.vm.deleteBesoin();
      expect(wrapper.vm.pendingBesoins).toEqual(
        [
          {
            id: 2,
            answers: [{quote: false, request_quote: false}],
          },
        ]
      );
      expect(wrapper.vm.currentPendingId).toEqual(null);
      expect(wrapper.vm.indexPending).toEqual(null);
      expect(wrapper.vm.nbResult1).toEqual(4);
    });
  });
});
