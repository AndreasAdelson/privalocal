import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import ManageModal from './manage-modal.vue';
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
  }),
  delete: jest.fn(() => {
    return Promise.resolve({data: {
    }});
  })
}));

describe('pending besoin component', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(ManageModal, {
      i18n,
      localVue,
      propsData: {
        id: '10',
        besoin: {
          id: 4,
          dt_created: '01/01/2021 10:00:34',
          title: 'title test',
          category: {name: 'test category name'},
          sous_categorys: [{name: 'test sous category name'}],
          description: 'description test',
          answers: [{company: {id:1, name: 'company name test'}}]
        },
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
    it('companys', () => {
      expect(wrapper.vm.companys).toEqual([
        {id: 1, name: 'company name test'}
      ]);
    });

    describe('status', () => {
      it('with companys === 0', () => {
        wrapper.vm.besoinStatuts = [
          {id: 1, name: 'besoins statut name 1'},
          {id: 2, name: 'besoins statut name 2'},
          {id: 3, name: 'besoins statut name 3'}
        ];
        wrapper.vm.besoin.answers = [];
        expect(wrapper.vm.status).toEqual([
          {id: 2, name: 'besoins statut name 2'},
          {id: 3, name: 'besoins statut name 3'}
        ]);
      });

      it('with companys > 0', () => {
        wrapper.vm.besoinStatuts = [
          {id: 1, name: 'besoins statut name 1'},
          {id: 2, name: 'besoins statut name 2'},
          {id: 3, name: 'besoins statut name 3'}
        ];
        wrapper.vm.besoin.answers = [
          {id: 1, name: 'company name test'}
        ];
        expect(wrapper.vm.status).toEqual([
          {id: 1, name: 'besoins statut name 1'},
          {id: 2, name: 'besoins statut name 2'},
        ]);
      });

    });

    it('companySelected', () => {
      wrapper.vm.formFields.companySelected = {id: 1, name: 'company name test'};
      expect(wrapper.vm.companySelected).toEqual({id: 1, name: 'company name test'});
    });
  });

  it('creates test', async() => {
    wrapper.vm.id = '10';
    const spyOnModal = jest.spyOn($('#10'), 'modal');
    await ManageModal.created.bind(wrapper.vm)();
    expect(spyOnModal).toHaveBeenCalledWith('handleUpdate');
  });

  it('mounted', async() => {
    wrapper.vm.formFields = {
      besoinStatut: {id: 1},
      companySelected: {id: 1},
      comment: {
        title: 'test title',
        message: 'test message',
        company: {id: 1},
        besoin: {id: 1},
        note: 4,
        authorCompany: {id: 1},
        utilisateur: {id: 1},
        _token: null
      },
      _token: null
    };
    await ManageModal.mounted.bind(wrapper.vm)();
    expect(wrapper.vm.formFields).toEqual(wrapper.vm.emptyFormFields);
  });

  describe('methods test', () => {
    describe('resetForm test', () => {
      it('beofre 150 ms', () => {
        const spyOnTimeout = jest.spyOn(global, 'setTimeout');
        const spyOnRemoveFormErrors = jest.fn();
        wrapper.vm.$removeFormErrors = spyOnRemoveFormErrors;
        const spyOnEmit = jest.fn();
        wrapper.vm.$emit = spyOnEmit;
        wrapper.vm.resetForm();
        expect(spyOnTimeout).toHaveBeenCalled();
        expect(spyOnRemoveFormErrors).not.toHaveBeenCalled();
        expect(spyOnEmit).not.toHaveBeenCalledWith('cancel-form');
      });

      it('after 150 ms', () => {
        const spyOnRemoveFormErrors = jest.fn();
        wrapper.vm.$removeFormErrors = spyOnRemoveFormErrors;
        const spyOnEmit = jest.fn();
        wrapper.vm.$emit = spyOnEmit;
        wrapper.vm.loading = true;
        wrapper.vm.formFields = {
          message: 'message test',
          besoin: {title: 'test title'},
          company: {name: 'test name'},
          _token: 'azefadas'
        };
        jest.useFakeTimers();
        wrapper.vm.resetForm();
        jest.runAllTimers();
        expect(wrapper.vm.formFields).toEqual(wrapper.vm.emptyFormFields);
        expect(spyOnRemoveFormErrors).toHaveBeenCalled();
        expect(spyOnEmit).toHaveBeenCalledWith('cancel-form');
      });
    });

    it('onCancelButton', () => {
      const spyOnResetForm = jest.spyOn(wrapper.vm, 'resetForm');
      wrapper.vm.onCancelButton();
      expect(spyOnResetForm).toHaveBeenCalled();
    });

    describe('archiveBesoin', () => {
      it('success case without formFields and companySelected && isCompany', async() => {
        const spyOnEmit = jest.spyOn(EventBus, '$emit');
        const spyOnModal= jest.spyOn($('#10'), 'modal');
        axios.post
        .mockImplementationOnce(() => {
          return Promise.resolve({
            headers: {location: 'location from header'}
          });
        });
        await wrapper.vm.archiveBesoin();
        expect(wrapper.vm.formFields.comment).toEqual({
          title: null,
          message: null,
          _token: null,
          note: null,
          besoin: null,
          utilisateur: null,
          company: null,
          authorCompany: null,
        });
        expect(spyOnModal).toHaveBeenCalledWith('hide');
        expect(spyOnEmit).toHaveBeenCalledWith('besoin-closed-end');

      });

      it('success case with formFields and companySelected && isCompany', async() => {
        wrapper.vm.formFields.comment.note = 4  ;
        wrapper.vm.besoin = {id: 1};
        wrapper.vm.utilisateurId = 2;
        wrapper.vm.formFields.companySelected = {id: 3};
        wrapper.vm.entitySelected = {id: 4};
        wrapper.vm.token = 'token test';
        wrapper.vm.isCompany = true;
        const spyOnEmit = jest.spyOn(EventBus, '$emit');
        const spyOnModal= jest.spyOn($('#10'), 'modal');
        axios.post
        .mockImplementationOnce(() => {
          return Promise.resolve({
            headers: {location: 'location from header'}
          });
        });
        await wrapper.vm.archiveBesoin();
        expect(wrapper.vm.formFields.comment).toEqual({
          title: null,
          message: null,
          _token: 'token test',
          note: 4,
          besoin: {id: 1},
          utilisateur: 2,
          company: {id: 3},
          authorCompany: {id: 4},
        });
        expect(spyOnModal).toHaveBeenCalledWith('hide');
        expect(spyOnEmit).toHaveBeenCalledWith('besoin-closed-end');

      });

      describe('error case', () => {
        it('server error (500) : logs the error’s message', async() => {
          axios.post.mockImplementationOnce(() => {
            return Promise.reject(new Error('erreur 500 test'));
          });
          const spy = jest.fn();
          wrapper.vm.$handleError = spy;
          await wrapper.vm.archiveBesoin();
          expect(wrapper.vm.loading).toEqual(false);
          expect(spy).toHaveBeenCalledWith(new Error('erreur 500 test'));
        });
        it('constraint validation violation error (400) with x-message', async() => {
          axios.post.mockImplementationOnce(() => Promise.reject({
            response: {
              status: 400,
              headers: {
                'x-message': 'error message in headers'
              },
              data: { errors: { children: 'test error' } }
            }
          }));
          await wrapper.vm.archiveBesoin();
          expect(wrapper.vm.loading).toEqual(false);
          expect(wrapper.vm.errorMessage).toEqual('error message in headers' );
        });

        it('constraint validation violation error (400) without x-message', async() => {
          const spyOnHandleFormError = jest.spyOn(wrapper.vm, '$handleFormError');
          axios.post.mockImplementationOnce(() => Promise.reject({
            response: {
              status: 400,
              headers: {
              },
              data: { errors: { children: 'test error' } }
            }
          }));
          await wrapper.vm.archiveBesoin();
          expect(wrapper.vm.loading).toEqual(false);
          expect(spyOnHandleFormError).toHaveBeenCalled();
        });
      });
    });

    describe('expiratedBesoin', () => {
      it('success case', async() => {
        wrapper.vm.besoin.id = 4;
        const spyOnEmit = jest.spyOn(EventBus, '$emit');
        const spyOnModal= jest.spyOn($('#10'), 'modal');
        wrapper.vm.loading = true;
        axios.post
        .mockImplementationOnce(() => {
          return Promise.resolve({
            headers: {location: 'location from headers'}
          });
        });
        await wrapper.vm.expiratedBesoin();
        expect(spyOnEmit).toHaveBeenCalledWith('besoin-closed-end');
        expect(spyOnModal).toHaveBeenCalledWith('hide');
        expect(wrapper.vm.loading).toEqual(false);

      });

      describe('error case', () => {
        it('server error (500) : logs the error’s message', async() => {
          axios.post.mockImplementationOnce(() => {
            return Promise.reject(new Error('erreur 500 test'));
          });
          const spy = jest.fn();
          wrapper.vm.$handleError = spy;
          await wrapper.vm.expiratedBesoin();
          expect(wrapper.vm.loading).toEqual(false);
          expect(spy).toHaveBeenCalledWith(new Error('erreur 500 test'));
        });
        it('constraint validation violation error (400) with x-message', async() => {
          axios.post.mockImplementationOnce(() => Promise.reject({
            response: {
              status: 400,
              headers: {
                'x-message': 'error message in headers'
              },
              data: { errors: { children: 'test error' } }
            }
          }));
          await wrapper.vm.expiratedBesoin();
          expect(wrapper.vm.loading).toEqual(false);
          expect(wrapper.vm.errorMessage).toEqual('error message in headers' );
        });

        it('constraint validation violation error (400) without x-message', async() => {
          const spyOnHandleFormError = jest.spyOn(wrapper.vm, '$handleFormError');
          axios.post.mockImplementationOnce(() => Promise.reject({
            response: {
              status: 400,
              headers: {
              },
              data: { errors: { children: 'test error expirated' } }
            }
          }));
          await wrapper.vm.expiratedBesoin();
          expect(wrapper.vm.loading).toEqual(false);
          expect(spyOnHandleFormError).toHaveBeenCalled();
        });
      });
    });

    describe('deleteBesoin', () => {
      it('success case', async() => {
        wrapper.vm.besoin.id = 4;
        wrapper.vm.loading = true;
        axios.delete.mockImplementationOnce(() => Promise.resolve({
          headers: { location: 'assign url' }
        }));
        const spyOnEmit = jest.spyOn(EventBus, '$emit');
        const spyOnModal= jest.spyOn($('#10'), 'modal');
        await wrapper.vm.deleteBesoin();
        expect(spyOnModal).toHaveBeenCalledWith('hide');
        expect(spyOnEmit).toHaveBeenCalledWith('besoin-deleted');
        expect(wrapper.vm.loading).toEqual(false);
      });

      it('request error', async() => {
        wrapper.vm.besoin.id = 4;
        axios.delete.mockImplementationOnce(() => {
          return Promise.reject(new Error('erreur 500 test'));
        });
        const spyOnHandleError = jest.fn();
        wrapper.vm.$handleError = spyOnHandleError;
        await wrapper.vm.deleteBesoin();
        expect(spyOnHandleError).toHaveBeenCalledWith(new Error('erreur 500 test'));
      });
    });

    describe('submitForm', () => {
      it('with code == ARC', () => {
        wrapper.vm.token = 'test token';
        wrapper.vm.formFields.besoinStatut = {
          code: 'ARC'
        };
        const spyOnRemoveFormErrors = jest.spyOn(wrapper.vm, '$removeFormErrors');
        const spyOnArchiveBesoin = jest.spyOn(wrapper.vm, 'archiveBesoin');
        const spyOnDeleteBesoin = jest.spyOn(wrapper.vm, 'deleteBesoin');
        const spyOnExpiratedBesoin = jest.spyOn(wrapper.vm, 'expiratedBesoin');
        wrapper.vm.submitForm();
        expect(spyOnRemoveFormErrors).toHaveBeenCalled();
        expect(spyOnArchiveBesoin).toHaveBeenCalled();
        expect(spyOnDeleteBesoin).not.toHaveBeenCalled();
        expect(spyOnExpiratedBesoin).not.toHaveBeenCalled();
      });

      it('with code == ERR', () => {
        wrapper.vm.token = 'test token';
        wrapper.vm.formFields.besoinStatut = {
          code: 'ERR'
        };
        const spyOnRemoveFormErrors = jest.spyOn(wrapper.vm, '$removeFormErrors');
        const spyOnArchiveBesoin = jest.spyOn(wrapper.vm, 'archiveBesoin');
        const spyOnDeleteBesoin = jest.spyOn(wrapper.vm, 'deleteBesoin');
        const spyOnExpiratedBesoin = jest.spyOn(wrapper.vm, 'expiratedBesoin');
        wrapper.vm.submitForm();
        expect(spyOnRemoveFormErrors).toHaveBeenCalled();
        expect(spyOnArchiveBesoin).not.toHaveBeenCalled();
        expect(spyOnDeleteBesoin).toHaveBeenCalled();
        expect(spyOnExpiratedBesoin).not.toHaveBeenCalled();
      });

      it('with code == EXP', () => {
        wrapper.vm.token = 'test token';
        wrapper.vm.formFields.besoinStatut = {
          code: 'EXP'
        };
        const spyOnRemoveFormErrors = jest.spyOn(wrapper.vm, '$removeFormErrors');
        const spyOnArchiveBesoin = jest.spyOn(wrapper.vm, 'archiveBesoin');
        const spyOnDeleteBesoin = jest.spyOn(wrapper.vm, 'deleteBesoin');
        const spyOnExpiratedBesoin = jest.spyOn(wrapper.vm, 'expiratedBesoin');
        wrapper.vm.submitForm();
        expect(spyOnRemoveFormErrors).toHaveBeenCalled();
        expect(spyOnArchiveBesoin).not.toHaveBeenCalled();
        expect(spyOnDeleteBesoin).not.toHaveBeenCalled();
        expect(spyOnExpiratedBesoin).toHaveBeenCalled();
      });
    });
  });
});
