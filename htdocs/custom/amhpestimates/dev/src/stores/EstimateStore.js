import { action, autorun, computed, extendObservable, observable, useStrict } from 'mobx';
import poStore from './ProductionOrderStore';
import ProductionOrderModel from '../models/ProductionOrderModel';
import { STORE_STATES, StoreBase } from './StoreBase';
import { MODEL_STATES } from '../models/ModelBase';
import ProgressTracker from './ProgressTracker';

useStrict(true);

class EstimateStore extends StoreBase {
	constructor() {
		super();
		extendObservable(this, {
			productionOrders: null,
			currentIndex: -1,
			currentId: null, // Holds id of PO currently displayed in EstimateApp form
			custId: null, // Holds id of customer whose estimates are being browsed by the next and prev buttons
			progressTracker: null
		});

		var store = this;
		window.onbeforeunload = function confirmExit() {
			if (store.Modified)
				return "Please save your changes first.";
		}
	}

	@action fetchProductionOrder(poid, custId=null)
	{
		var store = this;
		var index = -1;
		if (store.productionOrders)
			index = store.productionOrders.findIndex(po => po.POID == poid);

		if (index>=0)
		{
			store.currentIndex = index;
			store.currentId = store.productionOrders[store.currentIndex].POID;
			return $.Deferred().resolve().promise();
		}
		else{
			store.setStatus(STORE_STATES.BUSY, "loading production orders");
			return poStore.fetchPO(poid, custId).then(
				action("EstimateStore.fetchProductionOrder success", pos => store.processFetchedData(pos, 0, poid, custId)),
				action("EstimateStore.fetchProductionOrder failed", error => store.processError(error)),
			);
		}
	}

	@action fetchNextProductionOrderPage()
	{
		var store = this;
		var currentPOID = this.currentId;
		var currentCustId = this.custId;
		store.setStatus(STORE_STATES.BUSY, "loading production orders");
		return poStore.fetchPOAfter(currentPOID, currentCustId).then(
			action("EstimateStore.fetchNextProductionOrderPage success", pos => {
				store.processFetchedData(pos, 1, null, currentCustId);
				if (this.currentId && currentPOID && this.currentId == currentPOID)
					$.jnotify("There are no more production orders");
			}),
			action("EstimateStore.fetchNextProductionOrderPage failed", error => store.processError(error)),
		);
	}

	@action fetchPrevProductionOrderPage()
	{
		var store = this;
		var currentPOID = this.currentId;
		var currentCustId = this.custId;
		store.setStatus(STORE_STATES.BUSY, "loading production orders");
		return poStore.fetchPOBefore(currentPOID, currentCustId).then(
			action("EstimateStore.fetchPrevProductionOrderPage success", pos => {
				store.processFetchedData(pos, -1, null, currentCustId);
				if (this.currentId && currentPOID && this.currentId == currentPOID)
					$.jnotify("There are no more production orders");
			}),
			action("EstimateStore.fetchPrevProductionOrderPage failed", error => store.processError(error)),
		);
	}

	@action fetchProductionOrders(page=-1)
	{
		if (page < 0)
		{
			if (poStore.pageIndex != -1)
				page = poStore.pageIndex;
			else
				page = 0;
		}

		var pageDiff = page-poStore.pageIndex; // positive means we are moving forward, negative means we are moving backwards
		if (pageDiff == 0)
			return $.Deferred().resolve().promise(); // Nothing to do

		var store = this;
		store.setStatus(STORE_STATES.BUSY, "loading production orders");
		return poStore.fetchPage(page).then(
			action("EstimateStore.fetchProductionOrders success", pos => store.processFetchedData(pos, pageDiff)),
			action("EstimateStore.fetchProductionOrders failed", error => store.processError(error)),
		);
	}
	
	/** 
	 * pos is the new returned data. 
	 * dir is the scroll direction. dir>0 means pick the next highest PO. dir<0 means the lext lowest PO. And dir=0 means choose the same PO.
	 */
	@action processFetchedData(pos, dir=0, selectPOID=null, custId=null)
	{
		var store = this;
		if (pos == store.productionOrders)
		{
			store.setStatus(STORE_STATES.READY);
			return; // No change, nothing to do
		}

		var currentPOID = selectPOID;
		if (currentPOID == null)
			currentPOID = store.currentIndex>=0 && store.currentIndex<store.productionOrders.length ? store.productionOrders[store.currentIndex].POID : null;

		store.productionOrders = poStore._pos;
		var useMockData = !window.location.href.includes(".php");
		if (useMockData)
				store.productionOrders.forEach((po) => { 
					po._items = getItemStore().fetchItemsFor(po);
				});

		// If the new page is ahead, look for the first PO that has POID > current PO. If not found set currentIndex to same POID or 0;
		// If the new page is back, look for the last PO that has POID < current PO. If not found set currentIndex to same POID or 0;
		var newIndex = -1;
		if (currentPOID != null && store.productionOrders.length>0)
		{
			if (dir > 0) // positive means we are moving forward, negative means we are moving backwards
				newIndex = store.productionOrders.findIndex(po => po.POID > currentPOID);
			else if (dir < 0)
			{
				// Search in reverse
				var rev = Array.from(store.productionOrders).reverse();
				newIndex = rev.findIndex(po => po.POID < currentPOID);
				if (newIndex >= 0)
					newIndex = store.productionOrders.length-newIndex-1;
			}
			if (newIndex == -1)
				newIndex = store.productionOrders.findIndex(po => po.POID == currentPOID);
		}

		if (newIndex == -1)
			store.currentIndex = this.productionOrders.length > 0 ? 0 : -1;
		else
			store.currentIndex = newIndex;
		store.currentId = store.currentIndex >= 0 ? store.productionOrders[store.currentIndex].POID : null;
		store.custId = custId;
		store.setStatus(STORE_STATES.READY);
	}

	@action processError(error)
	{
		if ("message" in error)
			this.setStatus(STORE_STATES.ERROR, error.message);
		else
			this.setStatus(STORE_STATES.ERROR, poStore.statusDescription);
	}

	@computed get ProductionOrders() { return this.productionOrders && this.productionOrders.filter(po => !po.Deleted); }

	@computed get CurrentProductionOrder() { 
		return this.productionOrders && this.currentIndex>=0 && this.currentIndex<this.productionOrders.length ? this.productionOrders[this.currentIndex] : null;
	}

	@action fetchNextProductionOrder() {
		if (this.productionOrders)
		{
			var needNewPage = false;
			if(this.currentIndex < this.productionOrders.length-1)
			{
				var nextIndex = this.currentIndex + 1;
				while(nextIndex < this.productionOrders.length && this.productionOrders[nextIndex].Deleted)
					nextIndex++;

				if(nextIndex < this.productionOrders.length)
				{
					this.currentIndex = nextIndex;
					this.currentId = this.productionOrders[this.currentIndex].POID;
					return $.Deferred().resolve().promise();
				}
				else
					needNewPage = true;
			}
			else
				needNewPage = true;

			if (needNewPage)
			{
				if (this.Modified)
					$.jnotify("Please save changes first");
				else
					return this.fetchNextProductionOrderPage();
			}
		}
		return $.Deferred().fail().promise();
	}

	@action fetchPrevProductionOrder() {
		if (this.productionOrders)
		{
			var needNewPage = false;
			if (this.currentIndex > 0)
			{
				var prevIndex = this.currentIndex - 1;
				while(prevIndex >= 0 && this.productionOrders[prevIndex].Deleted)
					prevIndex--;

				if(prevIndex >= 0)
				{
					this.currentIndex = prevIndex;
					this.currentId = this.productionOrders[this.currentIndex].POID;
					return $.Deferred().resolve().promise();
				}
				else
					needNewPage = true;
			}
			else
				needNewPage = true;

			if (needNewPage)
			{
				if (this.Modified)
					$.jnotify("Please save changes first");
				else
					return this.fetchPrevProductionOrderPage();
			}
		}
		return $.Deferred().fail().promise();
	}

	@action createNewProductionOrder(custId) {
		var store = this;
		store.setStatus(STORE_STATES.BUSY, "preparing a new production order");
		poStore.createNewProductionOrder(custId).done(action("EstmateStore.createPO.success", newPO => {
				if (newPO)
				{
					if (this.currentIndex<0)
					{
						if (this.productionOrders == null)
							this.productionOrders = poStore._pos;
						this.productionOrders.push(newPO);
						this.currentIndex = this.productionOrders.length-1;
					}
					else
					{
						this.currentIndex++;
						this.productionOrders.splice(this.currentIndex, 0, newPO);
					}
					this.currentId = this.productionOrders[this.currentIndex].POID;
					this.custId = this.productionOrders[this.currentIndex].customerId;
				}
				store.setStatus(STORE_STATES.READY);
			})).fail(action("EstmateStore.createPO.error",	errorText => {
				$.jnotify(errorText, 'error', {timeout:7});
				store.setStatus(STORE_STATES.READY);
			})
		);
	}

	@action copyProductionOrder(po) {
		var store = this;
		store.setStatus(STORE_STATES.BUSY, "duplicating the production order");
		return poStore.copyProductionOrder(po, store.custId).then(
			action("EstimateStore.fetchProductionOrder success", data => store.processFetchedData(data.pos, 0, data.newPOID, store.custId)),
			action("EstimateStore.fetchProductionOrder failed", error => store.processError(error)),
		);
	}

	@action deleteCurrentProductionOrder() {
		if (this.currentIndex >= 0 && this.currentIndex < this.productionOrders.length)
		{
			this.productionOrders[this.currentIndex].delete();
			var nextIndex = this.currentIndex + 1;
			while(nextIndex < this.productionOrders.length && this.productionOrders[nextIndex].Deleted)
				nextIndex++;

			if (nextIndex < this.productionOrders.length)
			{
				this.currentIndex = nextIndex;
				this.currentId = this.productionOrders[this.currentIndex].POID;
			}
			else
			{
				var prevIndex = this.currentIndex - 1;
				while(prevIndex >= 0 && this.productionOrders[prevIndex].Deleted)
					prevIndex--;

				if (prevIndex >= 0)
				{
					this.currentIndex = prevIndex;
					this.currentId = this.productionOrders[this.currentIndex].POID;
				}
				else
				{
					this.currentIndex = -1;
					this.currentId = null;
				}
			}
		}
	}

	@computed get Modified()
	{
		return poStore.Modified;
	}

	@action save()
	{
		var store = this;
		store.setStatus(STORE_STATES.BUSY, "saving changes");

		//Remember the selected PO
		var currentPO = this.CurrentProductionOrder;

		this.progressTracker = new ProgressTracker();
		return poStore.save(this.progressTracker).then(
			action("EstimateStore.save().success", () => {
				// Reset the selected PO
				if (currentPO)
				{
					var newIndex = store.productionOrders.indexOf(currentPO);
					if (newIndex >= 0)
					{
						this.currentIndex = newIndex;
						this.currentId = this.productionOrders[this.currentIndex].POID;
					}
				}
				store.setStatus(STORE_STATES.READY);
				//console.log("Saving...Done!")
				return Promise.resolve();
			}),
			action("EstimateStore.save().fail", (error) => {
				store.setStatus(STORE_STATES.READY);
				//console.error("Saving...failed!", error);
				$.jnotify("Saving...failed! " + error, 'error', {timeout:7});
				return Promise.reject();
			})
		);
	}

	/** Discard changes and reload current page of production orders */
	@action cancelChanges()
	{
		var oldCustId = this.custId;
		var oldPageIndex = poStore.pageIndex;
		poStore.pageIndex = -1; // Force poStore to reload page
		var oldPOID = this.currentId;
		if (oldPOID == null && this.productionOrders.length > 0)
			oldPOID = this.productionOrders.find((po) => !po.New && !po.Trash);
		if (!oldPOID)
		{
			var backtopage = window.GetParameterValues('backtopage', null);
			if (backtopage != null)
				backtopage = decodeURIComponent(backtopage);
			else
				backtopage = "list.php?mainmenu=amhpestimates&restore_lastsearch_values=1";
			window.location.href = backtopage;
		}

		this.productionOrders = [];
		this.currentIndex = -1;
		this.currentId = null;
		this.custId = null;
		if (oldPageIndex >= 0 || !oldPOID)
			this.fetchProductionOrders(oldPageIndex);
		else
			this.fetchProductionOrder(oldPOID, oldCustId);
	}

	@action clearProgress()
	{
		this.progressTracker = null;
	}

	@computed get asString() {
		var s = "ProductionOrders = {\n";
		if (!this.ProductionOrders) 
			s += "	ProductionOrders: No data found!\n";
		else
			s += `	ProductionOrders: ${this.ProductionOrders.length} PO's\n`;
		s += `	currentIndex: ${this.currentIndex}\n`;
		s += `	currentId: ${this.currentId}\n`;
		s += "	current:" + ((this.CurrentProductionOrder) ? this.CurrentProductionOrder.POID : "None") + "\n";
		s += "}";
		return s;
	}
}

var store = window.store = new EstimateStore();

export default store;