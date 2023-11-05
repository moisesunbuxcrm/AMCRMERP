import React from "react"
import { observer } from 'mobx-react'
import POField from './POField'

@observer
export default class POPhone extends POField {
	render() {
		var owner = this.getOwner();
		var prop = this.props.prop;
		var val = owner[prop];

		if (val == null)
			val = "";

		if (!this.isReadOnly)
		{
			var className="text100";
			var size=this.props.size>0?this.props.size:"";

			if (size!="")
				className = "";

			return (
				<input type="text" className={className} data-mask="(000) 000-0000" size={size} value={val} onChange={this.onChange.bind(this)} />
			)
		}
		else{
			return (
				<span data-mask="(000) 000-0000">{val}</span>
			)
		}
	}

	componentDidMount()
	{
		// Format any fields in app marked with "data-mask" attribute from jQuery.mask
		$.applyDataMask();
	}
}