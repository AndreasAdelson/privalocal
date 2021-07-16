import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import CommentForm from './index.vue';
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
      });
  }),
  post: jest.fn(() => {
    return Promise.resolve({
      data: {},
      });
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
    wrapper = shallow(CommentForm, {
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

  describe('created test', () => {
    it('creates with company id', async() => {
      axios.get
        .mockImplementationOnce(() => {
          return Promise.resolve({
            data: {
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
            },
          });
        });
        wrapper.vm.companyId = 10;
        wrapper.vm.loading = true;
        await CommentForm.created.bind(wrapper.vm)();
        expect(wrapper.vm.company).toEqual({
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
        });
        expect(wrapper.vm.loading).toEqual(false);
    });

    it('creates error case', async() => {
      axios.get.mockImplementationOnce(() => {
        return Promise.reject(new Error('created() test error'));
      });
      const spyOnError = jest.fn();
      wrapper.vm.$handleError = spyOnError;
      wrapper.vm.companyId = 10;
      await CommentForm.created.bind(wrapper.vm)();
      expect(wrapper.vm.loading).toEqual(false);
      expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
    });
  });

  describe('methods test', () => {
    describe('submitForm test', () => {
      it('during/before the request to API with first if true', () => {
        const spyOnPost = jest.spyOn(axios, 'post');
        wrapper.vm.utilisateurId = 1234;
        wrapper.vm.companyId = 1;
        axios.post.mockImplementationOnce(() => Promise.resolve({
          headers: { location: 'assign url 2' }
        }));
        wrapper.vm.submitForm();
        expect(wrapper.vm.loading).toEqual(true);
        expect(spyOnPost).toHaveBeenCalledWith('/api/comments', wrapper.vm.$getFormFieldsData(wrapper.vm.formFields));
      });
      describe('error case', () => {
        it('server error (500) : logs the error’s message', async() => {
          const spyOnHandleFormErrors = jest.spyOn(wrapper.vm, '$handleFormError');
          const error = new Error('Error test');
          error.response = {
            status: 500,
            data: { errors: { children: {} } },
          };
          axios.post.mockImplementationOnce(() => {
            return Promise.reject(error);
          });
          const spy = jest.fn();
          wrapper.vm.utilisateurId = 1234;
          wrapper.vm.companyId = 1;
          await wrapper.vm.submitForm();
          expect(wrapper.vm.loading).toEqual(false);
          expect(spyOnHandleFormErrors).toHaveBeenCalled();
        });
        it('constraint validation violation error (400)', async() => {
          const error = new Error('Error test');
          axios.post.mockImplementationOnce(() => {
            error.response = {
              status: 400,
              data: { errors: { children: {} } },
              headers: {'x-message': 'x-message error'}
            };
            return Promise.reject(error);
          });
          wrapper.vm.utilisateurId = 1234;
          wrapper.vm.companyId = 1;
          await wrapper.vm.submitForm();
          expect(wrapper.vm.loading).toEqual(false);
          expect(wrapper.vm.errorMessage).toEqual('x-message error');
        });
      });
    });
    it('goBack test', () => {
      wrapper.vm.urlPrecedente = '/sakabay';
      const spyOnGoBack = jest.fn();
      wrapper.vm.$goTo = spyOnGoBack;
      wrapper.vm.goBack();
      expect(spyOnGoBack).toHaveBeenCalledWith('/sakabay');
    });
  });
});
