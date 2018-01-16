import React, { Component, Fragment } from 'react';
import LoadingGif from '../components/loading';
import { saveOptions } from '../actions/save_options';
import { connect } from 'react-redux';

class Logs extends Component {
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
			<div className="eum-section">
				<h3>{mpsum.I18N.logs}</h3>
				<p className="eum-description">
					{mpsum.I18N.logs_description}
				</p>
				{ ! this.state.loading &&
					<div className="toggle-wrapper">
						<button
							data-id="logs"
							className={`eum-toggle-button ${'on' == options.logs ? 'eum-active' : '' }`}
							aria-label={mpsum.I18N.logs_label_on}
							onClick={this.onButtonClick}
							value="on"
						>
							{mpsum.I18N.logs_label_on}
						</button>
						<button
							data-id="logs"
							className={`eum-toggle-button ${'off' == options.logs ? 'eum-active' : '' }`}
							aria-label={mpsum.I18N.logs_label_off}
							onClick={this.onButtonClick}
							value="off"
						>
						{mpsum.I18N.logs_label_off}
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

export default connect( mapStateToProps, { saveOptions } )(Logs);
