import React, { Component, Fragment } from 'react';
import LoadingGif from '../components/loading';
import { saveOptions } from '../actions/save_options';
import { connect } from 'react-redux';

class DisableUpdates extends Component {
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
			<div className="eum-section">
				<h3>{mpsum.I18N.disable_updates}</h3>
				<p className="eum-description">
					{mpsum.I18N.disable_updates_description}
				</p>
				{ ! this.state.loading &&
					<div className="toggle-wrapper">
						<label
							htmlFor="disable-updates"
							className="eum-toggle-label"
							aria-label={'on' == options.all_updates ? mpsum.I18N.disable_updates_label_off : mpsum.I18N.disable_updates_label_on }
						>
							<input
								type="checkbox"
								value={ 'on' == options.all_updates ? 'off' : 'on' }
								id="disable-updates"
								checked={ 'on' == options.all_updates ? 'checked' : false }
								className="eum-toggle eum-hidden"
								onChange={this.onInputChange}
							/>
							<span className="switch"></span>
							<span className="toggle"></span>
						{'on' == options.all_updates ? mpsum.I18N.disable_updates_label_off : mpsum.I18N.disable_updates_label_on }
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

export default connect( mapStateToProps, { saveOptions } )(DisableUpdates);
