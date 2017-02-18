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
		var tabs = [];
		for( var tab of this.props.tabs ) {
			var className = 'dashboard-tab-header-plugin dashboard-tab-item ' +  (this.state.active == tab.id ? 'active' : null );
			tabs.push(
				<div className={className} key={tab.id}>
					<a href="#" onClick={this.tabClicked} id={tab.id}>{tab.label}</a>
				</div>
			);
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