import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import Dashboard from './index.vue';
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
  })
}));

describe('dashboard component', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(Dashboard, {
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
  describe('computed test', () => {
    describe('numberUnseen test', () => {
      it('with seen == false', () => {
        wrapper.vm.notifications = [{seen: false}];
        expect(wrapper.vm.numberUnseen).toEqual(1);
      });

      it('with seen == true', () => {
        wrapper.vm.notifications = [{seen: true}];
        expect(wrapper.vm.numberUnseen).toEqual(0);
      });
    });

    it('companySubscription test', () => {
      wrapper.vm.companies = [
        {
          company_subscriptions: [
            {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
            {dt_debut: '01/01/2021', dt_fin: '03/09/2221 10:00:34'}
          ],
          name: 'Sakabay',
          company_statut: {
            code: 'TEST'
          },
        }
      ];
      expect(wrapper.vm.companySubscriptions).toEqual(
        [
          {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34', company_name: 'Sakabay'},
          {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34', company_name: 'Sakabay'},
          {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34', company_name: 'Sakabay'},
          {dt_debut: '01/01/2021', dt_fin: '03/09/2221 10:00:34', company_name: 'Sakabay'}
        ]
      );
    });

    describe('hasOneValidated', () => {
      it('with code == TEST', () => {
        wrapper.vm.companies = [
          {
            company_statut: {
              code: 'TEST'
            },
            name: 'Sakabay'
          }
        ];
        expect(wrapper.vm.hasOneValidated).toEqual(false);
      });

      it('with code == VAL', () => {
        wrapper.vm.companies = [
          {
            company_statut: {
              code: 'VAL'
            },
            name: 'Sakabay',
            company_subscriptions: [
              {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '03/09/2221 10:00:34'}
            ]
          }
        ];
        expect(wrapper.vm.hasOneValidated).toEqual(true);
      });

    });
  });

  describe('created test', () => {
    it('creates', async() => {
      const spyGetUser = jest.spyOn(wrapper.vm, 'getUser');
      const spyOnGetNotification = jest.spyOn(wrapper.vm, 'getNotifications');
      axios.get
        .mockImplementationOnce(() => {
          return Promise.resolve({
            data: {
              id: 1,
              username: 'username test',
              last_name: 'last name test',
              first_name: 'first name test',
              email: 'email test',
              companys: [
                {
                  id: 1,
                  company_statut: {code: 'VAL'},
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
                }
              ]
            },
          });
        })
        .mockImplementationOnce(() => {
          return Promise.resolve({
            data: [
              {
              id: 2,
              message: 'message notification',
              link: '/sakabay'
              }
            ]
          });
        });
      await Dashboard.created.bind(wrapper.vm)();
      expect(wrapper.vm.notifications).toEqual([{
        id: 2,
        message: 'message notification',
        link: '/sakabay'
      }]);
      expect(wrapper.vm.utilisateur).toEqual({
        id: 1,
        username: 'username test',
        last_name: 'last name test',
        first_name: 'first name test',
        email: 'email test',
        companys: [
          {
            id: 1,
            company_statut: {code: 'VAL'},
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
          }
      ]
      });
      expect(wrapper.vm.companies).toEqual([{
        id: 1,
        company_statut: {code: 'VAL'},
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
      }]);
      expect(spyGetUser).toHaveBeenCalled();
      expect(spyOnGetNotification).toHaveBeenCalled();
    });
  });

  describe('methods test', () => {
    describe('getNotifications test', () => {
      it('success case', async() => {
        axios.get
        .mockImplementationOnce(() => {
          return Promise.resolve({
            data: [
              {
              id: 2,
              message: 'message notification',
              link: '/sakabay'
              }
            ]
          });
        });
        await wrapper.vm.getNotifications();
        expect(wrapper.vm.notifications).toEqual([
          {
          id: 2,
          message: 'message notification',
          link: '/sakabay'
          }
        ]);
      });

      it('error case', async() => {
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        const error = new Error('Error test');
        axios.get
        .mockImplementationOnce(() => {
          error.response = {
            status: 400,
            data: { errors: { children: {} } },
          };
          return Promise.reject(error);
        });
        await wrapper.vm.getNotifications();
        expect(spyOnError).toHaveBeenCalledWith(new Error('Error test'));
      });
    });

    it('onNotificationSeen test', () => {
      const spy = jest.fn();
      wrapper.vm.$forceUpdate = spy;
      wrapper.vm.notifications = [{seen: false}];
      wrapper.vm.onNotificationSeen(0);
      expect(wrapper.vm.notifications[0].seen).toEqual(true);
      expect(spy).toHaveBeenCalled();
    });

    it('onNotificationUnSeen test', () => {
      wrapper.vm.notifications = [{seen: true}];
      wrapper.vm.onNotificationUnSeen(0);
      expect(wrapper.vm.notifications[0].seen).toEqual(false);
    });

    it('onNotificationDeleted test', () => {
      wrapper.vm.notifications = [{seen: true}];
      wrapper.vm.onNotificationDeleted(0);
      expect(wrapper.vm.notifications).toEqual([]);
    });

    it('sortNotifications test', () => {
      wrapper.vm.notifications = [{seen: true,notification: {date: '05/01/2021'}}, {seen: false, notification: {date: '01/01/2021'}}];
      wrapper.vm.sortNotifications();
      expect(wrapper.vm.notifications).toEqual([{seen: true, notification: {date: '05/01/2021'}}, {seen: false, notification: {date: '01/01/2021'}}]);
    });

    it('onNotificationMarked test', () => {
      wrapper.vm.notifications = [{seen: false}];
      wrapper.vm.onNotificationMarked(0);
      expect(wrapper.vm.notifications[0].seen).toEqual(true);
    });

    it('activeNotification test', () => {
      wrapper.vm.notificationActive = false;
      wrapper.vm.historyActive = false;
      wrapper.vm.activeNotification();
      expect(wrapper.vm.notificationActive).toEqual(true);
      expect(wrapper.vm.historyActive).toEqual(false);
    });

    it('activeHistory test', () => {
      wrapper.vm.notificationActive = false;
      wrapper.vm.historyActive = false;
      wrapper.vm.activeHistory();
      expect(wrapper.vm.notificationActive).toEqual(false);
      expect(wrapper.vm.historyActive).toEqual(true);
    });

    describe('getSubscribedCompany test', () => {
      it('with an active subscription', () => {
        wrapper.vm.companies = [
          {
            company_statut: {
              code: 'VAL'
            },
            name: 'Sakabay',
            company_subscriptions: [
              {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '03/09/2221 10:00:34'}
            ]
          }
        ];
        wrapper.vm.getSubscribedCompany();
        expect(wrapper.vm.hasOneSubscribed).toEqual(true);
      });

      it('without an active subscription', () => {
        wrapper.vm.companies = [
          {
            company_statut: {
              code: 'VAL'
            },
            name: 'Sakabay',
            company_subscriptions: [
              {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34'},
              {dt_debut: '01/01/2021', dt_fin: '03/09/2021 10:00:34'}
            ]
          }
        ];
        wrapper.vm.getSubscribedCompany();
        expect(wrapper.vm.hasOneSubscribed).toEqual(false);
      });
    });

    describe('getUser test', () => {
      it('success case', async() => {
        axios.get
        .mockImplementationOnce(() => {
          return Promise.resolve({
            data:
            {
              id: 1,
              username: 'username test',
              last_name: 'last name test',
              first_name: 'first name test',
              email: 'email test',
              companys: [
                {
                  id: 1,
                  company_statut: {code: 'VAL'},
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
                }
              ]
            }
          });
        });
        await wrapper.vm.getUser();
        expect(wrapper.vm.utilisateur).toEqual({
          id: 1,
          username: 'username test',
          last_name: 'last name test',
          first_name: 'first name test',
          email: 'email test',
          companys: [
            {
              id: 1,
              company_statut: {code: 'VAL'},
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
            }
          ]
        });
        expect(wrapper.vm.companies).toEqual([
          {
            id: 1,
            company_statut: {code: 'VAL'},
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
              {dt_debut: '01/01/2021', dt_fin: '01/02/2021 10:00:34', company_name: 'test name'},
              {dt_debut: '01/01/2021', dt_fin: '01/08/2021 10:00:34', company_name: 'test name'},
              {dt_debut: '01/01/2021', dt_fin: '01/03/2021 10:00:34', company_name: 'test name'},
              {dt_debut: '01/01/2021', dt_fin: '03/09/2221 10:00:34', company_name: 'test name'}
            ]
          }
        ]);
      });

      it('error case', async() => {
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        const error = new Error('Error test');
        axios.get
        .mockImplementationOnce(() => {
          error.response = {
            status: 400,
            data: { errors: { children: {} } },
          };
          return Promise.reject(error);
        });
        await wrapper.vm.getUser();
        expect(spyOnError).toHaveBeenCalledWith(new Error('Error test'));
      });
    });


  });
});
