import React, { Component, Fragment } from 'react';
import { getOptions } from '../actions/get_options';
import { saveOptions } from '../actions/save_options';
import { connect } from 'react-redux';
import AutomaticUpdates from './automatic_updates';
import DisableUpdates from './disable_updates';
import Logs from './logs';
import BrowserNag from './browser_nag';
import VersionFooter from './version_footer';
import Emails from './emails';
import CoreUpdates from './core-updates';
import PluginUpdates from './plugin_updates';
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
				<Fragment>
					<DisableUpdates />
					<AutomaticUpdates />
					<Logs />
					<Emails />
					<CoreUpdates />
					<PluginUpdates />
					<BrowserNag />
					<VersionFooter />
				</Fragment>
			);
		}


	}
}

function mapStateToProps(state) {
	return { options: state.options };
}

export default connect( mapStateToProps, { getOptions, saveOptions } )(Main);
