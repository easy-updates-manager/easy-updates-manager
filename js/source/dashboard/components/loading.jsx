import React from 'react';
import {render} from 'react-dom';

class LoadingGif extends React.Component {
	
	constructor(props) {
		super(props);
	}
	render() {
		return (
			<div className="mpsum-spinner">
				<img src={this.props.src} />	
			</div>	
		);
	}
}
LoadingGif.propTypes = {
	src: React.PropTypes.string.isRequired,
};
LoadingGif.defaultProps = {
	src: mpsum.spinner
};
export default LoadingGif;