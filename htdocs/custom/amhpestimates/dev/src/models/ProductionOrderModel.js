import { action, computed, reaction, extendObservable } from 'mobx';
import ProductionOrderItemModel from './ProductionOrderItemModel';
import { MODEL_STATES, ModelBase } from './ModelBase';

export default class ProductionOrderModel extends ModelBase {
	constructor(data, initialStatus, isNew) {
		super();
		extendObservable(this, {
			_items: [],

			"POID": null,
			"PONUMBER": null,
			"PODATE": null,
			"QUOTEDATE": null,
			"Salesman": null,
			"CUSTOMERNAME": null,
			"CONTACTNAME": null,
			"CONTACTPHONE1": null,
			"CONTACTPHONE2": null,
			"CUSTOMERADDRESS": null,
			"ZIPCODE": null,
			"CITY": null,
			"STATE": null,
			"PHONENUMBER1": null,
			"PHONENUMBER2": null,
			"FAXNUMBER": null,
			"EMail": null,
			"COLOR": null,
			"HTVALUE": null,
			"DESCRIPTIONOFWORK": null,
			"OBSERVATION": null,
			"TOTALTRACK" : null,
			"TAPCONS" : null,
			"TOTALLONG" : null,
			"FASTENERS" : null,
			"TOTALALUMINST" : null,
			"TOTALLINEARFT" : null,
			"OBSINST": null,
			"SQINSTPRICE": null,
			"INSTSALESPRICE": null,
			"ESTHTVALUE": null,
			"ESTOBSERVATION": null,
			"INSTTIME": null,
			"PERMIT": null,
			"CUSTVALUE": null,
			"CUSTOMIZE": null,
			"SALES_TAX": null,
			"SALESTAXAMOUNT": null,
			"TOTALALUM": null,
			"SALESPRICE": null,
			"SQFEETPRICE": null,
			"OTHERFEES": null,
			"Check50": false,
			"CheckAssIns": false,
			"OrderCompleted": false,
			"Check10YearsWarranty": false,
			"Check10YearsFreeMaintenance": false,
			"CheckFreeOpeningClosing": false,
			"CheckNoPayment": false,
			"YearsWarranty": null,
			"LifeTimeWarranty": false,
			"SignatureReq": false,
			"Discount": null,
			"customerId": null,
			"invoiceId": null,
			"invoiceLocked": false,
			"permitId": null,
		});

		for(var p in data)
		{
			var pp = p.replace(" ", "_");
			switch(pp)
			{
				case "items":
					for(var i in data[pp])
					{
						var item = new ProductionOrderItemModel(data[pp][i], MODEL_STATES.SAVED, false);
						item.po = this;
						this._items.push(item);
					}
					break;
				default:
					if (pp in this)
						this[pp] = data[pp];
					//else console.log("Warning: Ignoring " + this.$mobx.name + "." + pp + "=" + data[pp] + " (POID="+this["POID"]+")");
			}
		}

		if (initialStatus)
			this.setStatus(initialStatus);
		
		this.createReactions(isNew);
	}

	/**
	 * These MOBX reactions automatically update properties when some other property changes
	 * They are not marked as @computed because they can also be modified directly by the user
	 * and because we do not want to overwrite manually entered values until the user changes something.
	 */
	createReactions(isNew)
	{
		reaction(
			() => {
				var SUM = 0;
				this.Items && this.Items.forEach(i => 
					SUM += this.toNum(i.TRACK));
				return this.round(SUM / 12 * 2, 3);
			},
			val => this.setProperty("TOTALTRACK", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var SUM_TRACK = 0, SUM_BLADESLONG = 0;
				this.Items && this.Items.forEach(i => {
					SUM_TRACK += this.toNum(i.TRACK);
					SUM_BLADESLONG += this.toNum(i.BLADESLONG);
				});
				var res = Math.ceil(SUM_TRACK * 2 / 8) +
					Math.ceil(SUM_BLADESLONG * 2 / 16);
				return res;
			},
			val => this.setProperty("TAPCONS", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var SUM = 0;
				this.Items && this.Items.forEach(i => 
					SUM += this.toNum(i.BLADESLONG));
				return this.round(SUM / 12 * 2, 3);
			},
			val => this.setProperty("TOTALLONG", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var SUM_BLADESLONG = 0;
				this.Items && this.Items.forEach(i => {
					SUM_BLADESLONG += this.toNum(i.BLADESLONG);
				});
				var res = Math.ceil(SUM_BLADESLONG * 2 / 16);
				return res;
			},
			val => this.setProperty("FASTENERS", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var SUM = 0;
				this.Items && this.Items.forEach(i => 
					SUM += this.toNum(i.ALUMINST4));
				return this.round(SUM, 3);
			},
			val => this.setProperty("TOTALALUMINST", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var SUM = 0;
				this.Items && this.Items.forEach(i => 
					SUM += this.toNum(i.LINEARFT));
				return this.round(SUM, 3);
			},
			val => this.setProperty("TOTALLINEARFT", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var SUM = 0;
				this.Items && this.Items.forEach(i => 
					SUM += this.toNum(i.ALUM));
				return this.round(SUM, 3);
			},
			val => this.setProperty("TOTALALUM", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var SQINSTPRICE = this.toNum(this.SQINSTPRICE);
				var TOTALALUMINST = this.toNum(this.TOTALALUMINST);
				return this.round(TOTALALUMINST * SQINSTPRICE, 3);
			},
			val => this.setProperty("INSTSALESPRICE", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var CUSTVALUE = this.toNum(this.CUSTVALUE);
				var SALES_TAX = this.toNum(this.SALES_TAX);
				var Discount = this.toNum(this.Discount);
				var TOTALALUMPRICE = this.toNum(this.TOTALALUMPRICE);
				return this.round((TOTALALUMPRICE + CUSTVALUE - Discount) * (SALES_TAX / 100),3);
			},
			val => this.setProperty("SALESTAXAMOUNT", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var CUSTVALUE = this.toNum(this.CUSTVALUE);
				var PERMIT = this.toNum(this.PERMIT);
				var CUSTOMIZE = this.toNum(this.CUSTOMIZE);
				var OTHERFEES = this.toNum(this.OTHERFEES);
				var TOTALALUMPRICE = this.toNum(this.TOTALALUMPRICE);
				var SALESTAXAMOUNT = this.toNum(this.SALESTAXAMOUNT);
				var TOTALINSTFEE = this.toNum(this.TOTALINSTFEE);
				var Discount = this.toNum(this.Discount);
				var sum = TOTALALUMPRICE + CUSTVALUE - Discount + PERMIT + CUSTOMIZE + OTHERFEES + SALESTAXAMOUNT + TOTALINSTFEE;
				return this.round(sum, 3);
			},
			val => this.setProperty("SALESPRICE", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				return this.LifeTimeWarranty;
			},
			val => {
				if (val) 
					this.setProperty("Check10YearsWarranty", !val); 
			}, // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				return this.Check10YearsWarranty;
			},
			val => {
				if (val) 
					this.setProperty("LifeTimeWarranty", !val);
			}, // Sets state to modified if appropriate
			isNew
		);

		// If the colour is changed in the Estimate then update all the line items/rows
		reaction(
			() => {
				return this.COLOR;
			},
			val => {
				if (this.Items)
					this.Items.forEach(i => i.setProperty("COLOR", val));
			},
			isNew
		);

		// If the SQFEETPRICE is changed in the Estimate then update all the line items/rows
		reaction(
			() => {
				return this.SQFEETPRICE;
			},
			val => {
				var accordionId = 0;
				for(var i in estimateData.producttypes)
					if (estimateData.producttypes[i].name === "ACCORDION")
						accordionId = estimateData.producttypes[i].value;
				if (this.Items)
					this.Items.forEach(i => {
						if (i.PRODUCTTYPE == accordionId) 
							i.setProperty("SQFEETPRICE", val); 
					});
			},
			isNew
		);
	}

	@computed get TOTALALUMPRICE()
	{
		var SUM = 0;
		this.Items && this.Items.forEach(i => 
			SUM += this.toNum(i.ALUMPRICE));
		return this.round(SUM, 3);
	}

	@computed get TOTALINSTFEE()
	{
		var SUM = 0;
		this.Items && this.Items.forEach(i => 
			SUM += this.toNum(i.INSTFEE));
		return this.round(SUM, 3);
	}

	@computed get Items() { return this._items && this._items.filter(item => !item.Deleted); }
	@computed get SortedItems() { return this.Items.sort((a, b) => a.LineNumber - b.LineNumber); }
	@action resortItems() { this._items = this._items.sort((a, b) => a.LineNumber - b.LineNumber); }

	@computed get JSONObject()
	{
		return {
			"POID": this.POID,
			"PONUMBER": this.PONUMBER,
			"PODATE": this.PODATE,
			"QUOTEDATE": this.QUOTEDATE,
			"Salesman": this.Salesman,
			"CUSTOMERNAME": this.CUSTOMERNAME,
			"CONTACTNAME": this.CONTACTNAME,
			"CONTACTPHONE1": this.CONTACTNAME,
			"CONTACTPHONE2": this.CONTACTNAME,
			"CUSTOMERADDRESS": this.CUSTOMERADDRESS,
			"ZIPCODE": this.ZIPCODE,
			"CITY": this.CITY,
			"STATE": this.STATE,
			"PHONENUMBER1": this.PHONENUMBER1,
			"PHONENUMBER2": this.PHONENUMBER2,
			"FAXNUMBER": this.FAXNUMBER,
			"EMail": this.EMail,
			"COLOR": this.COLOR,
			"HTVALUE": this.HTVALUE,
			"DESCRIPTIONOFWORK": this.DESCRIPTIONOFWORK,
			"OBSERVATION": this.OBSERVATION,
			"TOTALTRACK" : this.TOTALTRACK,
			"TAPCONS" : this.TAPCONS,
			"TOTALLONG" : this.TOTALLONG,
			"FASTENERS" : this.FASTENERS,
			"TOTALALUMINST" : this.TOTALALUMINST,
			"TOTALLINEARFT" : this.TOTALLINEARFT,
			"OBSINST": this.OBSINST,
			"SQINSTPRICE": this.SQINSTPRICE,
			"INSTSALESPRICE": this.INSTSALESPRICE,
			"ESTHTVALUE": this.ESTHTVALUE,
			"ESTOBSERVATION": this.ESTOBSERVATION,
			"INSTTIME": this.INSTTIME,
			"PERMIT": this.PERMIT,
			"CUSTVALUE": this.CUSTVALUE,
			"CUSTOMIZE": this.CUSTOMIZE,
			"SALES_TAX": this.SALES_TAX,
			"SALESTAXAMOUNT": this.SALESTAXAMOUNT,
			"TOTALALUM": this.TOTALALUM,
			"SALESPRICE": this.SALESPRICE,
			"SQFEETPRICE": this.SQFEETPRICE,
			"OTHERFEES": this.OTHERFEES,
			"Check50": this.Check50,
			"CheckAssIns": this.CheckAssIns,
			"OrderCompleted": this.OrderCompleted,
			"Check10YearsWarranty": this.Check10YearsWarranty,
			"Check10YearsFreeMaintenance": this.Check10YearsFreeMaintenance,
			"CheckFreeOpeningClosing": this.CheckFreeOpeningClosing,
			"CheckNoPayment": this.CheckNoPayment,
			"YearsWarranty": this.YearsWarranty,
			"LifeTimeWarranty": this.LifeTimeWarranty,
			"SignatureReq": this.SignatureReq,
			"Discount": this.Discount,
			"customerId": this.customerId,
			"invoiceId": this.invoiceId,
			"invoiceLocked": this.invoiceLocked,
			"permitId": this.permitId,
		};
	}

	round(val, places)
	{
		var pow = Math.pow(10,places);
		return Math.round(val * pow) / pow;
	}

	toNum(str)
	{
		var val = 0;
		if (typeof str == "string")
			val = parseFloat(str);
		else if (typeof str == "number")
			val = str;
		if (isNaN(val))
			val = 0;
		return val;
	}

	@action
	setProperty(prop, val)
	{
		if (this[prop] != val)
		{
			this[prop] = val;
			if (!this.Modified)
				this.setStatus(MODEL_STATES.MODIFIED);
		}
	}

	@computed get anyChildrenModified() 
	{
		if (!this._items || !this._items.length)
			return false;

		var countModified = false;
		this._items.forEach(item => countModified += item.Modified ? 1 : 0); // Use forEach() to iterate all values without short circuit so that MobX sees dependence on all items´s
		return countModified > 0; 
	}
	
	@action delete() {
		this.setStatus(MODEL_STATES.DELETED);
	}

	@action newItem() {
		var lastLineNumber = 0;
		var items = this.SortedItems;
		if (items.length>0)
			lastLineNumber = parseInt(items[items.length-1].LineNumber);
		var newLineNumber = lastLineNumber+1;

		var lastPODescriptionID = 0;
		if (items.length>0)
			lastPODescriptionID = parseInt(items[items.length-1].PODescriptionID);
		var newPODescriptionID = lastPODescriptionID+1;

		var newItem = new ProductionOrderItemModel({
            "PODescriptionID": newPODescriptionID, // Temporary to ensure a unique key for React components
            "POID": this.POID,
            "LineNumber": newLineNumber,
            "TYPE": "STD",
            "UPPERTYPE": "STD",
            "LOWERTYPE": "STD",
            "MOUNT": "WW",
            "ANGULARTYPE": "1x3",
            "ANGULARSIZE": 0,
            "ANGULARQTY": "2",
            "EXTRAANGULARTYPE": "",
            "EXTRAANGULARSIZE": 0,
            "EXTRAANGULARQTY": 0,
            "LOCKIN": estimateData.getDefaultValue("LOCKIN", ""),
            "LOCKSIZE": estimateData.getDefaultValue("LOCKSIZE", ""),
            "PRODUCTTYPE": 1,
            "TRACK": 0,
            "BLADESQTY": 0,
			"BLADESSTACK": 0,
            "BLADESLONG": 0,
            "UPPERSIZE": 0,
            "LOWERSIZE": 0,
            "ALUMINST": 0,
            "COLOR": this.COLOR,
            "SQFEETPRICE": this.SQFEETPRICE,
            "MATERIAL": "ALUMINUM",
			"INSTFEE": 0,
			"WINDOWSTYPE": "WINDOW",
            "TUBETYPE": "",
            "TUBESIZE": 0,
			"TUBEQTY": 0
		}, null, true);

		// These assignments trigger the Mobx reactions which initialize the rest of the item
		newItem.PROVIDER = estimateData.getDefaultValue("PROVIDERID", "3");
		newItem.OPENINGHT = 0;
		newItem.OPENINGW = 0;

		newItem.po = this;
		this._items.push(newItem);
		return newItem;
	}

	duplicateItem(item) {
		var newItem = this.newItem();

		newItem.setProperty("PROVIDER", item.PROVIDER);
		newItem.setProperty("OPENINGW", item.OPENINGW);
		newItem.setProperty("OPENINGHT", item.OPENINGHT);
		newItem.setProperty("TRACK", item.TRACK);
		newItem.setProperty("TYPE", item.TYPE);
		newItem.setProperty("BLADESQTY", item.BLADESQTY);
		newItem.setProperty("BLADESLONG", item.BLADESLONG);
		newItem.setProperty("LEFT", item.LEFT);
		newItem.setProperty("RIGHT", item.RIGHT);
		newItem.setProperty("LOCKIN", item.LOCKIN);
		newItem.setProperty("LOCKSIZE", item.LOCKSIZE);
		newItem.setProperty("UPPERSIZE", item.UPPERSIZE);
		newItem.setProperty("UPPERTYPE", item.UPPERTYPE);
		newItem.setProperty("LOWERSIZE", item.LOWERSIZE);
		newItem.setProperty("LOWERTYPE", item.LOWERTYPE);
		newItem.setProperty("ANGULARTYPE", item.ANGULARTYPE);
		newItem.setProperty("ANGULARSIZE", item.ANGULARSIZE);
		newItem.setProperty("ANGULARQTY", item.ANGULARQTY);
		newItem.setProperty("MOUNT", item.MOUNT);
		newItem.setProperty("ALUMINST", item.ALUMINST);
		newItem.setProperty("WINDOWSTYPE", item.WINDOWSTYPE);
		newItem.setProperty("EXTRAANGULARTYPE", item.EXTRAANGULARTYPE);
		newItem.setProperty("EXTRAANGULARSIZE", item.EXTRAANGULARSIZE);
		newItem.setProperty("EXTRAANGULARQTY", item.EXTRAANGULARQTY);
		newItem.setProperty("PRODUCTTYPE", item.PRODUCTTYPE);
		newItem.setProperty("COLOR", item.COLOR);
		newItem.setProperty("MATERIAL", item.MATERIAL);
		newItem.setProperty("INSTFEE", item.INSTFEE);
		newItem.setProperty("SQFEETPRICE", item.SQFEETPRICE);
		newItem.setProperty("TUBETYPE", item.TUBETYPE);
		newItem.setProperty("TUBESIZE", item.TUBESIZE);
		newItem.setProperty("TUBEQTY", item.TUBEQTY);
	}

	@action removeItem(item)
	{
		this._items.remove(item);
	}

	@action removeAllItems()
	{
		this._items.clear();
	}

	@action setUnknownInvoice()
	{
		this.invoiceLocked = true;
		this.invoiceId = -1;  // We created an invoice but we do not know the ID yet.
	}
}