import { action, extendObservable } from 'mobx';

export default class ProgressTracker {
	constructor() {
		extendObservable(this, {
			progress: 0,
			max: 0
		});
	}

	@action addProgress(add)
	{
		this.progress += add;
	}

	@action addMax(add)
	{
		this.max += add;
	}
}