import EUMActionTypes from './EUMActionTypes.jsx';
import EUMDispatcher from './EUMDispatcher.jsx';

const Actions = {
	itemToggle( context, action, value, id ) {
		EUMDispatcher.dispatch({
			type: EUMActionTypes.ITEM_TOGGLE,
			context,
			action,
			value,
			id
		});
	}
}

export default Actions;