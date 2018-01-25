import React, { Component, Fragment } from 'react';
import ReactDOM from 'react-dom';
import axios from 'axios';
import { createStore, applyMiddleware } from 'redux';
import reducers from './reducers';
import Main from './containers/main_container';
import ReduxPromise from 'redux-promise';
import { Provider } from 'react-redux';
import 'raf/polyfill'; // For IE9

const createStoreWithMiddleware = applyMiddleware(ReduxPromise)(createStore);

class App extends Component {
	render() {
		return (
			<Fragment>
				<Main />
			</Fragment>
		);
	}
}
let dashboardApp = document.querySelector( '.eum-dashboard-app' );
if ( null !== dashboardApp ) {
	ReactDOM.render(
		<Provider store={createStoreWithMiddleware(reducers)}>
			<App />
		</Provider>
		,dashboardApp
	);
}
