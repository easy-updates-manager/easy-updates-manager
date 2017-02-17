import React from 'react';
import {render} from 'react-dom';
import LoadingGif from './loading.jsx';
import EUM from '../admin.jsx';

class ToggleItem extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			checked     : this.props.checked,
			itemClasses : this.maybeActiveItem(),
			disabled    : this.props.disabled,
			label       : mpsum.enabled,
			loading     : false
		};
		this.itemChange = this.itemChange.bind(this);
	}
	maybeSetActive(checked) {
		if(checked) {
			return 'dashboard-item active';
		} else {
			return 'dashboard-item';
		}
	}
	maybeActiveItem() {
		return this.maybeSetActive(this.props.checked);
	}
	itemChange(event) {
		this.setState({
			loading: true
		});
		
		let xhr = new XMLHttpRequest();
		xhr.open( 'POST', ajaxurl );
		xhr.setRequestHeader( 'Content-Type', 'application/x-www-form-urlencoded' );
		xhr.onload = function() {
			if ( xhr.status === 200 ) {
				let json = JSON.parse( xhr.response );
				if ( this.state.checked ) {
					this.setState({
						checked     : false,
						label       : mpsum.disabled,
						itemClasses : this.maybeSetActive(false),
						loading     : false
					});
				} else {
					this.setState({
						checked     : true,
						label       : mpsum.enabled,
						itemClasses : this.maybeSetActive(true),
						loading     : false
					});
				}
				this.props.update(json);
			}	
		};
		xhr.onload = xhr.onload.bind(this);
		xhr.send(
			'action=mpsum_ajax_action' +
			'&_ajax_nonce=' + mpsum.admin_nonce +
			'&context=' + this.props.context +
			'&data_action=' + this.props.name +
			'&value=' +  ( this.state.checked ? 'off' : 'on' )
		);
	}
	getLabel() {
		if ( this.state.loading ) {
			return(
				<LoadingGif />
			)	
		}
		return (
			<label htmlFor={this.props.name}>
				{this.state.label}
			</label>	
		);
	}
	componentDidUpdate() {
		if ( this.state.checked !== this.props.checked ) {
			this.setState({
				checked: this.props.checked,
			});
		}
		if ( this.state.disabled !== this.props.disabled ) {
			this.setState({
				disabled: this.props.disabled
			});
		}
		
	}
	checkedChange() {
		if ( this.props.checked !== this.state.checked ) {
			this.state.checked = this.props.checked;
		}
		return this.state.checked;
	}
	disabledChange() {
		if ( this.props.disabled !== this.state.disabled ) {
			this.state.disabled = this.props.disabled;
		}
		return this.state.disabled;
	}
	render() {
		return (
			<div>
				<div className={this.maybeActiveItem()}>
					<div className="dashboard-item-header input-radio">
						{this.props.title}	
					</div>		
					<div className="dashboard-item-choice">
						<input type="hidden" name={this.props.name} value="off" />
						<input
							id={this.props.name}
							type="checkbox" 
							className="dashboard-hide" 
							name={this.props.name} 
							value="on"
							onChange={this.itemChange}
							checked={this.checkedChange()}
							disabled={this.disabledChange()}
						/>
						{this.getLabel()}
					</div>
				</div>	
			</div>	
		);
	}
}
ToggleItem.propTypes = {
	checked: React.PropTypes.bool.isRequired,
	title: React.PropTypes.string.isRequired,
	disabled: React.PropTypes.bool.isRequired,
	name: React.PropTypes.string.isRequired,
	context: React.PropTypes.string.isRequired,
	loading: React.PropTypes.bool.isRequired
};
ToggleItem.defaultProps = {
	checked: false,
	title: '',
	disabled: false,
	name: '',
	context: '',
	loading: false
};
export default ToggleItem;