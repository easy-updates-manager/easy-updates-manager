import React, { Component, Fragment } from 'react';

export default class AutomaticUpdates extends Component {
	constructor( props ) {
		super( props );
	}
	render() {
		const { options } = this.props;
		return (
			<div className="eum-section">
				<h2>{mpsum.I18N.automatic_updates}</h2>
				<ul>
					<li>
						<input type="radio" value="default" id="automatic-updates-default" /> <label htmlFor="automatic-updates-default">{mpsum.I18N.default}</label>
					</li>
					<li>
						<input type="radio" value="on" id="automatic-updates-on" /> <label htmlFor="automatic-updates-on">{mpsum.I18N.on}</label>
					</li>
					<li>
						<input type="radio" value="off" id="automatic-updates-off" /> <label htmlFor="automatic-updates-off">{mpsum.I18N.off}</label>
					</li>
					<li>
						<input type="radio" value="custom" id="automatic-updates-custom" /> <label htmlFor="automatic-updates-custom">{mpsum.I18N.custom}</label>
					</li>
				</ul>
			</div>
		);
	}
}
