import React from "react"
import { observer } from 'mobx-react'
import POField from './POField'
import { MODEL_STATES } from '../../models/ModelBase'

@observer
export default class PODate extends POField {
	addDatePicker() {
		var field = this;
		$(this.refs.input).datepicker({
			dateFormat: "mm/dd/yy",
			showAnim: "slideDown",
			showOn: "both",
			buttonImage: "../../theme/eldy/img/object_calendarday.png",
			buttonImageOnly: true,
			buttonText: "Select date",
			onSelect: function(dateText) {
				let d = PODate.string2date(dateText);
				let s = PODate.dateToDBString(d);
				field.getOwner().setProperty(field.props.prop, s);
			}
		  });
	  }
	
	  componentWillUnmount() {
		$(this.refs.input).datepicker('destroy');
	  }

	render() {
		var owner = this.getOwner();
		var prop = this.props.prop;
		var val = owner[prop];

		if (val == null)
			val = "";
		else
		{
			// Standardize the date format to mm/dd/yyyy
			var d = PODate.string2date(val);
			val = PODate.dateToString(d);
		}

		if (!this.isReadOnly)
		{
			var className="text100";
			var size=this.props.size>0?this.props.size:"";

			if (size!="")
				className = "";
			var nowrap = {whiteSpace: "nowrap"};

			setTimeout(() => this.addDatePicker(), 0);
			
			return (
				<span style={nowrap}>
					<input type="text" ref="input" className={className} size={size} value={val} onChange={this.onChange.bind(this)} />
				</span>
			)
		}
		else{
			return (
				<span>{val}</span>
			)
		}
	}

	// Returns MM/DD/YYYY
	static dateToString(d)
	{
		return PODate.to2Digits(d.getMonth()+1)+"/"+PODate.to2Digits(d.getDate())+"/"+d.getFullYear();
	}

	// Returns "YYYY-MM-DD"
	static dateToDBString(d)
	{
		return d.getFullYear()+"-"+PODate.to2Digits(d.getMonth()+1)+"-"+PODate.to2Digits(d.getDate());
	}

	// Expects MM/DD/YYYY
	static string2date(s)
	{
		if (s.search(/^[0-9]{2}\/[0-9]{2}\/[0-9]{4}/i) == 0)
			return new Date(s.substring(6,10), s.substring(0,2)-1, s.substring(3,5));
		else if (s.search(/^[0-9]{4}-[0-9]{2}-[0-9]{2}/i) == 0)
			return new Date(s.substring(0,4), s.substring(5,7)-1, s.substring(8,10));
		else
			return new Date(s);
	}

	static to2Digits(n)
	{
		if (n<10)
			return "0"+n;
		return n.toString();
	}
}