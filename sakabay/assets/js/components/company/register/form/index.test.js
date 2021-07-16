import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import CompanyRegisterForm from './index.vue';
import axios from 'axios';

const localVue = createLocalVue();
localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);

let wrapper;

jest.mock('axios', () => ({
  get: jest.fn(() => {
    return Promise.resolve({data: {}});
  }),
  post: jest.fn(() => {
    return Promise.resolve({data: {}});
  })
}));

describe('company register page', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(CompanyRegisterForm, {
      i18n,
      localVue,
    });

    wrapper.vm.$refs.autocomplete.setValue = jest.fn();
    wrapper.vm.$refs.autocomplete.json = jest.fn();

  });

  /**
   * Test the initialization of the Vue component.
   */
  it('initializes', () => {
    expect(wrapper.vm).toBeDefined();
  });

  describe('creates with utilisateurId', () => {
    it('success case', async() => {
      axios.get
        .mockImplementationOnce(() => Promise.resolve({data: [{
          id:1,
          name: 'Beauté',
          code: 'BEA'
      }]}))
      .mockImplementationOnce(() => Promise.resolve({data: {
        id: 10,
        username: 'toto',
        first_name: 'Toto',
        last_name: 'Titi'
      }}));
      wrapper.vm.utilisateurId = 10;
      wrapper.vm.loading = true;
      await CompanyRegisterForm.created.bind(wrapper.vm)();
      expect(wrapper.vm.utilisateur).toEqual({
        id: 10,
        username: 'toto',
        first_name: 'Toto',
        last_name: 'Titi'
      });
      expect(wrapper.vm.category).toEqual([{
        id:1,
        name: 'Beauté',
        code: 'BEA'
    }]);
    });

    it('error case', async() => {
      axios.get.mockImplementationOnce(() => {
        return Promise.reject(new Error('created() test error'));
      });
      const spyOnError = jest.fn();
      wrapper.vm.$handleError = spyOnError;
      await CompanyRegisterForm.created.bind(wrapper.vm)();
      expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
    });

  });

  describe('methods test', () => {
    describe('getLongLat test', () => {
      it('success case', async() => {
        wrapper.vm.formFields.address = {postalAddress: '8 lotissement Ilang Ilang', postalCode: '97300'};
        wrapper.vm.formFields.city = 'Cayenne';
        axios.get
          .mockImplementationOnce(() => {
            return Promise.resolve({data: {
              id:1,
              features: [{geometry: {coordinates: ['123', '456']}}]
            }});
          });
        await wrapper.vm.getLongLat();
        expect(wrapper.vm.loadingMap).toEqual(false);
        expect(wrapper.vm.position.lng).toEqual('123');
        expect(wrapper.vm.position.lat).toEqual('456');
      });

      it('error case', async() => {
        axios.get.mockImplementationOnce(() => {
          return Promise.reject(new Error('created() test error'));
        });
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        await wrapper.vm.getLongLat();
        expect(wrapper.vm.loadingMap).toEqual(false);
        expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
      });
    });

    describe('getSousCategorys test', () => {
      it('success case', async() => {
        wrapper.vm.formFields.category = {id: 20};
        axios.get
          .mockImplementationOnce(() => {
            return Promise.resolve({data: [{
              id:1,
              name: 'Loisir',
              code: 'LOI'
            }]});
          });
          await wrapper.vm.getSousCategorys();
          expect(wrapper.vm.sousCategorys).toEqual([{
            id:1,
            name: 'Loisir',
            code: 'LOI'
          }]);
      });
      it('error case', async() => {
        wrapper.vm.formFields.category = {id: 20};
        axios.get.mockImplementationOnce(() => {
          return Promise.reject(new Error('created() test error'));
        });
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        wrapper.vm.$handleError = spyOnError;
        await wrapper.vm.getSousCategorys();
        expect(wrapper.vm.loading).toEqual(false);
        expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
      });
    });

    it('setCity test', () => {
      wrapper.vm.formFields.address = {postalAddress: '8 lotissement Ilang Ilang', postalCode: '97300'};
      let city = 'Cayenne';
      const spyOnGetLongLat = jest.spyOn(wrapper.vm, 'getLongLat');
      wrapper.vm.setCity(city);
      expect(wrapper.vm.formFields.city).toEqual('Cayenne');
      expect(spyOnGetLongLat).toHaveBeenCalled();
    });

    describe('submitForm test', () => {
      it('during/before the request to API with first if true', () => {
        const spyOnPost = jest.spyOn(axios, 'post');
        wrapper.vm.position.lat = 1234;
        wrapper.vm.formFields.address.latitude = 5678;
        wrapper.vm.position.lng = 789;
        wrapper.vm.formFields.address.longitude = 123;
        wrapper.vm.companyUrlName = 1;
        // wrapper.vm.$refs.autocomplete.json.length = 2;
        axios.post.mockImplementationOnce(() => Promise.resolve({
          headers: { location: 'assign url 2' }
        }));
        wrapper.vm.submitForm();
        expect(wrapper.vm.loading).toEqual(true);
        expect(spyOnPost).toHaveBeenCalledWith('/api/companies', wrapper.vm.$getFormFieldsData(wrapper.vm.formFields));
      });
      describe('error case', () => {
        it('server error (500) : logs the error’s message', async() => {
          axios.post.mockImplementationOnce(() => {
            return Promise.reject(new Error('erreur 500 test'));
          });
          const spy = jest.fn();
          wrapper.vm.$handleError = spy;
          await wrapper.vm.submitForm();
          expect(wrapper.vm.loading).toEqual(false);
          expect(spy).toHaveBeenCalledWith(new Error('erreur 500 test'));
        });
        it('constraint validation violation error (400)', async() => {
          wrapper.vm.$handleFormErrors = jest.fn();
          const spyOnHandleFormErrors = jest.spyOn(wrapper.vm, '$handleFormError');
          const error = new Error('Error test');
          axios.post.mockImplementationOnce(() => {
            error.response = {
              status: 400,
              data: { errors: { children: {} } },
            };
            return Promise.reject(error);
          });
          await wrapper.vm.submitForm();
          expect(wrapper.vm.loading).toEqual(false);
          expect(spyOnHandleFormErrors).toHaveBeenCalled( );
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
    it('resetSousCategorys', () => {
      wrapper.vm.formFields.sousCategorys = 'toto';
      wrapper.vm.resetSousCategorys();
      expect(wrapper.vm.formFields.sousCategorys).toEqual([]);
    });
  });
});
