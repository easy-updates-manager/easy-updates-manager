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

	onInputChange = ( event ) => {
		event.preventDefault();
		let checked = 'off';
		if ( event.target.value == 'on' ) {
			checked = 'off';
		} else {
			checked = 'on';
		}
		this.setState( {
			loading: true,
			checked: checked
		} );

		this.props.saveOptions( event.target.id, event.target.value );
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
						<label
							htmlFor="automatic-translation-updates"
							className="eum-toggle-label"
							aria-label={mpsum.I18N.translation_releases_label}
						>
							<input
								type="checkbox"
								value={ 'on' == options.automatic_translation_updates ? 'off' : 'on' }
								id="automatic-translation-updates"
								checked={ 'on' == options.automatic_translation_updates ? 'checked' : false }
								className="eum-toggle eum-hidden"
								onChange={this.onInputChange}
							/>
							<span className="switch"></span>
							<span className="toggle"></span>
						{mpsum.I18N.translation_releases_label}
						</label>
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
