import { combineReducers } from 'redux';
import GetOptions from './reducer_get_options';

const rootReducer = combineReducers({
  options: GetOptions
});

export default rootReducer;
