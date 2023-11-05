import {action, computed, extendObservable } from 'mobx';

const MODEL_STATES = {
	SAVED: "SAVED",
	MODIFIED: "MODIFIED",
	NEW: "NEW",
	DELETED: "DELETED",
	THRASH: "THRASH", // State for a NEW object that is deleted before being saved
};

class ModelBase {
	constructor() {
		extendObservable(this, {
			_status: MODEL_STATES.NEW,
		});
	}

	getStatus()
	{
		return this._status;
	}

	@computed get Modified()
	{
		return [
			MODEL_STATES.MODIFIED, 
			MODEL_STATES.DELETED,
			MODEL_STATES.NEW].includes(this._status);
	}

	@computed get ModifiedOnly()
	{
		return this._status == MODEL_STATES.MODIFIED;
	}

	@computed get Deleted()
	{
		return [
			MODEL_STATES.DELETED,
			MODEL_STATES.THRASH].includes(this._status);
	}
	
	@computed get DeletedOnly()
	{
		return this._status == MODEL_STATES.DELETED;
	}

	@computed get New()
	{
		return this._status == MODEL_STATES.NEW;
	}

	@computed get Thrash()
	{
		return this._status == MODEL_STATES.THRASH;
	}

	@action setStatus(newStatus)
	{
		if (this._status == newStatus)
			return; // Nothing to do

		switch(this._status)
		{
			case MODEL_STATES.SAVED:
					switch(newStatus)
					{
						case MODEL_STATES.MODIFIED:
						case MODEL_STATES.DELETED:
							break;
						default:
							throw "Illegal attempt to change state from " + this._status + " -> " + newStatus;
							break;
					}
					break;
			case MODEL_STATES.MODIFIED:
					switch(newStatus)
					{
						case MODEL_STATES.SAVED:
						case MODEL_STATES.DELETED:
							break;
						default:
							throw "Illegal attempt to change state from " + this._status + " -> " + newStatus;
							break;
					}
					break;
			case MODEL_STATES.NEW:
					switch(newStatus)
					{
						case MODEL_STATES.MODIFIED:
							return; // Ignore, leave state as NEW
							break
						case MODEL_STATES.SAVED:
						case MODEL_STATES.THRASH:
							break
						case MODEL_STATES.DELETED:
							newStatus = MODEL_STATES.THRASH;
							break;
						default:
							throw "Illegal attempt to change state from " + this._status + " -> " + newStatus;
							break;
					}
				break;
			default:
				throw "Illegal attempt to change state from " + this._status + " -> " + newStatus;
		}
		//console.log(this.$mobx.name + " -> " + newStatus);
		this._status = newStatus;
	}
}

export { MODEL_STATES, ModelBase} ;