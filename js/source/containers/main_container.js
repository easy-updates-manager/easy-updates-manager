import React, { Component } from 'react';
import { getOptions } from '../actions/get_options';
import { saveOptions } from '../actions/save_options';
import { connect, Provider } from 'react-redux';
import ReduxPromise from 'redux-promise';
import AutomaticUpdates from './automatic_updates';
import LoadingGif from '../components/loading';

class Main extends Component {
	constructor( props ) {
		super( props );
	}
	componentDidMount() {
		this.props.getOptions();
	}
	render() {
		if ( this.props.options.length === 0 ) {
			return (
				<LoadingGif />
			)
		} else {
			return (
				<AutomaticUpdates {...this.props}/>
			);
		}


	}
}

function mapStateToProps(state) {
	return { options: state.options };
}

export default connect( mapStateToProps, { getOptions, saveOptions } )(Main);
