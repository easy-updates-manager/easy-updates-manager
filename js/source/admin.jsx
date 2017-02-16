import React from 'react';
import {render} from 'react-dom';
import ToggleItem from './components/toggleitem.jsx';
import ToggleWrapper from './components/togglewrapper.jsx';






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
