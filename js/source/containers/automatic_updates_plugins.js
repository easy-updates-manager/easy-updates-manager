import React, { Component, Fragment } from 'react';
import LoadingGif from '../components/loading';
import { saveOptions } from '../actions/save_options';
import { connect } from 'react-redux';

class AutomaticUpdatesPlugins extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			loading: false
		};
	}

	componentWillReceiveProps() {
		this.setState( {
			loading: false
		} );
	}

	onButtonClick = ( event ) => {
		event.preventDefault();
		this.setState( {
			loading: true,
		} );

		this.props.saveOptions( event.target.getAttribute ( 'data-id' ), event.target.value );
	}

	render() {
		const { options } = this.props;
		return (
			<div className="eum-radio-group">
				<h2>{mpsum.I18N.automatic_plugin_updates}</h2>
				<p className="eum-description">{mpsum.I18N.automatic_plugin_updates_description}</p>
				<p className="eum-status">
					{ 'default' == options.automatic_plugin_updates &&
						mpsum.I18N.automatic_plugin_updates_default_status
					}
					{ 'on' == options.automatic_plugin_updates &&
						mpsum.I18N.automatic_plugin_updates_on_status
					}
					{ 'off' == options.automatic_plugin_updates &&
						mpsum.I18N.automatic_plugin_updates_off_status
					}
					{ 'individual' == options.automatic_plugin_updates &&
						mpsum.I18N.automatic_plugin_updates_individual_status
					}
				</p>
				{ ! this.state.loading &&
					<Fragment>
						<div class="toggle-wrapper">
							<button
								data-id="automatic-plugin-updates-default"
								className={`eum-toggle-button ${'default' == options.automatic_plugin_updates ? 'eum-active' : '' }`}
								aria-label={mpsum.I18N.default}
								onClick={this.onButtonClick}
								value="default"
							>
								{mpsum.I18N.default}
							</button>
							<button
								data-id="automatic-plugin-updates-on"
								className={`eum-toggle-button ${'on' == options.automatic_plugin_updates ? 'eum-active' : '' }`}
								aria-label={mpsum.I18N.on}
								onClick={this.onButtonClick}
								value="on"
							>
								{mpsum.I18N.on}
							</button>
							<button
								data-id="automatic-plugin-updates-off"
								className={`eum-toggle-button ${'off' == options.automatic_plugin_updates ? 'eum-active' : '' }`}
								aria-label={mpsum.I18N.off}
								onClick={this.onButtonClick}
								value="off"
							>
								{mpsum.I18N.off}
							</button>
							<button
								data-id="automatic-plugin-updates-individual"
								className={`eum-toggle-button ${'individual' == options.automatic_plugin_updates ? 'eum-active' : '' }`}
								aria-label={mpsum.I18N.select_individually}
								onClick={this.onButtonClick}
								value="individual"
							>
								{mpsum.I18N.select_individually}
							</button>
						</div>
					</Fragment>
				}
				{ this.state.loading &&
					<LoadingGif />
				}
			</div>
		);
	}
}

function mapStateToProps(state) {
	return {
		options: state.options,
		saveOptions: state.saveOptions
	};
}

export default connect( mapStateToProps, { saveOptions } )(AutomaticUpdatesPlugins);
