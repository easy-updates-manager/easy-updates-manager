import React from 'react';
import {render} from 'react-dom';
import ToggleWrapper from './togglewrapper.jsx';
import EUMActionTypes from '../data/EUMActionTypes.jsx';
import EUMDispatcher from '../data/EUMDispatcher.jsx';
import EventEmitter from 'Event-Emitter';
import update from "immutability-helper";
import LoadingGif from './loading.jsx';
import RatingsNag from './ratingsnag.jsx';
import TrackingNag from './trackingnag.jsx';


var _storeJSON = null;

var loadJSON = function( data ) {
	_storeJSON = data;
}


var EUMStore = update(EventEmitter.prototype, { 
	$merge: {
		getJSON: function() {
			return _storeJSON;
		},
		emitChange: function() {
			this.emit( 'change' );
		},
		addChangeListener: function( callback ) {
			this.on( 'change', callback );
		},
		removeChangeListener: function( callback ) {
			this.removeListener( 'change',callback );
		}
	}
} );

EUMDispatcher.register( function( action ) {
	switch( action.type ) {
		case EUMActionTypes.ITEM_TOGGLE:
			let xhr = new XMLHttpRequest();
			xhr.open( 'POST', ajaxurl );
			xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
			xhr.onload = function() {
				if ( xhr.status === 200 ) {
					let json = JSON.parse( xhr.response );
					_storeJSON = json;
					
					EUMStore.emitChange();
				}	
			};
			xhr.onload = xhr.onload.bind(this);
			xhr.send(
				'action=mpsum_ajax_action' +
				'&_ajax_nonce=' + mpsum.admin_nonce +
				'&context=' + action.context +
				'&data_action=' + action.action +
				'&value=' +  action.value +
				'&id=' + action.id
			);
			break;
	}
	return true;
} );

var getState = function() {
	return _storeJSON;
}

var initState = function() {
	let xhr = new XMLHttpRequest();
	xhr.open( 'POST', ajaxurl );
	xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
	xhr.onload = function() {
		if ( xhr.status === 200 ) {
			let json = JSON.parse( xhr.response );
			_storeJSON = json;
			EUMStore.emitChange();
		}	
	};
	xhr.onload = xhr.onload.bind(this);
	xhr.send(
		'action=mpsum_ajax_get_json' +
		'&_ajax_nonce=' + mpsum.admin_nonce
	);
}


class App extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			options: [],
			loading: true	
		};
		this._onChange = this._onChange.bind(this);
	}
	_onChange() {
		this.setState( { options: getState(), loading: false } );
	}
	createWrapper( title, items ) {
		return <ToggleWrapper class="" title={title} items={items} key={title} />
	}
	createWrappers( data ) {
		if ( this.state.loading ) {
			return (
				<div id="eum-dashboard-loading">
					<LoadingGif />
				</div>
			)
		}
		let wrappers = [];
		if ( data.length > 0 ) {
			for( var value of data ) {
				wrappers.push( this.createWrapper( value.title, value.items ) );
			}	
		}
		return wrappers;
		
	}
	componentDidMount() {
		EUMStore.addChangeListener(this._onChange);
		initState();
	}
	componentWillUnmount() {
		EUMStore.removeChangeListener(this._onChange);
	}
	render() {
		return (
			<div>
				<TrackingNag />
				<div id="eum-dashboard-wrappers">
					{this.createWrappers(this.state.options)}
				</div>
			</div>	
		);
	}
}
let appContainer = document.getElementById('eum-dashboard-app');
if ( null !== appContainer ) {
	render(
		<App />, 
		appContainer
	);
}