import EUMActionTypes from './EUMActionTypes.jsx';
import EUMDispatcher from './EUMDispatcher.jsx';

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