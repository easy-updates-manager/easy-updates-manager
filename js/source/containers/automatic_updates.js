import React, { Component, Fragment } from 'react';

export default class AutomaticUpdates extends Component {
	constructor( props ) {
		super( props );
	}
	render() {
		const { options } = this.props;
		return (
			<Fragment>
				{console.log( options )}
				<h2>{mpsum.I18N.automatic_updates}</h2>
			</Fragment>
		);
	}
}
