import React from 'react';
import {render} from 'react-dom';
class ToggleItem extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			checked     : this.props.checked,
			itemClasses : this.maybeSetActive(this.props.checked),
			disabled    : this.props.disabled 	
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
			this.setState({checked:false,itemClasses:this.maybeSetActive(false)});
		} else {
			this.setState({checked:true,itemClasses:this.maybeSetActive(true)});
		}

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
							disabled={this.props.disabled} 
						/>
						<label htmlFor={this.props.name}>
							Enabled
						</label>
					</div>
				</div>	
			</div>	
		);
	}
}



class ToggleWrapper extends React.Component {
	constructor(props) {
		super(props);
	}
	render() {
		return (
			<div>
				<div className="dashboard-main-wrapper">
					<div className="dashboard-main-header">{this.props.title}</div>
					
					<div className="dashboard-item-wrapper">
						<ToggleItem 
							title="All Updates" 
							name="all_updates" 
							checked={false}
							disabled={false} 
						/>
					</div>
				</div>
			</div>
		);
	}
}





class App extends React.Component {
	render() {
		return (
			<div>
				<ToggleWrapper class="" title="WordPress Updates" />
			</div>	
		);
	}
}
render(
	<App />, 
	document.getElementById('eum-dashboard-app')
);
