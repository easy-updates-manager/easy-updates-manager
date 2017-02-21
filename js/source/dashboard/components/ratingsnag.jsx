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
	}
	display() {
		if ( false === this.state.disabled ) {
			return (
				<div className="eum-ratings-nag mpsum-notice">
					<p>{mpsum.ratings_nag.text}</p>	
					<ul>
						<li><a target="_new" href={mpsum.ratings_nag.url}>{mpsum.ratings_nag.affirm}</a></li>
						<li><a href="#" onClick={this.disabled}>{mpsum.ratings_nag.cancel}</a></li>
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