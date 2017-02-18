import React from 'react';
import {render} from 'react-dom';
import ToggleWrapper from './togglewrapper.jsx';
import EUMActionTypes from '../data/EUMActionTypes.jsx';
import EUMDispatcher from '../data/EUMDispatcher.jsx';
import EventEmitter from 'Event-Emitter';
import update from "immutability-helper";

var _storeJSON = mpsum.json_options;

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
				'&value=' +  action.value
			);
			break;
	}
	return true;
} );

var getState = function() {
	return EUMStore.getJSON();
}


class App extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			options: getState()	
		};
		this._onChange = this._onChange.bind(this);
	}
	_onChange() {
		this.setState( { options: getState() } );
	}
	createWrapper( title, items ) {
		return <ToggleWrapper class="" title={title} items={items} key={title} update={this.update} />
	}
	createWrappers( data ) {
		let wrappers = [];
		for( var value of data ) {
			wrappers.push( this.createWrapper( value.title, value.items ) );
		}
		return wrappers;
		
	}
	componentDidMount() {
		EUMStore.addChangeListener(this._onChange);
	}
	componentWillUnmount() {
		EUMStore.removeChangeListener(this._onChange);
	}
	render() {
		return (
			<div>
				{this.createWrappers(this.state.options)}
			</div>	
		);
	}
}
render(
	<App />, 
	document.getElementById('eum-dashboard-app')
);