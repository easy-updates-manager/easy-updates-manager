import { FETCH_OPTIONS } from '../actions/get_options';

export default function( state = [], action ) {
	switch( action.type ) {
		case FETCH_OPTIONS:
			return action.payload.data;
			break;
	}
	return state;
}
