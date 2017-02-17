import React from 'react';
import {render} from 'react-dom';
import ToggleItem from './toggleitem.jsx';

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
					context={item.context}
					update={this.props.update}
					loading={false}
				/>
			</div>
		);
	}
	render() {
		return (
			<div className="dashboard-main-wrapper">
				<div className="dashboard-main-header">{this.props.title}</div>
					{this.createItems()}
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