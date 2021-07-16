
import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import axios from 'axios';
import cnsFormUtils from './cnsFormUtils';

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
localVue.use(cnsFormUtils);
localVue.use(VueLogger, loggerOptions);

const i18n = new VueI18n({
  locale: 'fr',
  fallbackLocale: 'fr',
  messages: {
    'en': require('i18n/en.json'),
    'fr': require('i18n/fr.json')
  }
});

jest.mock('axios', () => ({
  get: jest.fn(() => {
    return Promise.resolve({});
  }),
}));

const wrapper = shallow({}, {
  i18n, localVue,
  propsData: {
    formFields: {id: 1}
  }
});

describe('cnsFormUtils', () => {

  it('$removeFieldsNotInForm', () => {
    const object = { id: 0, a: 1, b: 2, c: 3, d: 4 };
    const expectedObject = { a: 1, c: 3 };
    wrapper.vm.$removeFieldsNotInForm(object, ['a', 'c']);
    expect(object).toEqual(expectedObject);
  });

  describe('$handleFormError(errorData) print all errors for each field in the given errorData', () => {
    it('all form fields', () => {
      document.body.innerHTML = '<fieldset class="a"><textarea/><fieldset>';
      const errorData = { errors: { children: {} } };
      const error = new Error('Error test');
      error.response = {
        status: 400,
        data: { errors: { children: {} } },
      };
      errorData.errors.children['a'] = { errors: ['Test form error'] };
      errorData.errors.children['b'] = {};
      const spyOnAddErrorsOfField = jest.spyOn(wrapper.vm, '$addErrorInFormError');
      wrapper.vm.$handleFormError(errorData);
      expect(spyOnAddErrorsOfField).toHaveBeenCalledWith('a', ['Test form error']);
    });
  });

  it('$removeFormErrors', () => {
    const spyOnRemoveFieldErrors = jest.spyOn(wrapper.vm, '$removeFieldErrors');
    wrapper.vm.formErrors = {
      test: ['error'],
    };
    wrapper.vm.$removeFormErrors();
    Object.keys(wrapper.vm.formErrors).forEach(field => {
      expect(spyOnRemoveFieldErrors).toHaveBeenCalledWith(field);
    });
  });

  it('$toggleModal', () => {
    global.spyOnJQueryModal.mockClear();
    wrapper.vm.$toggleModal('test');
    expect(global.spyOnJQueryModal).toHaveBeenCalledWith('toggle');
  });

  it('$transformDateFields(object)', () => {
    const object = {
      ar: [ { dt: '01/01/2001' }, 2 ],
      dt: '29/11/1995',
      dt2: null,
      autre: 'autre',
      obj: { dt: '01/01/2001', nb: 1 },
    };
    wrapper.vm.$transformDateFields(object);
    expect(object.ar[0].dt).toEqual(new Date('01/01/2001'));
    expect(object.ar[1]).toEqual(2);
    expect(object.dt).toEqual(new Date('11/29/1995'));
    expect(object.dt2).toEqual(null);
    expect(object.autre).toEqual('autre');
    expect(object.obj.dt).toEqual(new Date('01/01/2001'));
    expect(object.obj.nb).toEqual(1);
  });

  it('$onDeleteEntity(indexOfDeletedItem, fieldName, formFields)', () => {
    let myFormFields = {
      jalonsEtude: {
        0: { lbNom: 'a' },
        2: { lbNom: 'b' },
        3: { lbNom: 'c' },
      }
    };
    wrapper.vm.$onDeleteEntity(2, 'jalonsEtude', myFormFields);
    expect(myFormFields.jalonsEtude).toEqual({
      0: { lbNom: 'a' },
      3: { lbNom: 'c' },
    });
  });

  it('$onAfterCreateEntity(eventArguments, fieldName, formFields)', () => {
    let formFields = { jalonsEtude: {} };
    wrapper.vm.$onAfterCreateEntity({ 0: { lbNom: 'a' } }, 'jalonsEtude', formFields);
    expect(formFields.jalonsEtude).toEqual({
      0: { lbNom: 'a' },
    });
    wrapper.vm.$onAfterCreateEntity({ 0: { lbNom: 'b' } }, 'jalonsEtude', formFields);
    expect(formFields.jalonsEtude).toEqual({
      0: { lbNom: 'a' },
      1: { lbNom: 'b' },
    });
  });

  describe('$removeFieldErrors(field)', () => {
    describe('progresTechnos form', () => {
      document.body.innerHTML = '<fieldset class="pl"/>';
      wrapper.vm.includedFormsFields = {
        'pl': []
      };
      wrapper.vm.formErrors = {
        progresTechnos: {
          0: {
            pl: 'op the error'
          }
        }
      };
      wrapper.vm.formFields = {
        progresTechnos: {
          0: {
            pl: 'op the field'
          }
        }
      };
      wrapper.vm.$removeFieldErrors('progresTechnos');
      expect(wrapper.vm.formErrors.progresTechnos).toEqual([]);
    });

    describe('other forms', () => {
      it('with valid field', () => {
        document.body.innerHTML = '<fieldset class="sb_lbCommentaire"><textarea></textarea></fieldset>';
        wrapper.vm.formErrors = { sb_lbCommentaire: ['a'] };
        const formElement = document.getElementsByClassName('sb_lbCommentaire')[0].getElementsByTagName('textarea')[0];
        const spyOnClassListRemove = jest.spyOn(formElement.classList, 'remove');
        wrapper.vm.$removeFieldErrors('sb_lbCommentaire');
        expect(wrapper.vm.formErrors.sb_lbCommentaire).toEqual([]);
        expect(spyOnClassListRemove).toHaveBeenCalledWith('border-danger-skb');
      });

      it('with given index (budgets field)', () => {
        document.body.innerHTML = '<fieldset class="budgets"><textarea></textarea></fieldset>';
        wrapper.vm.formErrors = {
          budgets: {
            0: undefined,
            1: ['a'],
          }
        };
        wrapper.vm.$removeFieldErrors('budgets.1');
        expect(wrapper.vm.formErrors.budgets[1]).toEqual([]);
      });

      it('with field who has not errors area in front', next => {
        wrapper.vm.formErrors['test'] = [];
        wrapper.vm.$removeFieldErrors('test');
        next();
      });
    });
  });

  describe('$validateForm()', () => {
    it('validate fails (error case)', async() => {
      wrapper.vm.$validator = { validate: jest.fn() };
      wrapper.vm.$validator.validate.mockImplementationOnce(() => {
        return Promise.reject(new Error('testes'));
      });
      const spyOnError = jest.fn();
      wrapper.vm.$handleError = spyOnError;
      await wrapper.vm.$validateForm();
      expect(spyOnError).toHaveBeenCalledWith(new Error('testes'));
    });

    it('without error', async() => {
      wrapper.vm.$validator = { validate: jest.fn() };
      const spyOnSubmitForm = jest.fn();
      wrapper.vm.submitForm = spyOnSubmitForm;
      wrapper.vm.$validator.validate.mockImplementationOnce(() => {
        return Promise.resolve(true);
      });
      await wrapper.vm.$validateForm();
      expect(spyOnSubmitForm).toHaveBeenCalled();
    });

    it('with errors', async() => {
      const spyOnClear = jest.fn();
      wrapper.vm.$validator = {
        validate: jest.fn(),
        errors: {
          items: [
            { field: 'company', msg: 'should not be used 2', rule: 'required' },
          ],
        },
      };
      wrapper.vm.formErrors = {
        company: [],
      };
      wrapper.vm.$validator.validate.mockImplementationOnce(() => {
        return Promise.resolve(false);
      });
      await wrapper.vm.$validateForm();
      expect(wrapper.vm.formErrors.company).toEqual([wrapper.vm.$t('error_message_required')]);
    });

    // it('with errors, newLocationHash given', async() => {
    //   wrapper.vm.$validator = {
    //     validate: jest.fn(),
    //     errors: {
    //       items: [
    //         { field: 'a', msg: 'field is required.', rule: 'required' },
    //       ],
    //       clear: jest.fn(),
    //     },
    //   };
    //   wrapper.vm.formErrors = { a: [] };
    //   wrapper.vm.$validator.validate.mockImplementationOnce(() => {
    //     return Promise.resolve(false);
    //   });
    //   await wrapper.vm.$validateForm('#plop');
    //   expect(window.location.hash).toEqual('#plop');
    // });
  });



  describe('$addErrorInFormError(field, errors)', () => {
    it('on textarea', () => {
      document.body.innerHTML = '<fieldset class="a"><textarea/><input/><fieldset>';
      wrapper.vm.formErrors = { a: [] };
      const element = document.getElementsByClassName('a')[0].getElementsByTagName('textarea')[0];
      const spyOnElementClassListAdd = jest.spyOn(element.classList, 'add');
      wrapper.vm.$addErrorInFormError('a', ['Test form error']);
      expect(wrapper.vm.formErrors.a).toEqual(['Test form error']);
      expect(spyOnElementClassListAdd).toHaveBeenCalledWith('border-danger-skb');
    });

    it('on neither textarea, multiselect__tags or input', () => {
      document.body.innerHTML = '<fieldset class="b"><plop/><fieldset>';
      wrapper.vm.formErrors = { b: [] };
      const element = document.getElementsByClassName('b')[0].getElementsByTagName('plop')[0];
      const spyOnElementClassListAdd = jest.spyOn(element.classList, 'add');
      wrapper.vm.$addErrorInFormError('b', ['Test form error']);
      expect(wrapper.vm.formErrors.b).toEqual(['Test form error']);
      expect(spyOnElementClassListAdd).not.toHaveBeenCalledWith('border-danger-skb');
    });
  });

  it('$onSelectedItemsChange(fieldName, selectedItems)', () => {
    wrapper.vm.formFields = { etudesAssociees: [] };
    const eventArguments = {
      0: [1, 2, 3, 4, 5],
      a: 'o',
    };
    wrapper.vm.$onSelectedItemsChange(eventArguments, 'etudesAssociees');
    expect(wrapper.vm.formFields.etudesAssociees).toEqual([1, 2, 3, 4, 5]);
  });

  it('$goTo', () => {
    wrapper.vm.$goTo('plop');
    expect(window.location.assign).toHaveBeenCalledWith('plop');
  });

  it('$requestAndSet(queries)', async() => {
    axios.get
      .mockImplementationOnce(() => Promise.resolve({ data: 'plop res' }))
      .mockImplementationOnce(() => Promise.resolve({ data: 'haaa res' }));
    wrapper.vm.plop = [];
    wrapper.vm.ha = { aa: [] };
    const spy = jest.spyOn(axios, 'get');
    await wrapper.vm.$requestAndSet([
      { path: 'plop', url: '/api/plop' },
      { path: 'ha.aa', url: '/api/test', params: { id_entity: 69 } },
    ]);
    expect(spy).toHaveBeenCalledWith('/api/plop', { params: undefined });
    expect(spy).toHaveBeenCalledWith('/api/test', { params: { id_entity: 69 } });
    expect(wrapper.vm.plop).toEqual('plop res');
    expect(wrapper.vm.ha.aa).toEqual('haaa res');
  });

  describe('$getTransformedValue(value, key)', () => {
    describe('primitive value', () => {
      it('null, false, undefined, \'\' returns undefined', () => {
        expect(wrapper.vm.$getTransformedValue(null)).toEqual(undefined);
        expect(wrapper.vm.$getTransformedValue(false)).toEqual(undefined);
        expect(wrapper.vm.$getTransformedValue(undefined)).toEqual(undefined);
        expect(wrapper.vm.$getTransformedValue('')).toEqual(undefined);
      });

      it('numbers', () => {
        expect(wrapper.vm.$getTransformedValue(0)).toEqual(0);
        expect(wrapper.vm.$getTransformedValue(1)).toEqual(1);
        expect(wrapper.vm.$getTransformedValue(-99.3)).toEqual(-99.3);
      });

      it('string', () => {
        expect(wrapper.vm.$getTransformedValue('str')).toEqual('str');
      });

      it('boolean true', () => {
        expect(wrapper.vm.$getTransformedValue(true)).toEqual(true);
      });
    });

    it('date', () => {
      expect(wrapper.vm.$getTransformedValue(new Date(2000, 0, 2))).toEqual('2000/01/02 00:00:00');
    });

    describe('object', () => {
      it('empty', () => {
        expect(wrapper.vm.$getTransformedValue(new Object())).toEqual(undefined);
      });

      it('with id', () => {
        expect(wrapper.vm.$getTransformedValue({
          id: 12, a: '', b: null, c: undefined,
          d: false, e: new Object(), f: new Array(),
          g: 'string', h: 0, i: 1
        })).toEqual(12);
      });

      it('common object', () => {
        const value = { a: 'plop', b: null, refEntity: {} };
        const expectedTransform = { a: 'plop' };
        expect(wrapper.vm.$getTransformedValue(value)).toEqual(expectedTransform);
      });
    });

    describe('array', () => {
      it('of primitive values', () => {
        const value = [ 1, null, true, false, 'string' ];
        const expectedTransform = [ 1, true, 'string' ];
        expect(wrapper.vm.$getTransformedValue(value)).toEqual(expectedTransform);
      });

      it('of objects with ids', () => {
        const value = [ { id: 1 }, { id: 2 }, { id: 3 } ];
        const expectedTransform = [ 1, 2, 3 ];
        expect(wrapper.vm.$getTransformedValue(value)).toEqual(expectedTransform);
      });

      it('of objects whichout ids', () => {
        wrapper.vm.includedFormsFields = { inc: '' };
        const value = [
          { a: 1 },
          { b: 2 },
        ];
        const expectedTransform = [
          { a: 1 },
          { b: 2 },
        ];
        expect(wrapper.vm.$getTransformedValue(value)).toEqual(expectedTransform);
      });
    });
  });

  // it('$getFormFieldsData(formFields, formData)', () => {
  //   const formFields = {
  //     a: '',
  //     b: null,
  //     c: undefined,
  //     d: false,
  //     e: new Object(),
  //     f: new Array(),
  //     g: 'string',
  //     h: 0,
  //     i: 1,
  //     j: true,
  //     k: { id: 1 },
  //     l: [
  //       { id: 1 },
  //       { id: 2 },
  //     ],
  //     m: new Date(2000, 0, 2),
  //     n: [
  //       {
  //         m0a: new Date(2000, 0, 2),
  //         m0b: false,
  //         m0c: [ { id: 1 }, { id: 2 } ],
  //         m0d: 'mstring',
  //         m0e: { id: 1 },
  //       }, {
  //         m1a: new Date(2000, 0, 2),
  //         m1b: new Object(),
  //         m1c: [ { id: 1 }, { id: 2 } ],
  //         m1d: 'mstring',
  //         m1e: { id: 1 },
  //       },
  //     ],
  //     o: {
  //       0: {
  //         n0a: new Date(2000, 0, 2),
  //         n0b: new Array(),
  //         n0c: [ { id: 1 }, { id: 2 } ],
  //         n0d: 'mstring',
  //         n0e: { id: 1 },
  //       },
  //       1: {
  //         n1a: new Date(2000, 0, 2),
  //         n1b: '',
  //         n1c: [ { id: 1 }, { id: 2 } ],
  //         n1d: 'mstring',
  //         n1e: { id: 1 },
  //       },
  //     },
  //   };

  //   const expectedFormData = new FormData();
  //   expectedFormData.append('g', 'string');
  //   expectedFormData.append('h', 0);
  //   expectedFormData.append('i', 1);
  //   expectedFormData.append('j', true);
  //   expectedFormData.append('k', 1);
  //   expectedFormData.append('l[]', 1);
  //   expectedFormData.append('l[]', 2);
  //   expectedFormData.append('m[day]', 2);
  //   expectedFormData.append('m[month]', 1);
  //   expectedFormData.append('m[year]', 2000);
  //   expectedFormData.append('n', JSON.stringify([
  //     {
  //       m0a: {
  //         day: 2,
  //         month: 1,
  //         year: 2000,
  //       },
  //       m0c: [ 1, 2 ],
  //       m0d: 'mstring',
  //       m0e: 1,
  //     }, {
  //       m1a: {
  //         day: 2,
  //         month: 1,
  //         year: 2000,
  //       },
  //       m1c: [ 1, 2 ],
  //       m1d: 'mstring',
  //       m1e: 1,
  //     },
  //   ]));
  //   expectedFormData.append('o', JSON.stringify({
  //     0: {
  //       n0a: {
  //         day: 2,
  //         month: 1,
  //         year: 2000,
  //       },
  //       n0c: [ 1, 2 ],
  //       n0d: 'mstring',
  //       n0e: 1,
  //     },
  //     1: {
  //       n1a: {
  //         day: 2,
  //         month: 1,
  //         year: 2000,
  //       },
  //       n1c: [ 1, 2 ],
  //       n1d: 'mstring',
  //       n1e: 1,
  //     },
  //   }));
  //   expect(wrapper.vm.$getFormFieldsData(formFields)).toEqual(expectedFormData);
  // });
});
