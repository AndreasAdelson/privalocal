global.spyOnJQueryTooltip = jest.fn();
global.spyOnJQueryModal = jest.fn();
global.spyOnJQueryHide = jest.fn();
global.spyOnJQueryReady = jest.fn();
global.spyOnJQueryAddClass = jest.fn();
global.spyOnJQueryRemoveClass = jest.fn();
global.$ = jest.fn(() => {
  return {
    tooltip: global.spyOnJQueryTooltip,
    modal: global.spyOnJQueryModal,
    hide: global.spyOnJQueryHide,
    ready: global.spyOnJQueryReady,
    addClass: global.spyOnJQueryAddClass,
    removeClass: global.spyOnJQueryRemoveClass,
    each: jest.fn(),
  };
});
global.spyOnWindowScroll = jest.fn();
window.scroll = global.spyOnWindowScroll;

window.translations = {
  'filter_search_keyword_placeholder': '',
};
Object.defineProperty(window, 'location', {
  writable: true,
  value: { assign: jest.fn() }
});

