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

	onInputChange = ( event ) => {
		event.preventDefault();

		this.setState( {
			loading: true
		} );

		this.props.saveOptions( event.target.id, event.target.value );
	}

	render() {
		const { options } = this.props;
		return (
			<div className="eum-radio-group">
				<h2>{mpsum.I18N.automatic_theme_updates}</h2>
				{ ! this.state.loading &&
					<Fragment>
						<p className="eum-description">{mpsum.I18N.automatic_theme_updates_description}</p>
						<ul>
							<li>
								<input type="radio" value="default" id="automatic-theme-updates-default" checked={ 'default' == options.automatic_theme_updates } onChange={this.onInputChange} /> <label htmlFor="automatic-theme-updates-default">{mpsum.I18N.default}</label>
							</li>
							<li>
								<input type="radio" value="on" id="automatic-theme-updates-on" checked={ 'on' == options.automatic_theme_updates }  onChange={this.onInputChange} /> <label htmlFor="automatic-theme-updates-on">{mpsum.I18N.on}</label>
							</li>
							<li>
								<input type="radio" value="off" id="automatic-theme-updates-off" checked={ 'off' == options.automatic_theme_updates } onChange={this.onInputChange} /> <label htmlFor="automatic-theme-updates-off">{mpsum.I18N.off}</label>
							</li>
							<li>
								<input type="radio" value="individual" id="automatic-theme-updates-individual" checked={ 'individual' == options.automatic_theme_updates } onChange={this.onInputChange} /> <label htmlFor="automatic-theme-updates-individual">{mpsum.I18N.select_individually}</label>
							</li>
						</ul>
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
