import React from 'react';
import {render} from 'react-dom';
import ToggleItemInput from './toggleiteminput.jsx';

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
		var tabs = [];
		var tabContent = [];
		for( var tab of this.props.tabs ) {
			var className = 'dashboard-tab-header-plugin dashboard-tab-item ' +  (this.state.active == tab.id ? 'active' : '' );
			tabs.push(
				<div className={className} key={tab.id}>
					<a href="#" onClick={this.tabClicked} id={tab.id}>{tab.label}</a>
				</div>
			);
			var tabItemClassName = 'dashboard-tab-content ' + (this.state.active == tab.id ? 'active' : null );
			var tabItems = [];
			for( var asset of tab.items ) {
				tabItems.push(
					<ToggleItemInput 
						id={asset.id}
						title={asset.title} 
						name={asset.id + '_' + asset.context}
						key={asset.id + '_' + asset.context} 
						checked={asset.checked}
						disabled={asset.disabled}
						context={tab.context}
						loading={tab.loading}
					/>
				)
			}
			tabContent.push(
				<div className={tabItemClassName} key={tab.id}>
					{tabItems}
				</div>	
			);
		}
		return (
			<div className='dashboard-tab'>
				{tabs}
				{tabContent}
			</div>
		);
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