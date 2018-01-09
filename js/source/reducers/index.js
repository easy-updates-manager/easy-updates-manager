import { combineReducers } from 'redux';
import GetOptions from './options';

const rootReducer = combineReducers({
  options: GetOptions
});

export default rootReducer;
