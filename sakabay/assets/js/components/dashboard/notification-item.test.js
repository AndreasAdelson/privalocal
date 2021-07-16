import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import NotificationItem from './notification-item.vue';
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

describe('notification item component', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(NotificationItem, {
      i18n,
      localVue,
      propsData: {
        notif: {
          seen: false,
          notification: {
            date: '2021-01-02 10:00:34',
            message: 'test message',
            link: 'test link'
          },
          notifiable: {
            id: 1
          }
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
  describe('computed test', () => {
    it('notificationDate test', () => {
      expect(wrapper.vm.notificationDate).toEqual('le 2 January, 10:00');
    });
  });

  describe('methods test', () => {
    it('goToNotificationSubject', () => {
      wrapper.vm.notif = {
        seen: false,
        notification: {
          date: '2021-01-02 10:00:34',
          message: 'test message',
          link: 'test link'
        },
        notifiable: {
          id: 1
        }
      };
      const spyOnMarkAsSeen = jest.spyOn(wrapper.vm, 'markAsSeen');
      wrapper.vm.goToNotificationSubject();
      expect(spyOnMarkAsSeen).toHaveBeenCalledWith('test link');
    });


    describe('markAsSeen', () => {
      it('success case', async() => {
        const spyOnEmit = jest.spyOn(wrapper.vm, '$emit');
        axios.post.mockImplementationOnce(() => {
          return Promise.resolve({
          });
        });
        await wrapper.vm.markAsSeen('test');
        expect(window.location.assign).toHaveBeenCalledWith('test');
        expect(spyOnEmit).toHaveBeenCalledWith('notification-seen');
      });

      it('error cases', async() => {
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        axios.post.mockImplementationOnce(() => {
          return Promise.reject(new Error('test error'));
        });
        await wrapper.vm.markAsSeen();
        expect(spyOnError).toHaveBeenCalledWith(new Error('test error'));
      });
    });

    describe('markAsUnSeen', () => {
      it('success case', async() => {
        const spyOnEmit = jest.spyOn(wrapper.vm, '$emit');
        wrapper.vm.notif = {
          seen: false,
          notification: {
            id: 2,
            date: '2021-01-02 10:00:34',
            message: 'test message',
            link: 'test link'
          },
          notifiable: {
            id: 1
          }
        };
        axios.post.mockImplementationOnce(() => {
          return Promise.resolve({
          });
        });
        await wrapper.vm.markAsUnSeen();
        expect(spyOnEmit).toHaveBeenCalledWith('notification-unseen');
      });

      it('error cases', async() => {
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        axios.post.mockImplementationOnce(() => {
          return Promise.reject(new Error('test error'));
        });
        await wrapper.vm.markAsUnSeen();
        expect(spyOnError).toHaveBeenCalledWith(new Error('test error'));
      });
    });

    describe('deleteNotification', () => {
      it('success case', async() => {
        const spyOnEmit = jest.spyOn(wrapper.vm, '$emit');
        wrapper.vm.notif = {
          seen: false,
          notification: {
            id: 2,
            date: '2021-01-02 10:00:34',
            message: 'test message',
            link: 'test link'
          },
          notifiable: {
            id: 1
          }
        };
        axios.post.mockImplementationOnce(() => {
          return Promise.resolve({
          });
        });
        await wrapper.vm.deleteNotification();
        expect(spyOnEmit).toHaveBeenCalledWith('notification-deleted');
      });

      it('error cases', async() => {
        const spyOnError = jest.fn();
        wrapper.vm.$handleError = spyOnError;
        axios.post.mockImplementationOnce(() => {
          return Promise.reject(new Error('test error'));
        });
        await wrapper.vm.deleteNotification();
        expect(spyOnError).toHaveBeenCalledWith(new Error('test error'));
      });
    });
  });
});
