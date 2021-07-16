import { shallow, createLocalVue } from 'vue-test-utils';
import axios from 'axios';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import CompanyListForm from './index';

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
    }});
  }),
  post: jest.fn(() => {
    return Promise.resolve({data: {
    }});
  })
}));

describe('Create or edit a company entity', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(CompanyListForm, {
      propsData: { companyId: 0 },
      i18n,
      localVue,
    });

    wrapper.vm.$refs.autocomplete.setValue = jest.fn();
    wrapper.vm.$refs.autocomplete.json = jest.fn();

  });

  it('initializes', () => {
    expect(wrapper.vm).toBeDefined();
  });
});
describe('watcher on', () => {
  it('fullDescription', async() => {
    wrapper.vm.currentFullDescription = 'test';
    wrapper.vm.formFields.descriptionFull = 'new\ntest';
    await wrapper.vm.$nextTick();
    expect(wrapper.vm.formFields.descriptionClean).toEqual('new test');

  });
});
describe('created test', () => {
  it('creates with companyUrlName', async() => {
    axios.get
    .mockImplementationOnce(() => Promise.resolve({data: [{
      id:1,
      name: 'name category test',
      code: 'TEST'
    }]}))
    .mockImplementationOnce(() => Promise.resolve({data: {
      id:2,
      description_full: 'description full test',
      image_profil: 'test image name',
      city: {name: 'city name test'},
      address: {longitude: 10, latitutde: 11},
      name: 'company name test',
      category: {id:1, name: 'name category test', code: 'TEST'}
    }}))
    .mockImplementationOnce(() => Promise.resolve({data: {
      id:3,
      name: 'name sousCategory test',
      code: 'TEST',
    }}));
    wrapper.vm.companyUrlName = 'url company name';
    wrapper.vm.loading = true;
    await CompanyListForm.created.bind(wrapper.vm)();
    expect(wrapper.vm.formFields.name).toEqual('company name test');
    expect(wrapper.vm.loading).toEqual(false);
  });

  describe('creates with id error case', () => {
    it('frist error', async() => {
      axios.get
      .mockImplementationOnce(() => Promise.resolve({data: [{
        id:1,
        name: 'name category test',
        code: 'TEST'
      }]}))
      .mockImplementationOnce(() => Promise.resolve({data: {
        id:2,
        description_full: 'description full test',
        image_profil: 'test image name',
        city: {name: 'city name test'},
        address: {longitude: 10, latitutde: 11},
        name: 'company name test',
        category: {id:1, name: 'name category test', code: 'TEST'}
      }}))
      .mockImplementationOnce(() => {
        return Promise.reject(new Error('created() test error'));
      });
      wrapper.vm.companyUrlName = 'url company name';
      wrapper.vm.loading = true;
      const spyOnError = jest.fn();
      wrapper.vm.$handleError = spyOnError;
      await CompanyListForm.created.bind(wrapper.vm)();
      expect(wrapper.vm.formFields.name).toEqual('company name test');
      expect(wrapper.vm.loading).toEqual(false);
      expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
    });
    it('second error', async() => {
      axios.get.mockImplementationOnce(() => {
        return Promise.reject(new Error('created() test error'));
      });
      const spyOnError = jest.fn();
      wrapper.vm.$handleError = spyOnError;
      await CompanyListForm.created.bind(wrapper.vm)();
      expect(wrapper.vm.loading).toEqual(false);
      expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
    });


  });
});

describe('methods test', () => {
  it('setCity test', () => {
    wrapper.vm.formFields.address = {postalAddress: 'postal address test', postalCode: 'postal code test'};
    const spyOnGetLongLat = jest.spyOn(wrapper.vm, 'getLongLat');
    let city = {name: 'test city name'};
    wrapper.vm.setCity(city);
    expect(spyOnGetLongLat).toHaveBeenCalled();
  });

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

  // it('onFileSelected test', () => {
  //   let file = new File(['(⌐□_□)'], 'sample.jpg', {type: 'image/png'});
  //   const imageInput = wrapper.vm.$.document.getElementsByName('imageProfil');
  //   wrapper.vm.onFileSelected();
  //   expect(wrapper.vm.urlImageProfil).toEqual(URL.createObjectURL(wrapper.vm.$refs.imageProfil.files[0]));
  // });

  describe('submitForm test', () => {
    it('during/before the request to API with first if true', () => {
      const spyOnPost = jest.spyOn(axios, 'post');
      wrapper.vm.position.lat = 1234;
      wrapper.vm.formFields.address.latitude = 5678;
      wrapper.vm.position.lng = 789;
      wrapper.vm.formFields.address.longitude = 123;
      wrapper.vm.companyUrlName = 1;
      // wrapper.vm.imageProfilSelected = true;
      wrapper.vm.submitForm();
      expect(wrapper.vm.loading).toEqual(true);
      expect(spyOnPost).toHaveBeenCalledWith('/api/companies/edit/1', wrapper.vm.$getFormFieldsData(wrapper.vm.formFields));
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
        axios.post.mockImplementationOnce(() => Promise.reject({
          response: {
            status: 400,
            data: { errors: { children: 'test error' } }
          }
        }));
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

});


