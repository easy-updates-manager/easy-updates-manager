import React, { Component } from 'react';
import PropTypes from 'prop-types';

class LoadingGif extends Component {

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
	src: PropTypes.string.isRequired
};
LoadingGif.defaultProps = {
	src: mpsum.spinner
};
export default LoadingGif;
