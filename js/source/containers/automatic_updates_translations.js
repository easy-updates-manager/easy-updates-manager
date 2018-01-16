import React, { Component, Fragment } from 'react';
import LoadingGif from '../components/loading';
import { saveOptions } from '../actions/save_options';
import { connect } from 'react-redux';

class AutomaticUpdatesTranslations extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			loading: false,
			checked: 'off'
		};
	}

	componentWillReceiveProps() {
		this.setState( {
			loading: false,
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
			<div className="automatic-updates-custom">
				<h3>{mpsum.I18N.translation_releases}</h3>
				<p className="eum-description">
					{mpsum.I18N.translation_releases_description}
				</p>
				{ ! this.state.loading &&
					<div className="toggle-wrapper">
						<button
							data-id="automatic-translation-updates"
							className={`eum-toggle-button ${'on' == options.automatic_translation_updates ? 'eum-active' : '' }`}
							aria-label={mpsum.I18N.translation_releases_label_on}
							onClick={this.onButtonClick}
							value="on"
						>
							{mpsum.I18N.translation_releases_label_on}
						</button>
						<button
							data-id="automatic-translation-updates"
							className={`eum-toggle-button ${'off' == options.automatic_translation_updates ? 'eum-active' : '' }`}
							aria-label={mpsum.I18N.translation_releases_label_off}
							onClick={this.onButtonClick}
							value="off"
						>
						{mpsum.I18N.translation_releases_label_off}
						</button>
					</div>
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

export default connect( mapStateToProps, { saveOptions } )(AutomaticUpdatesTranslations);
