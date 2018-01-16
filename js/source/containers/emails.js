import React, { Component, Fragment } from 'react';
import LoadingGif from '../components/loading';
import { saveOptions } from '../actions/save_options';
import { connect } from 'react-redux';

class Emails extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			loading: false,
			checked: 'off',
			emails: props.options.email_addresses,
			errors: false,
			saving: false,
		};
	}

	componentWillReceiveProps( newProps ) {
		this.setState( {
			loading: false,
			saving: false,
			emails: newProps.options.email_addresses,
			errors: newProps.options.errors,
		} );
		setTimeout( () => {
			this.setState( {
				errors: false
			})
		}, 3000 );
	}

	onButtonClick = ( event ) => {
		event.preventDefault();
		this.setState( {
			loading: true,
		} );

		this.props.saveOptions( event.target.getAttribute ( 'data-id' ), event.target.value );
	}

	onInputChangeEmails = ( event ) => {
		event.preventDefault();
		this.setState( {
			emails: event.target.value
		} )
	}

	handleEmailSave = ( event ) => {
		this.setState( {
			saving: true
		} );

		let emails = '';

		if ( ! this.state.emails ) {
			emails = 'unset';
		} else {
			emails = this.state.emails;
		}

		this.props.saveOptions( 'notification-emails', emails );

	}

	render() {
		const { options } = this.props;
		return (
			<div className="eum-section">
				<h3>{mpsum.I18N.emails}</h3>
				<p className="eum-description">
					{mpsum.I18N.emails_description}
				</p>
				{ ! this.state.loading &&
					<div className="toggle-wrapper">
						<button
							data-id="email-notifications"
							className={`eum-toggle-button ${'on' == options.notification_core_update_emails ? 'eum-active' : '' }`}
							aria-label={mpsum.I18N.emails_label_on}
							onClick={this.onButtonClick}
							value="on"
						>
							{mpsum.I18N.emails_label_on}
						</button>
						<button
							data-id="email-notifications"
							className={`eum-toggle-button ${'off' == options.notification_core_update_emails ? 'eum-active' : '' }`}
							aria-label={mpsum.I18N.emails_label_off}
							onClick={this.onButtonClick}
							value="off"
						>
						{mpsum.I18N.emails_label_off}
						</button>
					</div>
				}
				{ this.state.loading &&
					<LoadingGif />
				}
				<Fragment>
					<p><label htmlFor="notification-emails" className="eum-input-label">
						{mpsum.I18N.emails_input_label}
					</label></p>
					<input
						id="notification-emails"
						className="eum-input-email"
						type="email"
						placeholder={mpsum.I18N.emails_placeholder}
						onChange={this.onInputChangeEmails}
						value={this.state.emails}
					/>
				</Fragment>
				<div>
					<button
						disabled={this.state.saving ? true : false } className="eum-save button button-primary"
						onClick={this.handleEmailSave}
					>
						{this.state.saving ? mpsum.I18N.emails_saving : mpsum.I18N.emails_save}
					</button>
				</div>
				{ this.state.errors &&
					<Fragment>
						<div className="mpsum-error">
							<p>{mpsum.I18N.emails_invalid}</p>
						</div>
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

export default connect( mapStateToProps, { saveOptions } )(Emails);
