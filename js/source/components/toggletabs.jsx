import React from 'react';
import {render} from 'react-dom';
import ToggleItem from './toggleitem.jsx';

class ToggleTabs extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			tabs     : this.props.tabs,
			active   : this.props.active
		};
		this.tabClicked = this.tabClicked.bind(this);
	}
	tabClicked( e ) {
		e.preventDefault();
		this.setState({active:e.target.id});
	}
	showTabs() {
		return;
		var tabs = [];
		var tabItems = [];
		for( var tab of this.props.tabs ) {
			var className = 'dashboard-tab-header-plugin dashboard-tab-item ' +  (this.state.active == tab.id ? 'active' : null );
			tabs.push(
				<div className={className} key={tab.id}>
					<a href="#" onClick={this.tabClicked} id={tab.id}>{tab.label}</a>
				</div>
			);
			var tabItemClassName = 'dashboard-tab-content' + (this.state.active == tab.id ? 'active' : null );
			tabItems.push(
				<div>
				for( var asset of tab.items ) {
					<ToggleItem 
						title={asset.title} 
						name={asset.name} 
						checked={item.checked}
						disabled={item.disabled}
						context={item.context}
						loading={item.loading}
					/>
				}
				</div>
			)
			for( var asset of tab.items ) {
				tabItems.push(
					
				)
			}
		}
		return (
			<div className='dashboard-tab'>
				{tabs}
			</div>
		);tabs;
	}
	render() {
		return (
			<div>
				{this.showTabs()}
			</div>
		);
	}
}
export default ToggleTabs;