import React, { Component, Fragment } from 'react';
import LoadingGif from '../components/loading';
import { saveOptions } from '../actions/save_options';
import { connect } from 'react-redux';

class DisableUpdates extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			loading: false,
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
			<div className="eum-section">
				<h3>{mpsum.I18N.disable_updates}</h3>
				<p className="eum-description">
					{mpsum.I18N.disable_updates_description}
				</p>
				<p className="eum-status">{'on' == options.all_updates ? mpsum.I18N.disable_updates_label_on_status : mpsum.I18N.disable_updates_label_off_status }</p>
				{ ! this.state.loading &&
					<div className="toggle-wrapper">
						<button
							data-id="disable-updates"
							className={`eum-toggle-button ${'on' == options.all_updates ? 'eum-active' : '' }`}
							aria-label={mpsum.I18N.disable_updates_label_on}
							onClick={this.onButtonClick}
							value="on"
						>
							{mpsum.I18N.disable_updates_label_on}
						</button>
						<button
							data-id="disable-updates"
							className={`eum-toggle-button ${'off' == options.all_updates ? 'eum-active' : '' }`}
							aria-label={mpsum.I18N.disable_updates_label_off}
							onClick={this.onButtonClick}
							value="off"
						>
						{mpsum.I18N.disable_updates_label_off}
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

export default connect( mapStateToProps, { saveOptions } )(DisableUpdates);
