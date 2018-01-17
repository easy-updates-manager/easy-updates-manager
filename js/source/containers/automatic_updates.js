import React, { Component, Fragment } from 'react';
import LoadingGif from '../components/loading';
import AutomaticUpdatesMajor from './automatic_updates_major';
import AutomaticUpdatesMinor from './automatic_updates_minor';
import AutomaticUpdatesDevelopment from './automatic_updates_development';
import AutomaticUpdatesTranslations from './automatic_updates_translations';
import AutomaticUpdatesPlugins from './automatic_updates_plugins';
import AutomaticUpdatesThemes from './automatic_updates_themes';
import { saveOptions } from '../actions/save_options';
import { connect } from 'react-redux';

class AutomaticUpdates extends Component {
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
			<div className="eum-section">
				<h2>{mpsum.I18N.automatic_updates}</h2>
				<p className="eum-description">{mpsum.I18N.automatic_updates_description}</p>
				<p className="eum-status">
					{ 'on' == options.automatic_updates &&
						mpsum.I18N.automatic_updates_on_status
					}
					{ 'off' == options.automatic_updates &&
						mpsum.I18N.automatic_updates_off_status
					}
					{ 'default' == options.automatic_updates &&
						mpsum.I18N.automatic_updates_default_status
					}
					{ 'custom' == options.automatic_updates &&
						mpsum.I18N.automatic_updates_custom_status
					}
				</p>
				{ ! this.state.loading &&
					<Fragment>
						<div class="toggle-wrapper">
							<button
								data-id="automatic-updates-default"
								className={`eum-toggle-button ${'default' == options.automatic_updates ? 'eum-active' : '' }`}
								aria-label={mpsum.I18N.default}
								onClick={this.onButtonClick}
								value="default"
							>
								{mpsum.I18N.default}
							</button>
							<button
								data-id="automatic-updates-on"
								className={`eum-toggle-button ${'on' == options.automatic_updates ? 'eum-active' : '' }`}
								aria-label={mpsum.I18N.on}
								onClick={this.onButtonClick}
								value="on"
							>
								{mpsum.I18N.on}
							</button>
							<button
								data-id="automatic-updates-off"
								className={`eum-toggle-button ${'off' == options.automatic_updates ? 'eum-active' : '' }`}
								aria-label={mpsum.I18N.off}
								onClick={this.onButtonClick}
								value="off"
							>
								{mpsum.I18N.off}
							</button>
							<button
								data-id="automatic-updates-custom"
								className={`eum-toggle-button ${'custom' == options.automatic_updates ? 'eum-active' : '' }`}
								aria-label={mpsum.I18N.off}
								onClick={this.onButtonClick}
								value="custom"
							>
								{mpsum.I18N.custom}
							</button>
						</div>
					</Fragment>
				}
				{ this.state.loading &&
					<LoadingGif />
				}
				{ options.automatic_updates == 'custom' && ! this.state.loading &&
					<Fragment>
						<AutomaticUpdatesMajor />
						<hr />
						<AutomaticUpdatesMinor />
						<hr />
						<AutomaticUpdatesDevelopment />
						<hr />
						<AutomaticUpdatesTranslations />
						<hr />
						<AutomaticUpdatesPlugins />
						<hr />
						<AutomaticUpdatesThemes />
					</Fragment>
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

export default connect( mapStateToProps, { saveOptions } )(AutomaticUpdates);
