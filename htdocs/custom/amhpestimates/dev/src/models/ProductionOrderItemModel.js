import { action, computed, reaction, when, extendObservable } from 'mobx';
import { MODEL_STATES, ModelBase } from './ModelBase';

export default class ProductionOrderItemModel extends ModelBase {
	constructor(data, initialStatus, isNew) {
		super();
		extendObservable(this, {
			po: null,
			"PODescriptionID": null,
			"POID": null,
			"LineNumber": null,
			"OPENINGW": null,
			"OPENINGHT": null,
			"TRACK": null,
			"TYPE": null,
			"BLADESQTY": null,
			"BLADESSTACK": null,
			"BLADESLONG": null,
			"LEFT": null,
			"RIGHT": null,
			"LOCKIN": null,
			"LOCKSIZE": null,
			"UPPERSIZE": null,
			"UPPERTYPE": null,
			"LOWERSIZE": null,
			"LOWERTYPE": null,
			"ANGULARTYPE": null,
			"ANGULARSIZE": null,
			"ANGULARQTY": null,
			"MOUNT": null,
			"ALUMINST": null,
			"LINEARFT" : null,
			"OPENINGHT4" : null,
			"ALUMINST4" : null,
			"EST8HT" : null,
			"ALUM" : null,
			"WINDOWSTYPE": null,
			"EXTRAANGULARTYPE": null,
			"EXTRAANGULARSIZE": null,
			"EXTRAANGULARQTY": null,
			"SQFEETPRICE": null,
			"PRODUCTTYPE": null,
			"COLOR": null,
			"MATERIAL": null,
			"PROVIDER": null,
			"INSTFEE": null,
			"TUBETYPE": null,
			"TUBESIZE": null,
			"TUBEQTY": null,
		});

		for(var p in data)
		{
			var pp = p.replace(" ", "_");
			if (pp in this)
				this[pp] = data[pp];
			//else console.log("Warning: Ignoring " + this.$mobx.name + "." + pp + "=" + data[pp] + " (PODescriptionID="+this["PODescriptionID"]+")");
		}

		if (initialStatus)
			this.setStatus(initialStatus);
		
		// call createReactions() only after this.po has been initialized
		when(() => this.po != null, () => this.createReactions(isNew));
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
				var sc = this.StackingChart;
				var sfp = this.toNum(this.StackingChart.SQFEETPRICE);
				return sfp;
			},
			val => this.setProperty("SQFEETPRICE", val), // Sets state to modified if appropriate
			isNew
		);
	
		reaction(
			() => {
				var OPENINGHT = this.toNum(this.OPENINGHT);
				var ESTHTVALUE = this.toNum(this.po?this.po.ESTHTVALUE:0);
				if (OPENINGHT == 0) {
					return 0;
				}
				else{
					return OPENINGHT + ESTHTVALUE;
				}
			},
			val => this.setProperty("EST8HT", val), // Sets state to modified if appropriate
			isNew
		);
	
		reaction(
			() => {
				var nextMO = this.MONumber;
				var sd = this.StackingData;
				var data = nextMO ? sd[nextMO.toString()] : null;
				return data ? data.track : 0;
			},
			val => this.setProperty("TRACK", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var nextMO = this.MONumber;
				var data = nextMO ? this.StackingData[nextMO.toString()] : null;
				if (data)
					return data.blades;
				return 0;
			},
			val => this.setProperty("BLADESQTY", val), // Sets state to modified if appropriate
			isNew
		);
	
		reaction(
			() => {
				var nextMO = this.MONumber;
				var data = nextMO ? this.StackingData[nextMO.toString()] : null;
				if (data)
					return data.stack;
				return 0;
			},
			val => this.setProperty("BLADESSTACK", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var EST8HT = this.toNum(this.EST8HT);
				var OPENINGHT = this.toNum(this.OPENINGHT);
		
				if (this.po.TYPE == "PANELS")
					return EST8HT;
				
				var MOUNT = this.MOUNT;
				if (!MOUNT)
					MOUNT = "WW";
		
				switch(MOUNT)
				{
					case "CW":
					case "WFL":
						return OPENINGHT;
						break;
					case "WW":
						return OPENINGHT + 2;
						break;
					default:
						switch(this.TYPE)
						{
							case "WO":
								return OPENINGHT - 3.25;
								break;
							case "ADJ":
								return OPENINGHT - 3.5;
								break;
							default:
								return OPENINGHT + 2;
								break;
						}
						break;
				}
			},
			val => this.setProperty("BLADESLONG", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var BLADESQTY = this.toNum(this.BLADESQTY);
				var left = BLADESQTY/2.0;
				var floorLeft = Math.floor(left);
				if (left != floorLeft)
				{
					if (floorLeft%2 == 1)
						left = floorLeft + 1;
					else
						left = floorLeft;
				}
				return left; // LEFT should be EVEN and RIGHT should be ODD
			},
			val => this.setProperty("LEFT", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var BLADESQTY = this.toNum(this.BLADESQTY);
				var LEFT = this.toNum(this.LEFT);
				return BLADESQTY-LEFT;
			},
			val => this.setProperty("RIGHT", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var TRACK = this.toNum(this.TRACK);
				return TRACK;
			},
			val => {
				this.setProperty("UPPERSIZE", val); // Sets state to modified if appropriate
				this.setProperty("LOWERSIZE", val); // Sets state to modified if appropriate
			},
			isNew
		);
	
		reaction(
			() => {
				var BLADESLONG = this.toNum(this.BLADESLONG);
				switch(this.ANGULARTYPE)
				{
					case "1x3":
						return BLADESLONG + 3;
						break;
					case "2x5":
					case "2x7":
						return BLADESLONG + 3.5;
						break;
					default:
						return 0;
						break;
				}
			},
			val => this.setProperty("ANGULARSIZE", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var BLADESLONG = this.toNum(this.BLADESLONG);
				var BLADESQTY = this.toNum(this.BLADESQTY);
				return this.round((BLADESLONG * BLADESQTY / 12),3);
			},
			val => this.setProperty("LINEARFT", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var OPENINGHT = this.toNum(this.OPENINGHT);
				var poHTVALUE = this.toNum(this.po.HTVALUE);
				return OPENINGHT == 0 ? 0 : OPENINGHT + poHTVALUE;
			},
			val => this.setProperty("OPENINGHT4", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var TRACK = this.toNum(this.TRACK);
				var OPENINGHT4 = this.OPENINGHT4;
				return this.round((TRACK * OPENINGHT4 / 144),3);
			},
			val => this.setProperty("ALUMINST4", val), // Sets state to modified if appropriate
			isNew
		);

		reaction(
			() => {
				var EST8HT = this.toNum(this.EST8HT);
				var TRACK = this.toNum(this.TRACK);
				return Math.ceil(EST8HT * TRACK / 144);
			},
			val => this.setProperty("ALUM", val), // Sets state to modified if appropriate
			isNew
		);
	}

	@computed get ALUMPRICE() 
	{
		return this.toNum(this.ALUM) * this.toNum(this.SQFEETPRICE);
	}

	@computed get MONumber() 
	{
        // Get the lowest MO that is >= OPENINGW
		var mo = this.toNum(this.OPENINGW);
		var sd = this.StackingData;
        var nextMO = sd ? this.getNextMO(sd, mo) : null;
        return nextMO;
	}
	
	@computed get StackingChart()
	{
		var sc = estimateData.stackingcharts.find(c => c.value == this.PROVIDER);
		return sc;
	}

	@computed get StackingData()
	{
		var chartName = this.StackingChart ? this.StackingChart.name : null;
		return chartName ? estimateData.stackingdata[chartName] : null;
	}

	getNextMO(stackingData, mo) {
		// Find the lowest MO in the stacking data that is >= mo
		var lastMo = 0;
		var keys = [];
		for (let key in stackingData)
			keys.push(parseFloat(key));
		keys = keys.sort(function sortNumber(a,b) { return a - b; });
		for (let i = 0; i < keys.length; i++) {
			lastMo = keys[i];
			if (lastMo >= mo)
				return lastMo;
		}
		return lastMo;
	}

	@computed get JSONObject()
	{
		return {
			"PODescriptionID" : this.PODescriptionID,
			"POID" : this.po ? this.po.POID : this.POID,
			"LineNumber" : this.LineNumber,
			"OPENINGW" : this.OPENINGW,
			"OPENINGHT" : this.OPENINGHT,
			"TRACK" : this.TRACK,
			"TYPE" : this.TYPE,
			"BLADESQTY" : this.BLADESQTY,
			"BLADESSTACK" : this.BLADESSTACK,
			"BLADESLONG" : this.BLADESLONG,
			"LEFT" : this.LEFT,
			"RIGHT" : this.RIGHT,
			"LOCKIN" : this.LOCKIN,
			"LOCKSIZE" : this.LOCKSIZE,
			"UPPERSIZE" : this.UPPERSIZE,
			"UPPERTYPE" : this.UPPERTYPE,
			"LOWERSIZE" : this.LOWERSIZE,
			"LOWERTYPE" : this.LOWERTYPE,
			"ANGULARTYPE" : this.ANGULARTYPE,
			"ANGULARSIZE" : this.ANGULARSIZE,
			"ANGULARQTY" : this.ANGULARQTY,
			"MOUNT" : this.MOUNT,
			"ALUMINST" : this.ALUMINST,
			"LINEARFT" : this.LINEARFT,
			"OPENINGHT4" : this.OPENINGHT4,
			"ALUMINST4" : this.ALUMINST4,
			"EST8HT" : this.EST8HT,
			"ALUM" : this.ALUM,
			"WINDOWSTYPE" : this.WINDOWSTYPE,
			"EXTRAANGULARTYPE" : this.EXTRAANGULARTYPE,
			"EXTRAANGULARSIZE" : this.EXTRAANGULARSIZE,
			"EXTRAANGULARQTY" : this.EXTRAANGULARQTY,
			"SQFEETPRICE" : this.SQFEETPRICE,
			"PRODUCTTYPE" : this.PRODUCTTYPE,
			"COLOR": this.COLOR,
			"MATERIAL": this.MATERIAL,
			"PROVIDER": this.PROVIDER,
			"INSTFEE": this.INSTFEE,
			"TUBETYPE" : this.TUBETYPE,
			"TUBESIZE" : this.TUBESIZE,
			"TUBEQTY" : this.TUBEQTY,
		};
	}

	round(val, places)
	{
		var pow = Math.pow(10,places);
		return Math.round(val * pow) / pow;
	}

	@action
	setProperty(prop, val)
	{
		if (this[prop] != val)
		{
			this[prop] = val;
			this.setStatus(MODEL_STATES.MODIFIED);
		}
	}

	@action delete() {
		this.setStatus(MODEL_STATES.DELETED);
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
}