import EUMActionTypes from './EUMActionTypes.js';
import EUMDispatcher from './EUMDispatcher.js';

const Actions = {
	itemToggle( context, action, value ) {
		EUMDispatcher.dispatch({
			type: EUMActionTypes.ITEM_TOGGLE,
			context,
			action,
			value
		});
	}
}

export default Actions;