import React from "react"
import { observer } from 'mobx-react'
import POField from './POField'

@observer
export default class PONumber extends POField {
	render() {
		var owner = this.getOwner();
		var prop = this.props.prop;
		var val = owner[prop] == null ? "0" : owner[prop];
		var currencySymbol = this.props.currency ? "$" : "";

		if (!this.isReadOnly)
		{
			var className="text100";
			var size=this.props.size>0?this.props.size:"";

			if (size!="")
				className = "";

			return (
				<input type="text" className={className} size={size} value={val} onChange={this.onChange.bind(this)} onBlur={this.onBlur.bind(this)} />
			)
		}
		else{
			val=PONumber.toFixedPrecisionString(val, this.props.precision);

			return (
				<span onDoubleClick={this.props.onDoubleClick}>{currencySymbol}{val}</span>
			)
		}
	}

	static isNumber(n) {
		return !isNaN(parseFloat(n)) && isFinite(n);
	}

	static toFixedPrecisionString(val, precision)
	{
		if (val == null || !PONumber.isNumber(val))
			val = 0;
		else if (typeof val == "string")
			val = parseFloat(val);

		// Set precision. 3 for floats by default.
		precision = precision > 0 ? precision : -1;
		if (precision == -1)
		{
			if (val != Math.floor(val))
				precision = 3;
			else
				precision = 0;
		}
		return val.toFixed(precision);
	}

	toFixedPrecisionNumber(val, precision)
	{
		return PONumber.toFixedPrecisionString(val, precision);
	}

	onBlur(e) {
		var owner = this.getOwner();
		var prop = this.props.prop;
		var val = owner[prop];
		val=this.toFixedPrecisionNumber(val, this.props.precision);
		owner.setProperty(this.props.prop, val);
	}
}