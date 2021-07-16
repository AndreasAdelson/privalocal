import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import CompanyShow from './index.vue';
import axios from 'axios';

const localVue = createLocalVue();
localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);

let wrapper;

jest.mock('axios', () => ({
  get: jest.fn(() => {
    return Promise.resolve({data: {
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
    }});
  })
}));

describe('new entity card component', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(CompanyShow, {
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
    it('creates', async() => {
      const spyOnSetPageByUrlParameter = jest.spyOn(wrapper.vm, 'setPageByUrlParameter');
      window.location = {
        search: '?page=comments'
      };
      axios.get
      .mockImplementationOnce(() => Promise.resolve({data: {
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
      }}));
      wrapper.vm.loading = true;
      await CompanyShow.created.bind(wrapper.vm)();
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
      expect(spyOnSetPageByUrlParameter).toHaveBeenCalled();

    });

    it('creates error case', async() => {
      axios.get.mockImplementationOnce(() => {
        return Promise.reject(new Error('created() test error'));
      });
      const spyOnError = jest.fn();
      wrapper.vm.$handleError = spyOnError;
      await CompanyShow.created.bind(wrapper.vm)();
      expect(wrapper.vm.loading).toEqual(false);
      expect(spyOnError).toHaveBeenCalledWith(new Error('created() test error'));
    });
  });

  describe('methods test', () => {
    it('activePresentation test', () => {
      wrapper.vm.activePresentation();
      expect(wrapper.vm.presentationActive).toEqual(true);
      expect(wrapper.vm.jobOfferActive).toEqual(false);
      expect(wrapper.vm.commentActive).toEqual(false);
    });

    it('activeJobOffer test', () => {
      wrapper.vm.activeJobOffer();
      expect(wrapper.vm.presentationActive).toEqual(false);
      expect(wrapper.vm.jobOfferActive).toEqual(true);
      expect(wrapper.vm.commentActive).toEqual(false);
    });

    it('activeComment test', () => {
      wrapper.vm.activeComment();
      expect(wrapper.vm.presentationActive).toEqual(false);
      expect(wrapper.vm.jobOfferActive).toEqual(false);
      expect(wrapper.vm.commentActive).toEqual(true);
    });

    it('round test', () => {
      expect(wrapper.vm.round(4.1234)).toEqual(4.1);
    });

    describe('setPageByUrlParameter test', () => {
      it('with page = comments', () => {
        window.location = {
          search: '?page=comments'
        };
        const spyOnGetUrlParameter = jest.spyOn(wrapper.vm, 'getUrlParameter');
        const spyOnActiveComment = jest.spyOn(wrapper.vm, 'activeComment');
        const spyOnActiveJobOffer = jest.spyOn(wrapper.vm, 'activeJobOffer');
        const spyOnActivePresentation = jest.spyOn(wrapper.vm, 'activePresentation');
        wrapper.vm.setPageByUrlParameter();
        expect(spyOnGetUrlParameter).toHaveBeenCalledWith('page');
        expect(spyOnActiveComment).toHaveBeenCalled();
        expect(spyOnActiveJobOffer).not.toHaveBeenCalledWith();
        expect(spyOnActivePresentation).not.toHaveBeenCalledWith();

      });

      it('with page = presentation', () => {
        window.location = {
          search: '?page=presentation'
        };
        const spyOnGetUrlParameter = jest.spyOn(wrapper.vm, 'getUrlParameter');
        const spyOnActiveComment = jest.spyOn(wrapper.vm, 'activeComment');
        const spyOnActiveJobOffer = jest.spyOn(wrapper.vm, 'activeJobOffer');
        const spyOnActivePresentation = jest.spyOn(wrapper.vm, 'activePresentation');
        wrapper.vm.setPageByUrlParameter();
        expect(spyOnGetUrlParameter).toHaveBeenCalledWith('page');
        expect(spyOnActiveComment).not.toHaveBeenCalled();
        expect(spyOnActiveJobOffer).not.toHaveBeenCalledWith();
        expect(spyOnActivePresentation).toHaveBeenCalledWith();

      });

      it('with page = joboffer', () => {
        window.location = {
          search: '?page=joboffer'
        };
        const spyOnGetUrlParameter = jest.spyOn(wrapper.vm, 'getUrlParameter');
        const spyOnActiveComment = jest.spyOn(wrapper.vm, 'activeComment');
        const spyOnActiveJobOffer = jest.spyOn(wrapper.vm, 'activeJobOffer');
        const spyOnActivePresentation = jest.spyOn(wrapper.vm, 'activePresentation');
        wrapper.vm.setPageByUrlParameter();
        expect(spyOnGetUrlParameter).toHaveBeenCalledWith('page');
        expect(spyOnActiveComment).not.toHaveBeenCalled();
        expect(spyOnActiveJobOffer).toHaveBeenCalledWith();
        expect(spyOnActivePresentation).not.toHaveBeenCalledWith();

      });
    });

  });
});
