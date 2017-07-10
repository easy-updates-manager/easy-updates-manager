import React from 'react';
import {render} from 'react-dom';

class TrackingNag extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			disabled: 'off' === mpsum.tracking_nag.enabled ? true : false
		}
		this.disabled = this.disabled.bind( this );
		this.enabled = this.enabled.bind( this );
		console.log( mpsum );
	}
	disabled(e) {
		e.preventDefault();
		this.setState({disabled:true});
		let xhr = new XMLHttpRequest();
		xhr.open( 'POST', ajaxurl );
		xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
		xhr.send(
			'action=mpsum_ajax_remove_tracking_nag' +
			'&_ajax_nonce=' + mpsum.admin_nonce
		);
	}
	enabled(e) {
		e.preventDefault();
		this.setState({disabled:true});
		let xhr = new XMLHttpRequest();
		xhr.open( 'POST', ajaxurl );
		xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
		xhr.send(
			'action=mpsum_ajax_enable_tracking' +
			'&_ajax_nonce=' + mpsum.admin_nonce
		);
	}
	display() {
		if ( false === this.state.disabled ) {
			return (
				<div className="eum-tracking-nag mpsum-notice">
					<p>
						<strong>{mpsum.tracking_nag.text}</strong>
						&nbsp;
						<a target="_blank" href={mpsum.tracking_nag.url}>{mpsum.tracking_nag.help}</a>
					</p>	
					<ul>
						<li><button className="button button-primary" onClick={this.enabled}>{mpsum.tracking_nag.affirm}</button></li>
						<li><button className="button button-secondary" onClick={this.disabled}>{mpsum.tracking_nag.cancel}</button></li>
					</ul>
				</div>
			)
		}
		return;
	}
	render() {
		return (
			<div>
				{this.display()}	
			</div>
		);
	}
}
export default TrackingNag;