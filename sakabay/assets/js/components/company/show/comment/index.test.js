import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import CommentIndex from './index.vue';
import axios from 'axios';

const localVue = createLocalVue();
localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);

let wrapper;

jest.mock('axios', () => ({
  get: jest.fn(() => {
    return Promise.resolve({
      data: {},
      headers: {
      'x-total-count': 35,
      'x-excellent-count': 5,
      'x-very-good-count': 6,
      'x-medium-count': 7,
      'x-low-count': 8,
      'x-horrible-count': 9,
    },});
  })
}));

describe('new comment index component', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(CommentIndex, {
      i18n,
      localVue,
      propsData: {
        company: {
          id: 10,
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
          ],
          utilisateur: {id: 12, image_profil: 'test image profil'}
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

  describe('created test', () => {
    it('creates', async() => {
      const spyOnApplyFilter = jest.spyOn(wrapper.vm, 'applyFilter');
      axios.get
        .mockImplementationOnce(() => {
          return Promise.resolve({
            data: [{
              id: 1,
              dt_created: '01/01/2021',
              message: 'test message',
              title: 'title test',
              author_company: {id: 12, image_profil: 'test image profil', name: 'test company name'},
              utilisateur: {username: 'username test'}
            }],
            headers: {
              'x-total-count': 35,
              'x-excellent-count': 5,
              'x-very-good-count': 6,
              'x-medium-count': 7,
              'x-low-count': 8,
              'x-horrible-count': 9,
            },
          });
        });
      wrapper.vm.loading = true;
      await CommentIndex.created.bind(wrapper.vm)();
      expect(wrapper.vm.printedComments).toEqual([{
          id: 1,
          dt_created: '01/01/2021',
          message: 'test message',
          title: 'title test',
          author_company: {id: 12, image_profil: 'test image profil', name: 'test company name'},
          utilisateur: {username: 'username test'},
          isCommentTruncated: true
      }]);
      expect(spyOnApplyFilter).toHaveBeenCalled();

    });

    it('creates error case', async() => {
      axios.get.mockImplementationOnce(() => {
        return Promise.reject(new Error('created() test error'));
      });
      const spyOnError = jest.fn();
      wrapper.vm.$handleError = spyOnError;
      await CommentIndex.created.bind(wrapper.vm)();
      expect(wrapper.vm.loading).toEqual(false);
      expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
    });
  });

  describe('methods test', () => {
    describe('applyFilter test', () => {
      it('success case', async() => {
        axios.get
        .mockImplementationOnce(() => {
          return Promise.resolve({
            data: [{
              id: 1,
              dt_created: '01/01/2021',
              message: 'test message',
              title: 'title test',
              author_company: {id: 12, image_profil: 'test image profil', name: 'test company name'},
              utilisateur: {username: 'username test'}
            }],
            headers: {
              'x-total-count': 35,
              'x-excellent-count': 5,
              'x-very-good-count': 6,
              'x-medium-count': 7,
              'x-low-count': 8,
              'x-horrible-count': 9,
            }
          });
        });
        const spyOnSetFilter = jest.spyOn(wrapper.vm, 'setFilter');
        await wrapper.vm.applyFilter();
        expect(spyOnSetFilter).toHaveBeenCalled();
        expect(wrapper.vm.loading).toEqual(false);
        expect(wrapper.vm.nbMaxComments).toEqual(35);
        expect(wrapper.vm.excellentResults).toEqual(5);
        expect(wrapper.vm.veryGoodResults).toEqual(6);
        expect(wrapper.vm.mediumResults).toEqual(7);
        expect(wrapper.vm.lowResults).toEqual(8);
        expect(wrapper.vm.horribleResults).toEqual(9);
        expect(wrapper.vm.printedComments).toEqual([{
          id: 1,
          dt_created: '01/01/2021',
          message: 'test message',
          title: 'title test',
          author_company: {id: 12, image_profil: 'test image profil', name: 'test company name'},
          utilisateur: {username: 'username test'},
          isCommentTruncated: true
        }]);
      });

      it('error case', async() => {
        axios.get.mockImplementationOnce(() => {
          return Promise.reject(new Error('applyFilter() test error'));
        });
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        await wrapper.vm.applyFilter();
        expect(wrapper.vm.loading).toEqual(false);
        expect(spyOnError).toHaveBeenCalledWith(new Error('applyFilter() test error'));
      });
    });
    describe('urlImageProfil test', () => {
      it('with author_company and image_profil set', () => {
        const comment = {
          author_company: {url_name: 'titi', image_profil: 'test'},
          utilisateur: {image_profil: 'test2'}
        };
        expect(wrapper.vm.urlImageProfil(comment)).toEqual('/build/images/uploads/titi/test');
      });

      it('without author_company and utilisateur set', () => {
        const comment = {
          author_company: null,
          utilisateur: {image_profil: 'test2'}
        };
        expect(wrapper.vm.urlImageProfil(comment)).toEqual('/build/images/uploads/test2');
      });

      it('without author_company and without image profil utilisateur', () => {
        const comment = {
          author_company: null,
          utilisateur: {image_profil: null}
        };
        expect(wrapper.vm.urlImageProfil(comment)).toEqual('/build/logo.png');
      });
    });

    describe('getName test', () => {
      it('with author_company', () => {
        const comment = {
          author_company: {name: 'test name'},
          utilisateur: {username: 'test2'}
        };
        expect(wrapper.vm.getName(comment)).toEqual('Test name');
      });

      it('without author_company', () => {
        const comment = {
          author_company: null,
          utilisateur: {username: 'test2'}
        };
        expect(wrapper.vm.getName(comment)).toEqual('Test2');
      });
    });

    it('toggleComment test', () => {
      const comment = {
        isCommentTruncated: true
      };
      wrapper.vm.toggleComment(comment, false);
    });

    describe('getPercent test', () => {
      it('with nbMaxComments > 0', () => {
        wrapper.vm.nbMaxComments = 10;
        wrapper.vm.getPercent(10, 1, 'excellent');
        expect(wrapper.vm.configCheckbox.excellent).toEqual(10);
      });

      it('with nbMaxComments == 0', () => {
        wrapper.vm.nbMaxComments = 0;
        wrapper.vm.getPercent(10, 1, 'excellent');
        expect(wrapper.vm.configCheckbox.excellent).toEqual(0);
      });
    });
  });
});
