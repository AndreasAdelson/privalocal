import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import BesoinForm from './index.vue';
import axios from 'axios';

const localVue = createLocalVue();
localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);

let wrapper;

jest.mock('axios', () => ({
  get: jest.fn(() => {
    return Promise.resolve({data: [{
    }]});
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
    wrapper = shallow(BesoinForm, {
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
    it('categoryIsSet', () => {
      wrapper.vm.formFields.category = {
        id: 1,
        name: 'category name test',
        sous_categorys: [{id: 2, name: 'sous category name test'}]
      };
      expect(wrapper.vm.categoryIsSet).toEqual({
        id: 1,
        name: 'category name test',
        sous_categorys: [{id: 2, name: 'sous category name test'}]
      });
    });
  });

  describe('watcher on',() => {
    it('categoryIsSet', async() => {
      wrapper.vm.formFields.category = {
        id: 1,
        name: 'category name test',
        sous_categorys: [{id: 2, name: 'sous category name test'}]
      };
      await wrapper.vm.$nextTick();
      expect(wrapper.vm.sousCategory).toEqual([
        {id: 2, name: 'sous category name test'}
      ]);
    });
  });

  describe('creates test', () => {
    it('with besoinId', async() => {
      const spyOnRemoveFieldNotInForm = jest.spyOn(wrapper.vm, '$removeFieldsNotInForm');
      const spyOnSetEditForm = jest.spyOn(wrapper.vm, '$setEditForm');
      axios.get
      .mockImplementationOnce(() => Promise.resolve({data:
        [
          {id: 1, name: 'category name test', code: 'TEST'}
        ]
      }))
      .mockImplementationOnce(() => Promise.resolve({data:[{id: 1, name: 'company name test', url_name: 'url name test', utilisateur: {id: 1, username: 'username test'}}]}))
      .mockImplementationOnce(() => Promise.resolve({data:{id: 1, title: 'besoin title', description: 'description test'}}));
      wrapper.vm.besoinId = 10;
      wrapper.vm.loading = true;
      await BesoinForm.created.bind(wrapper.vm)();
      expect(spyOnRemoveFieldNotInForm).toHaveBeenCalled();
      expect(spyOnSetEditForm).toHaveBeenCalled();
      expect(wrapper.vm.category).toEqual([{id: 1, name: 'category name test', code: 'TEST'}]);
      expect(wrapper.vm.companies).toEqual([{id: 1, name: 'company name test', url_name: 'url name test',utilisateur: {id: 1,username: 'username test'}}, {id: 1, username: 'username test'}]);
      expect(wrapper.vm.loading).toEqual(false);
    });

    it('error case', async() => {
      axios.get
      .mockImplementationOnce(() => Promise.reject(new Error('error test')));
      const spyOnHandleError = jest.fn();
      wrapper.vm.$handleError = spyOnHandleError;
      wrapper.vm.loading = true;
      await BesoinForm.created.bind(wrapper.vm)();
      expect(spyOnHandleError).toHaveBeenCalledWith(new Error('error test'));
      expect(wrapper.vm.loading).toEqual(false);

    });
  });

  describe('methods test', () => {
    describe('submitForm test', () => {
      it('success case with no company for the user', async() => {
        wrapper.vm.utilisateurId = 10;
        wrapper.vm.formFields.company = {
          last_name: 'test last name',
          id: 10
        };
        axios.post
        .mockImplementationOnce(() => {
          return Promise.resolve({
            headers: {
              location: 'location from headers'
            }
          });
        });
        wrapper.vm.loading = true;
        const spyOnGetFormFieldsData = jest.fn();
        wrapper.vm.$getFormFieldsData = spyOnGetFormFieldsData;
        await wrapper.vm.submitForm();
        expect(wrapper.vm.loading).toEqual(false);
        expect(window.location.assign).toHaveBeenCalledWith('location from headers');
        expect(spyOnGetFormFieldsData).toHaveBeenCalled();
      });

      it('success case with company for the user', async() => {
        wrapper.vm.utilisateurId = 10;
        wrapper.vm.formFields.company = {
          name: 'test last name',
          id: 10
        };
        axios.post
        .mockImplementationOnce(() => {
          return Promise.resolve({
            headers: {
              location: 'location from headers'
            }
          });
        });
        wrapper.vm.loading = true;
        const spyOnGetFormFieldsData = jest.fn();
        wrapper.vm.$getFormFieldsData = spyOnGetFormFieldsData;
        await wrapper.vm.submitForm();
        expect(wrapper.vm.loading).toEqual(false);
        expect(window.location.assign).toHaveBeenCalledWith('location from headers');
        expect(spyOnGetFormFieldsData).toHaveBeenCalled();
      });

      it('error case 400', async() => {
        wrapper.vm.$handleFormError = jest.fn();
        const spyOnFormError = wrapper.vm.$handleFormError;
        axios.post.mockImplementationOnce(() => {
          return Promise.reject({
            response: {
              status: 400,
              data: {
              }
            }
          });
        });
        await wrapper.vm.submitForm();
        expect(wrapper.vm.loading).toBe(false);
        expect(spyOnFormError).toHaveBeenCalled();
      });

      it('other error cases', async() => {
        wrapper.vm.$handleError = jest.fn();
        const spyOnError = wrapper.vm.$handleError;
        axios.post.mockImplementationOnce(() => {
          return Promise.reject(new Error('test'));
        });
        await wrapper.vm.submitForm();
        expect(wrapper.vm.loading).toBe(false);
        expect(spyOnError).toHaveBeenCalledWith(new Error('test'));
      });
    });
    it('resetSousCategorys test', () => {
      wrapper.vm.formFields.sousCategorys = [
        {id: 1, name: 'sous category name'}
      ];
      wrapper.vm.resetSousCategorys();
      expect(wrapper.vm.formFields.sousCategorys).toEqual([]);
    });
  });
});
