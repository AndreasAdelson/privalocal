import { shallow, createLocalVue } from 'vue-test-utils';
import VueI18n from 'vue-i18n';
import _ from 'lodash';
import cnsRenderUtils from 'plugins/cnsRenderUtils';
import cnsFormUtils from 'plugins/cnsFormUtils';
import DualList from './dual-list.vue';

const localVue = createLocalVue();
localVue.use(VueI18n);
localVue.use(cnsRenderUtils);
localVue.use(cnsFormUtils);

let wrapper;

describe('Without props', () => {
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(DualList, {
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
});

describe('With props', () => {
  const props = {
    items: [{ a: '1' }],
    itemLabelFields: ['a'],
  };
  beforeEach(() => {
    const i18n = new VueI18n({
      locale: 'fr',
      fallbackLocale: 'fr',
      messages: {
        'en': require('i18n/en.json'),
        'fr': require('i18n/fr.json')
      }
    });
    wrapper = shallow(DualList, {
      i18n,
      localVue,
      propsData: _.cloneDeep(props),
    });
  });

  /**
   * Test the initialization of the Vue component.
   */
  it('initializes', () => {
    expect(wrapper.vm).toBeDefined();
  });

  describe('computed attribute', () => {
    it('printedSelectedItems', () => {
      wrapper.vm.selectedItems = [{ a: '1' }, { a: '11' }, { a: 'aoui1sdf' }, { a: '4' }, { a: '5' }, { a: '6' }];
      wrapper.vm.selectedItemsSearchText = '1';
      expect(wrapper.vm.printedSelectedItems).toEqual([{ a: '1' }, { a: '11' }, { a: 'aoui1sdf' }]);
    });

    it('printedUnselectedItems', () => {
      wrapper.vm.itemLabelFields = ['a', 'b', 'c'];
      wrapper.vm.unselectedItems = [{ a: '1' }, { b: '11' }, { c: 'aoui1sdf' }, { a: '4' }, { a: '5' }, { a: '6' }];
      wrapper.vm.unselectedItemsSearchText = '1';
      expect(wrapper.vm.printedUnselectedItems).toEqual([{ a: '1' }, { b: '11' }, { c: 'aoui1sdf' }]);
    });

    it('areAllSelectedItemsClicked', () => {
      wrapper.vm.selectedItems = [{ a: 1 }, { a: 2 }, { a: 3 }, { a: 4 }];
      wrapper.vm.clickedItems = [{ a: 1 }, { a: 2 }, { a: 3 }, { a: 4 }];
      expect(wrapper.vm.areAllSelectedItemsClicked).toEqual(true);
      wrapper.vm.selectedItems = [];
      wrapper.vm.clickedItems = [];
      expect(wrapper.vm.areAllSelectedItemsClicked).toEqual(false);
    });

    it('areAllUnselectedItemsClicked', () => {
      wrapper.vm.unselectedItems = [{ a: 1 }, { a: 2 }, { a: 3 }, { a: 4 }];
      wrapper.vm.clickedItems = [{ a: 1 }, { a: 2 }, { a: 3 }, { a: 4 }];
      expect(wrapper.vm.areAllUnselectedItemsClicked).toEqual(true);
      wrapper.vm.unselectedItems = [];
      wrapper.vm.clickedItems = [];
      expect(wrapper.vm.areAllUnselectedItemsClicked).toEqual(false);
    });
  });

  describe('method', () => {
    it('orderItems()', () => {
      wrapper.vm.unselectedItems = [{ a: '2' }, { a: '0' }, { a: '1' }];
      wrapper.vm.selectedItems = [{ a: '2' }, { a: '0' }, { a: '1' }];
      wrapper.vm.orderItems();
      expect(wrapper.vm.unselectedItems).toEqual([{ a: '0' }, { a: '1' }, { a: '2' }]);
      expect(wrapper.vm.selectedItems).toEqual([{ a: '0' }, { a: '1' }, { a: '2' }]);
    });

    it('selectItems()', () => {
      wrapper.vm.unselectedItems = [{ a: '1' }, { a: '3' }];
      wrapper.vm.selectedItems = [{ a: '2' }, { a: '4' }];
      wrapper.vm.clickedItems = [{ a: '1' }, { a: '2' }];
      wrapper.vm.selectItems();
      expect(wrapper.vm.unselectedItems).toEqual([{ a: '3' }]);
      expect(wrapper.vm.selectedItems).toEqual([{ a: '1' }, { a: '2' }, { a: '4' }]);
      expect(wrapper.vm.clickedItems).toEqual([{ a: '1' }, { a: '2' }]);
    });

    it('unselectItems()', () => {
      wrapper.vm.unselectedItems = [{ a: '1' }, { a: '3' }];
      wrapper.vm.selectedItems = [{ a: '2' }, { a: '4' }];
      wrapper.vm.clickedItems = [{ a: '1' }, { a: '2' }];
      wrapper.vm.unselectItems();
      expect(wrapper.vm.unselectedItems).toEqual([{ a: '1' }, { a: '2' }, { a: '3' }]);
      expect(wrapper.vm.selectedItems).toEqual([{ a: '4' }]);
      expect(wrapper.vm.clickedItems).toEqual([{ a: '1' }, { a: '2' }]);
    });

    describe('toggleAllSelectedItems()', () => {
      it('all selectedItems are clicked', () => {
        wrapper.vm.selectedItems = [{ a: '1' }, { a: '2' }, { a: '3' }, { a: '4' }];
        wrapper.vm.clickedItems = [{ a: '1' }, { a: '2' }, { a: '3' }, { a: '4' }, { a: '5' }, { a: '6' }];
        wrapper.vm.toggleAllSelectedItems();
        expect(wrapper.vm.clickedItems).toEqual([{ a: '5' }, { a: '6' }]);
      });
      it('not all selectedItems are clicked', () => {
        wrapper.vm.selectedItems = [{ a: '1' }, { a: '2' }, { a: '3' }, { a: '4' }, { a: '5' }, { a: '6' }];
        wrapper.vm.clickedItems = [{ a: '1' }, { a: '2' }, { a: '3' }, { a: '4' }, { a: '7' }];
        wrapper.vm.toggleAllSelectedItems();
        expect(wrapper.vm.clickedItems).toEqual([{ a: '1' }, { a: '2' }, { a: '3' }, { a: '4' }, { a: '7' }, { a: '5' }, { a: '6' }]);
      });
    });

    describe('toggleAllUnselectedItems()', () => {
      it('all unselectedItems are clicked', () => {
        wrapper.vm.unselectedItems = [{ a: '1' }, { a: '2' }, { a: '3' }, { a: '4' }];
        wrapper.vm.clickedItems = [{ a: '1' }, { a: '2' }, { a: '3' }, { a: '4' }, { a: '5' }, { a: '6' }];
        wrapper.vm.toggleAllUnselectedItems();
        expect(wrapper.vm.clickedItems).toEqual([{ a: '5' }, { a: '6' }]);
      });
      it('not all unselectedItems are clicked', () => {
        wrapper.vm.unselectedItems = [{ a: '1' }, { a: '2' }, { a: '3' }, { a: '4' }, { a: '5' }, { a: '6' }];
        wrapper.vm.clickedItems = [{ a: '1' }, { a: '2' }, { a: '3' }, { a: '4' }, { a: '7' }];
        wrapper.vm.toggleAllUnselectedItems();
        expect(wrapper.vm.clickedItems).toEqual([{ a: '1' }, { a: '2' }, { a: '3' }, { a: '4' }, { a: '7' }, { a: '5' }, { a: '6' }]);
      });
    });

    describe('toggleInClickedItems(item)', () => {
      it('item is in clickedItems', () => {
        wrapper.vm.clickedItems = [{ a: '1' }, { a: '2' }, { a: '3' }];
        wrapper.vm.toggleInClickedItems({ a: '2' });
        expect(wrapper.vm.clickedItems).toEqual([{ a: '1' }, { a: '3' }]);
      });
      it('item not in clickedItems', () => {
        wrapper.vm.clickedItems = [{ a: '1' }, { a: '2' }, { a: '3' }];
        wrapper.vm.toggleInClickedItems({ a: '4' });
        expect(wrapper.vm.clickedItems).toEqual([{ a: '1' }, { a: '2' }, { a: '3' }, { a: '4' }]);
      });
    });

    describe('removeItemFromArray(itemToRemove, array)', () => {
      it('when item is in array', () => {
        let array = [1, '2', { c: 'c' }, { a: [{ a: 'a' }] }];
        let obj = { a: [{ a: 'a' }] };
        expect(wrapper.vm.removeItemFromArray(obj, array)).toEqual(3);
        expect(array).toEqual([1, '2', { c: 'c' }]);
      });
      it('when item is not in array', () => {
        let array = [1, '2', { c: 'c' }, { a: [{ a: 'a' }] }];
        let obj = { a: 6 };
        expect(wrapper.vm.removeItemFromArray(obj, array)).toEqual(-1);
        expect(array).toEqual([1, '2', { c: 'c' }, { a: [{ a: 'a' }] }]);
      });
    });

    it('arrayContainsObject(array, object)', () => {
      let array = [1, '2', { c: 'c' }, { a: [{ a: 'a' }] }];
      let obj = { a: [{ a: 'a' }] };
      expect(wrapper.vm.arrayContainsObject(array, obj)).toEqual(true);
    });

    it('areSameItems(item1, item2)', () => {
      expect(wrapper.vm.areSameItems({ a: [{ a: 'a' }] }, { a: [{ a: 'a' }] })).toEqual(true);
    });

    describe('getItemLabel(item)', () => {
      it('with 1 item label field', () => {
        wrapper.vm.itemLabelFields = ['a'];
        expect(wrapper.vm.getItemLabel({ a: 'a' })).toEqual('a');
      });
      it('with 2 item label field', () => {
        wrapper.vm.itemLabelFields = ['a', 'b'];
        expect(wrapper.vm.getItemLabel({ a: 'a', b: 'b' })).toEqual('[a] b');
      });
      it('with 3 item label field', () => {
        wrapper.vm.itemLabelFields = ['a', 'b', 'c'];
        expect(wrapper.vm.getItemLabel({ a: 'a', b: 'b', c: 'c' })).toEqual('[a] b (c)');
      });
    });
  });

  describe('watcher on', () => {
    it('selectedItems', async() => {
      wrapper.vm.selectedItems = [];
      wrapper.vm.selectedItems = [{ a: '1' }];
      let spyOnEmit = jest.spyOn(wrapper.vm, '$emit');
      await wrapper.vm.$nextTick();
      expect(spyOnEmit).toHaveBeenCalledWith('selected-items-change', [{ a: '1' }]);
    });

    describe('doResetList', () => {
      it('doResetList = true', async() => {
        wrapper.vm.clickedItems = [{ a: '1' }];
        wrapper.vm.selectedItems = [{ a: '2' }];
        wrapper.vm.unselectedItems = [{ a: '3' }];
        wrapper.vm.items = [{ a: '6' }, { a: '5' }];
        wrapper.vm.unselectedItemsSearchText = 'texte';
        wrapper.vm.selectedItemsSearchText = 'texte';
        wrapper.vm.doResetList = true;
        await wrapper.vm.$nextTick();
        expect(wrapper.vm.clickedItems).toEqual([]);
        expect(wrapper.vm.selectedItems).toEqual([]);
        expect(wrapper.vm.unselectedItems).toEqual([{ a: '5' }, { a: '6' }]);
        expect(wrapper.vm.unselectedItemsSearchText).toEqual('');
        expect(wrapper.vm.selectedItemsSearchText).toEqual('');
      });
      it('doResetList = false', async() => {
        wrapper.vm.clickedItems = [{ a: '1' }];
        wrapper.vm.selectedItems = [{ a: '2' }];
        wrapper.vm.unselectedItems = [{ a: '3' }];
        wrapper.vm.items = [{ a: '6' }, { a: '5' }];
        wrapper.vm.unselectedItemsSearchText = 'texte';
        wrapper.vm.selectedItemsSearchText = 'texte';
        wrapper.vm.doResetList = false;
        await wrapper.vm.$nextTick();
        expect(wrapper.vm.clickedItems).toEqual([{ a: '1' }]);
        expect(wrapper.vm.selectedItems).toEqual([{ a: '2' }]);
        expect(wrapper.vm.unselectedItems).toEqual([{ a: '3' }]);
        expect(wrapper.vm.unselectedItemsSearchText).toEqual('texte');
        expect(wrapper.vm.selectedItemsSearchText).toEqual('texte');
      });
    });
  });
});
