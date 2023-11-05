import React from "react"
import { observer } from 'mobx-react'
import POField from './POField'

@observer
export default class PODropdown extends POField {
	render() {
		var owner = this.getOwner();
		var prop = this.props.prop;
		var items = this.props.items || []; // Expect [{value:1, name:"Display Name"}, 	.]
		var valueIsName = this.props.valueIsName != "false" && this.props.valueIsName != false;
		var allowEmpty = this.props.allowEmpty == "true" || this.props.allowEmpty == true;
		var emptyVal = this.props.emptyVal;
		var emptyName = this.props.emptyName;
		var val = owner[prop];

		if (allowEmpty)
		{
			items = items.slice(0);
			items.splice(0, 0, {name: emptyName, value: emptyVal});
		}

		if (val == null || val=="")
		{
			val = valueIsName ? items[0].name : items[0].value;
			// We should probably update owner[prop] if allowEmpty is false;
		}

		if (!this.isReadOnly)
		{
			var options = items.map(i => <option key={i.value} value={valueIsName?i.name:i.value}>{i.name}</option>);

			return (
				<select value={val} onChange={this.onChange.bind(this)} className={prop+"_Field"}>
					{options}
				</select>
			)
		}
		else{
			if (!valueIsName)
				for (const key in items) {
					if (items.hasOwnProperty(key)) {
						const element = items[key];
						if (element.value == val)
						{
							val = element.name;
							break;
						}
					}
				}
			return (
				<span className={prop+"_Field"}>{val}</span>
			)
		}
	}
}