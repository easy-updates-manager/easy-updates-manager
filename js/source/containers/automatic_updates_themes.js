import React, { Component, Fragment } from 'react';
import LoadingGif from '../components/loading';
import { saveOptions } from '../actions/save_options';
import { connect } from 'react-redux';

class AutomaticUpdatesThemes extends Component {
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
				<h2>{mpsum.I18N.automatic_theme_updates}</h2>
				{ ! this.state.loading &&
					<Fragment>
						<p className="eum-description">{mpsum.I18N.automatic_theme_updates_description}</p>
							<div class="toggle-wrapper">
								<button
									data-id="automatic-theme-updates-default"
									className={`eum-toggle-button ${'default' == options.automatic_theme_updates ? 'eum-active' : '' }`}
									aria-label={mpsum.I18N.default}
									onClick={this.onButtonClick}
									value="default"
								>
									{mpsum.I18N.default}
								</button>
								<button
									data-id="automatic-theme-updates-on"
									className={`eum-toggle-button ${'on' == options.automatic_theme_updates ? 'eum-active' : '' }`}
									aria-label={mpsum.I18N.on}
									onClick={this.onButtonClick}
									value="on"
								>
									{mpsum.I18N.on}
								</button>
								<button
									data-id="automatic-theme-updates-off"
									className={`eum-toggle-button ${'off' == options.automatic_theme_updates ? 'eum-active' : '' }`}
									aria-label={mpsum.I18N.off}
									onClick={this.onButtonClick}
									value="off"
								>
									{mpsum.I18N.off}
								</button>
								<button
									data-id="automatic-theme-updates-individual"
									className={`eum-toggle-button ${'individual' == options.automatic_theme_updates ? 'eum-active' : '' }`}
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

export default connect( mapStateToProps, { saveOptions } )(AutomaticUpdatesThemes);
