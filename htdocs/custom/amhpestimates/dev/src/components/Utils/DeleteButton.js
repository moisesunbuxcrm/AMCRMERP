import React from "react"
import { observer } from 'mobx-react'
import { STORE_STATES } from '../../stores/StoreBase';

@observer
export default class DeleteButton extends React.Component {
	render() {
		var store = this.props.store;
		var onClick = this.props.onClick;

		var po = store.CurrentProductionOrder;

		if (po != null)
			return <div className="inline-block divButAction"><a className="butActionDelete" onClick={onClick}>Delete</a></div>;

		return <div className="inline-block divButAction"><a className="butActionRefused">Delete</a></div>;
	}
}
