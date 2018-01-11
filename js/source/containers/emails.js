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
			saving: false
		};
	}

	componentWillReceiveProps( newProps ) {
		this.setState( {
			loading: false,
			saving: false,
			emails: newProps.options.email_addresses,
			errors: newProps.options.errors
		} );
		setTimeout( () => {
			this.setState( {
				errors: false
			})
		}, 3000 );
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

	handleEmailAdd = ( email ) => {
		if ( ! isEmail( email ) ) {
			this.setState( {
				errors: true
			} );
			setTimeout( () => {
				this.setState( {
					errors: false
				})
			}, 3000 );
			return;
		}
		let emails = this.state.emails;

		emails.push({
			id: emails.length + 1,
			text: email
		});
		this.setState( {
			emails: emails
		} );
	}

	handleEmailDelete = ( email ) => {
		let emails = this.state.emails;
		emails.splice( email, 1 );
		this.setState( {
			emails: emails
		} );
	}

	handleEmailSave = ( event ) => {
		this.setState( {
			saving: true
		} );

		let email_addresses = [];
		for (var value of this.state.emails) {
			email_addresses.push( toArray(value)[1] );
		}
		let comma_separated_emails = email_addresses.join( ',' );

		if ( '' === comma_separated_emails ) {
			comma_separated_emails = 'remove';
		}

		this.props.saveOptions( 'notification-emails', comma_separated_emails );

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
						<label
							htmlFor="email-notifications"
							className="eum-toggle-label"
							aria-label={mpsum.I18N.emails_label}
						>
							<input
								type="checkbox"
								value={ 'on' == options.notification_core_update_emails ? 'off' : 'on' }
								id="email-notifications"
								checked={ 'on' == options.notification_core_update_emails ? 'checked' : false }
								className="eum-toggle eum-hidden"
								onChange={this.onInputChange}
							/>
							<span className="switch"></span>
							<span className="toggle"></span>
						{mpsum.I18N.emails_label}
						</label>
					</div>
				}
				{ this.state.loading &&
					<LoadingGif />
				}
				<Fragment>
					<input
						className="eum-input-email"
						type="email"
						placeholder={mpsum.I18N.emails_placeholder}
					/>
				</Fragment>
				<Fragment>
					<button
						disabled={this.state.saving ? true : false } className="eum-save button button-primary"
						onClick={this.handleEmailSave}
					>
						{this.state.saving ? mpsum.I18N.emails_saving : mpsum.I18N.emails_save}
					</button>
				</Fragment>
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
