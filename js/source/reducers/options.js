import { FETCH_OPTIONS } from '../actions/get_options';
import { SAVE_OPTIONS } from '../actions/save_options';

export default function( state = [], action ) {
	switch( action.type ) {
		case FETCH_OPTIONS:
			return action.payload.data;
			break;
		case SAVE_OPTIONS:
			return action.payload.data;
			break;
	}
	return state;
}
