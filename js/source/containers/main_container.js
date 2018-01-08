import React, { Component } from 'react';
import { getOptions } from '../actions/get_options';
import { connect, Provider } from 'react-redux';
import ReduxPromise from 'redux-promise';
import AutomaticUpdates from './automatic_updates';

class Main extends Component {
	constructor( props ) {
		super( props );
	}
	componentDidMount() {
		this.props.getOptions();
	}
	render() {
		return (
			<AutomaticUpdates {...this.props}/>
		);

	}
}

function mapStateToProps(state) {
	return { options: state.options };
}

export default connect( mapStateToProps, { getOptions } )(Main);
