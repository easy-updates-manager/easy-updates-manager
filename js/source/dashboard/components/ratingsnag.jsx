import React from 'react';
import {render} from 'react-dom';

class RatingsNag extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			disabled: !mpsum.ratings_nag.enabled
		}
		this.disabled = this.disabled.bind( this );
	}
	disabled(e) {
		e.preventDefault();
		this.setState({disabled:true});
		let xhr = new XMLHttpRequest();
		xhr.open( 'POST', ajaxurl );
		xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
		xhr.send(
			'action=mpsum_ajax_remove_ratings_nag' +
			'&_ajax_nonce=' + mpsum.admin_nonce
		);
	}
	display() {
		if ( false === this.state.disabled ) {
			return (
				<div className="eum-ratings-nag mpsum-notice">
					<p><strong>{mpsum.ratings_nag.text}</strong></p>	
					<ul>
						<li><a className="button button-primary" target="_new" href={mpsum.ratings_nag.url}>{mpsum.ratings_nag.affirm}</a></li>
						<li><a className="button button-secondary" href="#" onClick={this.disabled}>{mpsum.ratings_nag.cancel}</a></li>
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
export default RatingsNag;