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



class ToggleWrapper extends React.Component {
	constructor(props) {
		super(props);
	}
	createItems() {
		var items = [];
		for( var item of this.props.items ) {
			if ( 'ToggleItem' == item.component ) {
				items.push( this.createToggleComponent( item ) );
			}
		}
		return items;
	}
	createToggleComponent( item ) {
		return (
			<div className="dashboard-item-wrapper" key={item.name}>
				<ToggleItem 
					title={item.title} 
					name={item.name} 
					checked={item.checked}
					disabled={item.disabled} 
				/>
			</div>
		);
	}
	render() {
		return (
			<div>
				<div className="dashboard-main-wrapper">
					<div className="dashboard-main-header">{this.props.title}</div>
					{this.createItems()}
				</div>
			</div>
		);
	}
}


class App extends React.Component {
	
	
	createWrapper( title, items ) {
		return <ToggleWrapper class="" title={title} items={items} key={title} />
	}
	createWrappers( data ) {
		var wrappers = [];
		for( var value of data ) {
			wrappers.push( this.createWrapper( value.title, value.items ) );
		}
		return wrappers;
		
	}
	render() {
		return (
			<div>
				{this.createWrappers(mpsum.json_options)}
			</div>	
		);
	}
}
render(
	<App />, 
	document.getElementById('eum-dashboard-app')
);
