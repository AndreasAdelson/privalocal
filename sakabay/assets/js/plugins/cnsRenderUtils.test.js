import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import VueLogger from 'vuejs-logger';
import cnsRenderUtils from './cnsRenderUtils';

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
localVue.use(VueLogger, loggerOptions);

const i18n = new VueI18n({
  locale: 'fr',
  fallbackLocale: 'fr',
  messages: {
    'en': require('i18n/en.json'),
    'fr': require('i18n/fr.json')
  }
});

const wrapper = shallow({}, {
  i18n, localVue
});

describe('cnsRenderUtils', () => {

  describe('$getUserLabel(user)', () => {
    it('user is undefined', () => {
      expect(wrapper.vm.$getUserLabel()).toEqual('');
    });

    it('user is defined', () => {
      let user = {
        last_name: 'toto',
        first_name: 'titi',
        username: 'username'
      };
      expect(wrapper.vm.$getUserLabel(user)).toEqual('TOTO titi [username]');
    });
  });

  describe('$getCityLabel', () => {
    it('without city', () => {
      expect(wrapper.vm.$getCityLabel()).toEqual('');
    });
    it('with city', () => {
      let city = {
        name: 'city name'
      };
      expect(wrapper.vm.$getCityLabel(city)).toEqual('city name');
    });
  });

  describe('$getAuthorLabel', () => {
    it('without author', () => {
      expect(wrapper.vm.$getAuthorLabel()).toEqual('');
    });
    it('with author and without name', () => {
      let user = {
        last_name: 'toto',
        first_name: 'titi',
        username: 'username',
      };
      expect(wrapper.vm.$getAuthorLabel(user)).toEqual('toto titi');
    });
    it('with author and with name', () => {
      let user = {
        last_name: 'toto',
        first_name: 'titi',
        username: 'username',
        name: 'test name'
      };
      expect(wrapper.vm.$getAuthorLabel(user)).toEqual('test name');
    });
  });

  describe('$getPaymentMethodLabel', () => {
    it('without entity', () => {
      expect(wrapper.vm.$getPaymentMethodLabel()).toEqual('');
    });
    it('with entity', () => {
      // const spyOnReplaceAll = jest.spyOn(wrapper.vm, 'replaceAll');
      let entity = {
        country: 'FR',
        fingerprint: 'adaadadaad',
        last4: 1234
      };
      expect(wrapper.vm.$getPaymentMethodLabel(entity)).toEqual('************ 1234');
    });
  });

  describe('$getDateLabelFromNow ', () => {
    it('with date', () => {
      let date = '01/01/2020';
      expect(wrapper.vm.$getDateLabelFromNow(date)).toEqual('2 years ago');
    });
  });

  it('$getDateLabel test', () => {
    let date = '01/01/2020 10:00:34';
    expect(wrapper.vm.$getDateLabel(date)).toEqual('01/01/2020 à 10:00');
  });

  it('$getDateLabel2 test', () => {
    let date = '01/01/2020 10:00:34';
    expect(wrapper.vm.$getDateLabel2(date)).toEqual('01/01/2020 10:00');
  });

  it('$getDateLabelForComments  test', () => {
    let date = '01/01/2020 10:00:34';
    expect(wrapper.vm.$getDateLabelForComments (date)).toEqual('January 1, 2020');
  });

  describe('$capitalise', () => {
    it('with a string', () => {
      expect(wrapper.vm.$capitalise('test')).toEqual('Test');
    });
    it('without a string', () => {
      expect(wrapper.vm.$capitalise(1234)).toEqual('');
    });
  });

  describe('$handleError(error)', () => {
    describe('error with response', () => {
      it('response with data', () => {
        const spy = jest.fn();
        wrapper.vm.$log.error = spy;
        let error = new Error();
        error.response = {
          data: {
            code: 500,
            message: 'Erreur test'
          }
        };
        wrapper.vm.$handleError(error);
        expect(spy).toHaveBeenCalledWith('Erreur 500: Erreur test');
      });

      it('response with headers', () => {
        wrapper.vm.$log.error =  jest.fn();
        let error = new Error();
        error.response = {
          headers: {
            'x-message': 'error message from headers',
          }
        };
        wrapper.vm.$handleError(error);
        expect(wrapper.vm.$log.error).toHaveBeenCalledWith('error message from headers');
      });
    });

    it('error without response, with message', () => {
      const spy = jest.spyOn(wrapper.vm.$log, 'error');
      let error = new Error('erreur test');
      wrapper.vm.$handleError(error);
      expect(spy).toHaveBeenCalledWith('erreur test');
    });

    it('error without response and message', () => {
      const spy = jest.spyOn(wrapper.vm.$log, 'error');
      let error = new Error();
      wrapper.vm.$handleError(error);
      expect(spy).toHaveBeenCalledWith(error);
    });

    it('no error given', () => {
      const spy = jest.spyOn(wrapper.vm.$log, 'error');
      wrapper.vm.$handleError();
      expect(spy).toHaveBeenCalled();
    });
  });

  it('$camelToSnakeCase(string)', () => {
    expect(wrapper.vm.$camelToSnakeCase('myTest1String')).toEqual('my_test_1_string');
  });

  it('$getNbCharactersLeft(text, nbMaxChars)', () => {
    expect(wrapper.vm.$getNbCharactersLeft('je suis un texte de 32 carctères', 32)).toEqual(0);
    expect(wrapper.vm.$getNbCharactersLeft('je suis un texte de 32 carctères', 50)).toEqual(18);
    expect(wrapper.vm.$getNbCharactersLeft('', 32)).toEqual(32);
    expect(wrapper.vm.$getNbCharactersLeft(null, 32)).toEqual(32);
  });

  it('$getStringWithSpaces(string)', () => {
    expect(wrapper.vm.$getStringWithSpaces('123')).toEqual('123');
    expect(wrapper.vm.$getStringWithSpaces('1234567891234')).toEqual('123 456 789 1234');
    expect(wrapper.vm.$getStringWithSpaces('12345678912345')).toEqual('123 456 789 12345');
    expect(wrapper.vm.$getStringWithSpaces('123456789123456')).toEqual('123 456 789 12345 6');
  });

  it('$refreshScrollbar()', () => {
    const spy = jest.fn();
    wrapper.vm.$vuebar = {
      refreshScrollbar: spy
    },
    jest.useFakeTimers();
    wrapper.vm.$refreshScrollbar();
    jest.runAllTimers();
    expect(spy).toHaveBeenCalledWith(document.getElementById('scrollbar'));
  });
});
