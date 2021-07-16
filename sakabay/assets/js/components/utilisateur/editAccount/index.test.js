import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import EditAccount from './index.vue';
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
    wrapper = shallow(EditAccount, {
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

  describe('creates test', () => {
    it('with utilisateurId', async() => {
      const spyOnSetEditForm = jest.spyOn(wrapper.vm, '$setEditForm');
      axios.get
      .mockImplementationOnce(() => {
        return Promise.resolve({data: {id: 1, image_profil: 'image profil test', username: 'username test'}
        });
      });
      wrapper.vm.utilisateurId = 10;
      wrapper.vm.loading = true;
      await EditAccount.created.bind(wrapper.vm)();
      expect(spyOnSetEditForm).toHaveBeenCalled();
      expect(wrapper.vm.utilisateur).toEqual({id: 1, image_profil: 'image profil test', username: 'username test'});
      expect(wrapper.vm.urlImageProfil).toEqual('/build/images/uploads/image profil test');
      expect(wrapper.vm.loading).toEqual(false);
    });

    it('error case with utilisateurId', async() => {
      wrapper.vm.utilisateurId = 10;
      axios.get
      .mockImplementationOnce(() => Promise.reject(new Error('error test')));
      const spyOnHandleError = jest.fn();
      wrapper.vm.$handleError = spyOnHandleError;
      wrapper.vm.loading = true;
      await EditAccount.created.bind(wrapper.vm)();
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

    it('goBack test', () => {
      wrapper.vm.urlPrecedente = '/sakabay';
      const spyOGoTo = jest.spyOn(wrapper.vm, '$goTo');
      wrapper.vm.goBack();
      expect(spyOGoTo).toHaveBeenCalled();
    });

    describe('changePassword', () => {
      it('passwordActive == true', () => {
        wrapper.vm.passwordActive = true;
        wrapper.vm.changePassword();
        expect(wrapper.vm.passwordActive).toEqual(false);
      });

      it('passwordActive == false', () => {
        wrapper.vm.passwordActive = false;
        wrapper.vm.changePassword();
        expect(wrapper.vm.passwordActive).toEqual(true);
      });
    });
  });
});
