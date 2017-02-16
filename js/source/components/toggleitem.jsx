import React from 'react';
import {render} from 'react-dom';
import LoadingGif from './loading.jsx';

class ToggleItem extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			checked     : this.props.checked,
			itemClasses : this.maybeSetActive(this.props.checked),
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
	itemChange(event) {
		this.setState({
			loading: true
		});
		
		// todo Ajax Call
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
		
		this.setState({
			loading: false
		});
		//todo - Ajax call
		//re-render main component

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
	render() {
		return (
			<div>
				<div className={this.state.itemClasses}>
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
							checked={this.state.checked}
							disabled={this.state.disabled} 
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