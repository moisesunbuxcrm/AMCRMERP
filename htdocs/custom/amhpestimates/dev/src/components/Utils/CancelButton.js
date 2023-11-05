import React from "react"
import { observer } from 'mobx-react'
import { STORE_STATES } from '../../stores/StoreBase';

@observer
export default class CancelButton extends React.Component {
	render() {
		var store = this.props.store;
		var readOnly = this.props.readOnly;
		var disabled = readOnly;

		var button = null;
		if (disabled)
			button = <div className="inline-block divButAction"><a className="butActionRefused">Cancel</a></div>;
		else
			button = <div className="inline-block divButAction"><a className="butAction" onClick={this.cancel.bind(this)}>Cancel</a></div>;

		return button;
	}

	cancel() {
		if (this.props.onClick)
			this.props.onClick();
		this.props.store.cancelChanges();
	}
}
