import React from 'react';
import {render} from 'react-dom';

class ToggleItem extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			checked     : this.props.checked,
			itemClasses : this.maybeSetActive(this.props.checked),
			disabled    : this.props.disabled,
			label       : mpsum.enabled
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
		if ( this.state.checked ) {
			this.setState({
				checked     : false,
				label       : mpsum.disabled,
				itemClasses : this.maybeSetActive(false)
			});
		} else {
			this.setState({
				checked     : true,
				label       : mpsum.enabled,
				itemClasses : this.maybeSetActive(true)});
		}
		
		//todo - Ajax call
		//re-render main component

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
						<label htmlFor={this.props.name}>
							{this.state.label}
						</label>
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
	context: React.PropTypes.string.isRequired
};
ToggleItem.defaultProps = {
	checked: false,
	title: '',
	disabled: false,
	name: '',
	context: ''
};
export default ToggleItem;