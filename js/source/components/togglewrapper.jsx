import React from 'react';
import {render} from 'react-dom';
import ToggleItem from './toggleitem.jsx';
import ToggleItemRadio from './toggleitemradio.jsx';
import ToggleTabs from './toggletabs.jsx';

class ToggleWrapper extends React.Component {
	constructor(props) {
		super(props);
	}
	createItems() {
		var items = [];
		for( var item of this.props.items ) {
			if ( 'ToggleItem' == item.component ) {
				items.push( this.createToggleComponent( item ) );
			} else if( 'ToggleItemRadio' == item.component ) {
				items.push( this.createToggleRadioComponent( item ) );
			} else if( 'ToggleTabs' == item.component ) {
				items.push( this.createToggleTabsComponent (item ) );
			}
		}
		return items;
	}
	createToggleTabsComponent( item ) {
		return (
			<div key={item.id}>
				<ToggleTabs
					tabs={item.tabs}
					active={item.active}
				/>
			</div>
		)
	}
	createToggleComponent( item ) {
		return (
			<div key={item.name}>
				<ToggleItem 
					title={item.title} 
					name={item.name} 
					checked={item.checked}
					disabled={item.disabled}
					context={item.context}
					loading={item.loading}
				/>
			</div>
		);
	}
	createToggleRadioComponent( item ) {
		return (
			<div key={item.name}>
				<ToggleItemRadio 
					title={item.title} 
					name={item.name} 
					checked={item.checked}
					disabled={item.disabled}
					context={item.context}
					update={this.props.update}
					loading={item.loading}
					choices={item.choices}
				/>
			</div>
		);
	}
	render() {
		return (
			<div className="dashboard-main-wrapper">
				<div className="dashboard-main-header">{this.props.title}</div>
				<div className="dashboard-item-wrapper">
					{this.createItems()}
				</div>
			</div>
		);
	}
}
ToggleWrapper.propTypes = {
	title: React.PropTypes.string.isRequired,
};
ToggleWrapper.defaultProps = {
	title: '',
};
export default ToggleWrapper;