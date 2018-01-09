import React, { Component, Fragment } from 'react';
import LoadingGif from '../components/loading';

export default class AutomaticUpdatesMajor extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			loading: false
		};
	}

	componentWillReceiveProps() {
		this.setState( {
			loading: false
		} );
	}

	onInputChange = ( event ) => {
		event.preventDefault();

		this.setState( {
			loading: true
		} );

		this.props.saveOptions( event.target.id, event.target.value );
	}

	render() {
		const { options } = this.props;
		return (
			<div className="automatic-updates-custom">
				<h3>{mpsum.I18N.major_releases}</h3>
				<p className="eum-description">
					{mpsum.I18N.major_releases_description}
				</p>
				{ ! this.state.loading &&
					<Fragment>
						<input type="checkbox" value="on" id="automatic-updates-major" checked={'off' == this.props.automatic_major_updates} className="eum-toggle" aria-label={mpsum.I18N.major_releases_label} /> <label htmlFor="automatic-updates-major" className="screen-reader-text">{mpsum.I18N.major_releases_label}</label>
					</Fragment>
				}
				{ this.state.loading &&
					<LoadingGif />
				}
			</div>
		);
	}
}
