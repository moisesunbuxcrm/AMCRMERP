import {action, autorun, computed, extendObservable, observable } from 'mobx';
import ProductionOrderModel from '../models/ProductionOrderModel';
import { STORE_STATES, StoreBase } from './StoreBase';
import { MODEL_STATES } from '../models/ModelBase';

/*
Provides:
	1) a fetch method to load 5 production orders starting at a given index
	2) a fetch method to load a production order with a given POID
	3) a fetch method to load production orders for a given customer
*/
class ProductionOrderStore extends StoreBase {
	
	constructor() {
		super();
		extendObservable(this, {
			_pos: [],
			pageIndex: -1
		});
	}

	/** Returns list of PO's that have not been deleted */
	@computed get POS() { return this._pos && this._pos.filter(po => !po.Deleted); }

	@action fetchPage(pageIndex = -1) 
	{ 
		if (this.getStatus() == STORE_STATES.BUSY)
			return this.pendingFetch;

		if (pageIndex == -1)
			pageIndex = 0;
		
		if (pageIndex == this.pageIndex)
			return $.Deferred().resolve(this._pos).promise(); // Nothing to do, return a completed promise

		var dataUrl = "db/searchPO.php?ii=1&pg="+pageIndex;
		var useMockData = !window.location.href.includes(".php");
		if (useMockData)
			dataUrl = "pos.json";

		var promise = $.ajax({
			url: dataUrl,
			dataType: 'json'
		}).then(
			data => this.processFetchedData(data, pageIndex), 
			(req, errorText) => this.processFetchError(req, errorText));

		this.setStatus(STORE_STATES.BUSY, null, promise); // Sets this.pendingFetch = promise
		return promise;
	}

	@action fetchPO(poid, custId=null) 
	{ 
		if (this.getStatus() == STORE_STATES.BUSY)
			return this.pendingFetch;

		var store = this;
		var dataUrl = "db/searchPO.php?ii=1&po="+poid+(custId?"&custid="+custId:"");
		var promise = $.ajax({
			url: dataUrl,
			dataType: 'json'
		}).then(
			action("ProductionOrderStore.fetchPO() succeeded", data => store.processFetchedData(data, -1, custId)), 
			action("ProductionOrderStore.fetchPO() failed", (req, errorText) => store.processFetchError(req, errorText)));

		this.setStatus(STORE_STATES.BUSY, null, promise); // Sets this.pendingFetch = promise
		return promise;
	}

	@action fetchPOAfter(poid, custId=null) 
	{ 
		if (this.getStatus() == STORE_STATES.BUSY)
			return this.pendingFetch;
			
		var store = this;
		var dataUrl = "db/searchPO.php?ii=1&nextpo="+poid+(custId?"&custid="+custId:"");
		var promise = $.ajax({
			url: dataUrl,
			dataType: 'json'
		}).then(
			action("ProductionOrderStore.fetchNextPO() succeeded", data => store.processFetchedData(data, -1, custId)), 
			action("ProductionOrderStore.fetchNextPO() failed", (req, errorText) => store.processFetchError(req, errorText)));

		this.setStatus(STORE_STATES.BUSY, null, promise); // Sets this.pendingFetch = promise
		return promise;
	}

	@action fetchPOBefore(poid, custId=null) 
	{ 
		if (this.getStatus() == STORE_STATES.BUSY)
			return this.pendingFetch;
			
		var store = this;
		var dataUrl = "db/searchPO.php?ii=1&prevpo="+poid+(custId?"&custid="+custId:"");
		var promise = $.ajax({
			url: dataUrl,
			dataType: 'json'
		}).then(
			action("ProductionOrderStore.fetchPrevPO() succeeded", data => store.processFetchedData(data, -1, custId)), 
			action("ProductionOrderStore.fetchPrevPO() failed", (req, errorText) => store.processFetchError(req, errorText)));

		this.setStatus(STORE_STATES.BUSY, null, promise); // Sets this.pendingFetch = promise
		return promise;
	}

	@action processFetchedData(data, pageIndex, custId=null)
	{
		if (data.length != 0)
		{
			this._pos = data.map((po) => new ProductionOrderModel(po, MODEL_STATES.SAVED, false));
			this.pageIndex = pageIndex;
			this.custId = custId;
		}
		this.setStatus(STORE_STATES.READY);
		return this._pos
	}

	@action processFetchError(req, errorText)
	{
		console.log("errorText="+errorText);
		console.log("req.responseText="+req.responseText);
		var errormsg=errorText;
		if (req.status==200)
		{
			this.setStatus(STORE_STATES.ERROR, req.responseText);
			errormsg=req.responseText;
			$.jnotify(req.responseText, 'error', {timeout:7});
		}
		else
		{
			this.setStatus(STORE_STATES.ERROR, errorText);
			$.jnotify(errorText, 'error', {timeout:7});
		}
		return errormsg;
	}

	@action createNewProductionOrder(custId) {
		var newPO = new ProductionOrderModel({
			PODATE: this.dateToString(new Date()),
			QUOTEDATE: this.dateToString(new Date()),
			"PERMIT": estimateData.getDefaultValue("PERMIT", "300"),
			"INSTTIME": estimateData.getDefaultValue("INSTTIME", "8"),
			"Check50": estimateData.getDefaultBool("CHECK50", true),
			"SignatureReq": estimateData.getDefaultBool("SIGNATUREREQ", false),
			"YearsWarranty": estimateData.getDefaultValue("YEARSWARRANTY", "10"),
			"Check10YearsWarranty": estimateData.getDefaultBool("CHECK10YEARSWARRANTY", true),
			"LifeTimeWarranty": estimateData.getDefaultBool("LIFETIMEWARRANTY", false),
			"CheckFreeOpeningClosing": estimateData.getDefaultBool("CHECKFREEOPENINGCLOSING", false),
			"CheckNoPayment": estimateData.getDefaultBool("CHECKNOPAYMENT", true),
			"ESTHTVALUE": estimateData.getDefaultValue("ESTHTVALUE", "8"),
			"Check10YearsFreeMaintenance": estimateData.getDefaultBool("CHECK10YEARSFREEMAINTENANCE", true),
			"HTVALUE": estimateData.getDefaultValue("HTVALUE", "2"),
			"COLOR": estimateData.getDefaultValue("COLOR", "NONE"),
			"SQFEETPRICE": estimateData.getDefaultValue("SQFEETPRICE", "14"),
			"customerId": custId,
			"Salesman": estimateData.user.Name,
			"OTHERFEES": 0,
			"Discount": 0,
			"TOTALTRACK": 0,
			"TAPCONS": 0,
			"TOTALLONG": 0,
			"FASTENERS": 0,
			"TOTALALUMINST": 0,
			"TOTALLINEARFT": 0,
			"SQINSTPRICE": 0,
			"INSTSALESPRICE": 0,
			"CUSTVALUE": 0,
			"CUSTOMIZE": 0,
			"SALESTAXAMOUNT": 0,
			"TOTALALUM": 0,
		}, null, true);
		newPO.SALES_TAX = 0;

		var maxLoadedPONumber = 0;
		if (this.POS && this.POS.length>0)
			maxLoadedPONumber = Math.max(...this.POS.map(po => parseInt(po.PONUMBER)));

		var useMockData = !window.location.href.includes(".php");
		if (useMockData)
		{
			newPO.PONUMBER = (maxLoadedPONumber+1) + "PD";
			return $.Deferred().resolve(newPO).promise(); // Return a completed promise
		}
		else
		{
			var promise = $.ajax({
				url: "db/getPOInitialData.php?cid="+(custId==null?"":custId),
				dataType: 'json'
			}).then(action("ProductionOrderStore.getPOInitialData().success", data => {
				if (data)
				{
					// Double check max loaded PONumber
					if (this.POS && this.POS.length>0)
						maxLoadedPONumber = Math.max(...this.POS.map(po => parseInt(po.PONUMBER)));
					newPO.PONUMBER = data.PONUMBER;
					newPO.CUSTOMERNAME = data.CUSTOMERNAME;
					newPO.CONTACTNAME = data.CONTACTNAME;
					newPO.CONTACTPHONE1 = data.CONTACTPHONE1;
					newPO.CONTACTPHONE2 = data.CONTACTPHONE2;
					newPO.CUSTOMERADDRESS = data.CUSTOMERADDRESS;
					newPO.ZIPCODE = data.ZIPCODE;
					newPO.CITY = data.CITY;
					newPO.STATE = data.STATE;
					newPO.PHONENUMBER1 = data.PHONENUMBER1;
					newPO.PHONENUMBER2 = data.PHONENUMBER2;
					newPO.FAXNUMBER = data.FAXNUMBER;
					newPO.EMail = data.EMail;
				}
				this.setStatus(STORE_STATES.READY);
				return newPO;
			}), action("ProductionOrderStore.getPOInitialData().error",	(req, errorText) => {
				if (req.status==200)
				{
					$.jnotify("Failed: " + req.responseText, 'error', {timeout:7});
					return req.responseText;
				}
				else
				{
					$.jnotify("Failed: " + errorText, 'error', {timeout:7});
					return errorText;
				}
			}));
			return promise;
		}
	}

	@action copyProductionOrder(po, custId) 
	{ 
		if (this.getStatus() == STORE_STATES.BUSY)
			return this.pendingFetch;

		var store = this;
		var dataUrl = "db/copyPO.php?POID="+po.POID;
		var promise = $.ajax({
			url: dataUrl,
			dataType: 'json'
		}).then(
			action("ProductionOrderStore.copyProductionOrder() succeeded", data => {
				store.setStatus(STORE_STATES.READY);
				return store.fetchPO(data.newPOID, custId).then(
					action("EstimateStore.fetchProductionOrder success", pos => {
						pos = store.processFetchedData(pos, -1, custId);
						return { pos: pos, newPOID: data.newPOID };
					}),
					action("EstimateStore.fetchProductionOrder failed", error => store.processError(error)),
				);
				}), 
			action("ProductionOrderStore.copyProductionOrder() failed", (req, errorText) => store.processFetchError(req, errorText))
		);

		this.setStatus(STORE_STATES.BUSY, null, promise); // Sets this.pendingFetch = promise
		return promise;
	}

	// YYYY-MM-DD
	dateToString(d)
	{
		return d.getFullYear()+"-"+this.to2Digits(d.getMonth()+1)+"-"+this.to2Digits(d.getDate());
	}

	to2Digits(n)
	{
		if (n<10)
			return "0"+n;
		return n.toString();
	}
		
	@computed get Modified()
	{
		if (this._pos == null || this._pos.length == 0)
			return false;
		var countModified = 0;
		this._pos.forEach(po => countModified += po.Modified || po.anyChildrenModified ? 1 : 0); // Use forEach() to iterate all values without short circuit so that MobX sees dependence on all PO´s
		return countModified > 0; 
	}

	/** Starts saving changes to the database. Returns a promise that will be resolved when all changes are complete. */
	async save(progressTracker)
	{
		const useMockData = !window.location.href.includes(".php");
		const poStore = this;
		var promises = [];

		// Save all the required changes.
		// This code will save as many changes as it can.
		// An error in one place will not prevent unrelated saves from completing.

		var posCopy = poStore._pos.slice();
		for (var i = 0; i < posCopy.length; i++) { 
			let po = posCopy[i];
			if (po.DeletedOnly) // Ignore PO's marked as TRASH
			{
				if (useMockData)
					poStore.removePO(po);
				else
					promises.push(this.deletePOFromDB(progressTracker, po));
			}
		}

		// Insert PO's marked as new
		for (var i = 0; i < posCopy.length; i++) { 
			let po = posCopy[i];
			if (po.New)
			{
				if (useMockData)
				{
					po.POID = Math.max(...poStore.POS.map(po => parseInt(po.POID)))+1;
					po.setStatus(MODEL_STATES.SAVED);
				}
				else
					promises.push(this.addPOToDB(progressTracker, po));
			}
		}

		// Update PO's marked as modified
		for (var i = 0; i < posCopy.length; i++) { 
			let po = posCopy[i];
			if (po.ModifiedOnly || (!po.Modified && po.anyChildrenModified))
			{
				if (useMockData)
					po.setStatus(MODEL_STATES.SAVED);
				else
					promises.push(this.savePOToDB(progressTracker, po));
			}
		}

		await Promise.all(promises);
	}

	/**
	 * Asynchronously adds the given PO and all items
	 */
	async addPOToDB(progressTracker, po)
	{
		progressTracker.addMax(1+po.Items.length);

		// Send request to add PO and wait for response
		var url = "db/createPO.php";
		//console.log("addPOToDB("+po.$mobx.name+"): " + url);
		var promise = $.ajax(
			{
				url: url,
				dataType: 'json',
				method: 'POST',
				data: po.JSONObject
			});
		promise.always(() => progressTracker.addProgress(1));
		var data = await promise;

		// If create of PO was successful, add items...
		if (data && data.msg == "OK")
		{
			po.POID = data.POID;
			po.setStatus(MODEL_STATES.SAVED);

			var promises = [];
			for (const i in po.Items) {
				promises.push(this.addItemToDB(progressTracker, po.Items[i]));
			}

			await Promise.all(promises);
			//console.log("addPOToDB("+po.$mobx.name+"): Created POID=" + data.POID + ", PONUMBER=" + po.PONUMBER + " in database");
		}
		else
		{
			if (data && data.msg && data.msg.toLowerCase().includes("duplicate"))
				throw("This PONUMBER is already in use");
			throw("addPOToDB("+po.$mobx.name+"): Create failed: " + (data && data.msg ? data.msg : "Unknown " + data));
		}
	}

	/**
	 * Asynchronously deletes the given PO and all items
	 */
	async deletePOFromDB(progressTracker, po)
	{
		progressTracker.addMax(2);

		// First delete all the items for this PO
		await this.deleteItemsFromDB(progressTracker, po);

		// Send request to delete PO and wait for response
		var url = "db/deletePO.php?id="+po.POID;
		//console.log("deletePOFromDB("+po.$mobx.name+"): " + url);
		var data = await $.ajax(
			{
				url: url,
				dataType: 'json'
			});

		// If deletion of PO was successful, remove from memory...
		if (data && data.msg == "OK")
		{
			//console.log("deletePOFromDB("+po.$mobx.name+"): Deleted POID=" + po.POID + " from database");
			poStore.removePO(po);
		}
		else
			throw("deletePOFromDB("+po.$mobx.name+"): Delete failed: " + (data && data.msg ? data.msg : "Unknown " + data));
		
		progressTracker.addProgress(1);
	}

	/**
	 * Asynchronously writes changes to the given PO and all its items to the database
	 */
	async savePOToDB(progressTracker, po)
	{
		if (po.ModifiedOnly)
			progressTracker.addMax(1);
		for (let i = 0; i < po._items.length; i++) {
			if (po._items[i].Modified)
				progressTracker.addMax(1);
		}

		if (po.ModifiedOnly)
		{
			// Send request to delete PO and wait for response
			var url = "db/updatePO.php";
			//console.log("savePOToDB("+po.$mobx.name+"): " + url);
			var data = await $.ajax(
				{
					url: url,
					dataType: 'json',
					method: 'POST',
					data: po.JSONObject
				});

			// If deletion of PO was successful, remove from memory...
			if (data && data.msg == "OK")
			{
				//console.log("savePOToDB("+po.$mobx.name+"): Updated POID=" + po.POID + ", PONUMBER=" + po.PONUMBER + " in database");
				po.setStatus(MODEL_STATES.SAVED);
			}
			else
			{
				if (data && data.msg && data.msg.toLowerCase().includes("duplicate"))
					throw("This PONUMBER is already in use");
				throw("savePOToDB("+po.$mobx.name+"): Delete failed: " + (data && data.msg ? data.msg : "Unknown " + data));
			}
			progressTracker.addProgress(1);
		}

		var promises = [];
		for (let i = 0; i < po._items.length; i++) {
			if (po._items[i].DeletedOnly)
				promises.push(this.deleteItemFromDB(progressTracker, po._items[i]));
			else if (po._items[i].New)
				promises.push(this.addItemToDB(progressTracker, po._items[i]));
			else if (po._items[i].ModifiedOnly)
				promises.push(this.saveItemToDB(progressTracker, po._items[i]));
		}
			
		await Promise.all(promises);
		po.resortItems();
	}

	async addItemToDB(progressTracker, item)
	{
		// Send request to add PO and wait for response
		var url = "db/createItem.php";
		//console.log("addItemToDB("+item.$mobx.name+"): " + url);
		var data = await $.ajax(
			{
				url: url,
				dataType: 'json',
				method: 'POST',
				data: item.JSONObject
			});

		// If create of item was successful, we are done
		if (data && data.msg == "OK")
		{
			item.PODescriptionID = data.PODescriptionID;
			item.setStatus(MODEL_STATES.SAVED);

			//console.log("addItemToDB("+item.$mobx.name+"): Created PODescriptionID=" + data.PODescriptionID + " in database");
		}
		else
			throw("addItemToDB("+item.$mobx.name+"): Delete failed: " + (data && data.msg ? data.msg : "Unknown " + data));

		progressTracker.addProgress(1);
	}

	/**
	 * Asynchronously writes changes to the given item to the database
	 */
	async saveItemToDB(progressTracker, item)
	{
		// Send request to delete PO and wait for response
		var url = "db/updateItem.php";
		//console.log("saveItemToDB("+item.$mobx.name+"): " + url);
		var data = await $.ajax(
			{
				url: url,
				dataType: 'json',
				method: 'POST',
				data:item.JSONObject
			});

		// If deletion of PO was successful, remove from memory...
		if (data && data.msg == "OK")
		{
			//console.log("saveItemToDB("+item.$mobx.name+"): Updated PODescriptionID=" + item.PODescriptionID + " in database");
			item.setStatus(MODEL_STATES.SAVED);
		}
		else
			throw("saveItemToDB("+item.$mobx.name+"): Delete failed: " + (data && data.msg ? data.msg : "Unknown " + data));

		progressTracker.addProgress(1);
	}

	/**
	 * Asynchronously deletes the given item from the database and from the child array of the owning PO
	 */
	async deleteItemFromDB(progressTracker, item)
	{
		// Send request to delete item and wait for response
		var url = "db/deleteItem.php?id="+item.PODescriptionID;
		//console.log("deleteItemFromDB("+item.$mobx.name+"): " + url);
		var data = await $.ajax(
			{
				url: url,
				dataType: 'json'
			});

		// If deletion of item was successful, remove from memory...
		if (data && data.msg == "OK")
		{
			//console.log("deleteItemFromDB("+item.$mobx.name+"): Deleted PODescriptionID=" + item.PODescriptionID + " from database");
			item.po.removeItem(item);
		}
		else
			throw("deleteItemFromDB("+item.$mobx.name+"): Delete failed: " + (data && data.msg ? data.msg : "Unknown " + data));

		progressTracker.addProgress(1);
	}

	@action removePO(po)
	{
		this._pos.remove(po);
	}

	/**
	 * Asynchronously deletes the all the items for the given PO from the database and from the child array of the owning PO
	 */
	async deleteItemsFromDB(progressTracker, po)
	{
		// Send request to delete items and wait for response
		var url = "db/deleteItems.php?id="+po.POID;
		//console.log("deleteItemsFromDB("+po.$mobx.name+"): " + url);
		var data = await $.ajax(
			{
				url: url,
				dataType: 'json'
			});

		// If deletion of items was successful, remove from memory...
		if (data && data.msg == "OK")
		{
			//console.log("deleteItemsFromDB("+po.$mobx.name+"): Deleted POID=" + po.POID + " from database");
			po.removeAllItems();
		}
		else
			throw("deleteItemsFromDB("+po.$mobx.name+"): Delete failed: " + (data && data.msg ? data.msg : "Unknown " + data));

		progressTracker.addProgress(1);
	}

	@computed get asString() {
		var s = "";
		if (!this.POS) 
			s += "	ProductionOrders: No data found!\n";
		else
			s += `	ProductionOrders: ${this.POS.length} PO's\n`;
		return s;
	}

}

var poStore = new ProductionOrderStore();

export default poStore;