import {action, autorun, computed, extendObservable, observable } from 'mobx';
import itemData from './items';
import ProductionOrderItemModel from '../models/ProductionOrderItemModel';

/*
Provides:
  1) a fetch method to load items for a given production order
*/
class ProductionOrderItemStore {
  constructor() {
    extendObservable(this, {
      items: null
    });

    if (itemData.items && itemData.items.length > 0)
    {
      this.items = itemData.items.map((item) => new ProductionOrderItemModel(item, null, false));
    }
  }

  // Returns an iterable (use for...of)
  fetchItemsFor(po)
  { 
    var items = this.items.filter((item) => item.POID == po.POID);
    items.forEach((item) => { item.po = po; });
    return items;
  }

  @computed get asString() {
    var s = "";
    if (!this.Items) 
      s += "  Items: No data found!\n";
    else
      s += `  Items: ${this.Items.length} items's\n`;
    return s;
  }
}

var itemStore = null;
var lazyStore = function() 
{
  if (itemStore == null)
    itemStore = new ProductionOrderItemStore()
  return itemStore;
}

export default lazyStore;