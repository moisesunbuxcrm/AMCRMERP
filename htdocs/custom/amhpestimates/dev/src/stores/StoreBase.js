import {action, computed, extendObservable } from 'mobx';

const STORE_STATES = {
	INITIALIZING: "INITIALIZING",
	READY: "READY",
	BUSY: "BUSY",
	ERROR: "ERROR"
};

class StoreBase {
	constructor() {
		extendObservable(this, {
			_status: STORE_STATES.INITIALIZING,
			pendingFetch: null,
			statusDescription: null,
		});
	}

	getStatus()
	{
		return this._status;
	}
	
	@computed get BUSY()
	{
		return this._status == STORE_STATES.BUSY || this._status == STORE_STATES.INITIALIZING;
	}

	@action setStatus(newStatus, newStatusDescription, newPendingFetch)
	{
		this._status = newStatus;
		if (newStatusDescription)
			this.statusDescription = newStatusDescription;
		else
			this.statusDescription = null;

		if (newPendingFetch)
			this.pendingFetch = newPendingFetch;
		else
			this.pendingFetch = null;
		//console.log(this.$mobx.name + " -> " + newStatus);
	}

}

export { STORE_STATES, StoreBase} ;