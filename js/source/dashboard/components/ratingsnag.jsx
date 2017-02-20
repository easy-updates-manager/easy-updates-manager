import React from 'react';
import {render} from 'react-dom';

class RatingsNag extends React.Component {
	
	constructor(props) {
		super(props);
	}
	render() {
		return (
			<div className="eum-ratings-nag mpsum-notice">
				<p>{mpsum.ratings_nag.text}</p>	
				<ul>
					<li><a target="_new" href={mpsum.ratings_nag.url}>{mpsum.ratings_nag.affirm}</a></li>
					<li><a href="#">{mpsum.ratings_nag.cancel}</a></li>
				</ul>
			</div>	
		);
	}
}
export default RatingsNag;