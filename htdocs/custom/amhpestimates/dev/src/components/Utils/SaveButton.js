import React from "react"
import { observer } from 'mobx-react'
import { STORE_STATES } from '../../stores/StoreBase';

@observer
export default class SaveButton extends React.Component {
	render() {
		var store = this.props.store;
		var modified = store.Modified;

		var button = null;
		if (modified)
			button = <div className="inline-block divButAction"><a className="butAction" onClick={this.save.bind(this)}>Save</a></div>;
		else
			button = <div className="inline-block divButAction"><a className="butActionRefused">Save</a></div>;

		return button;
	}

	save() {
		this.props.store.save().then(
			() => {
				var onSave = this.props.onSave;
				if (onSave)
					onSave();
			},
			(error) => {
			}
		);
	}
}
