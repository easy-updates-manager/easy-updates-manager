import React, { Component, Fragment } from 'react';
import LoadingGif from '../components/loading';

export default class AutomaticUpdatesMajor extends Component {
	constructor( props ) {
		super( props );

		this.state = {
			loading: false,
			checked: 'off'
		};
	}

	componentWillReceiveProps() {
		this.setState( {
			loading: false,
		} );
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
						<input
							type="checkbox"
							value={ 'on' == options.automatic_major_updates ? 'off' : 'on' } id="automatic-major-updates"
							checked={ 'on' == options.automatic_major_updates ? 'checked' : false }
							className="eum-toggle"
							aria-label={mpsum.I18N.major_releases_label}
							onChange={this.onInputChange}
						/>
							<label
								htmlFor="automatic-major-updates"
								className="screen-reader-text">{mpsum.I18N.major_releases_label}
							</label>
					</Fragment>
				}
				{ this.state.loading &&
					<LoadingGif />
				}
			</div>
		);
	}
}
