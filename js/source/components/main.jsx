import React from 'react';
import {render} from 'react-dom';
import ToggleWrapper from './togglewrapper.jsx';

let EUM = class App extends React.Component {
	constructor(props) {
		super(props);
		this.state = {
			options     : this.props.options,
		};
		this.update = this.update.bind(this);
	}
	update( json ) {
		this.setState({options:json});
	}
	createWrapper( title, items ) {
		return <ToggleWrapper class="" title={title} items={items} key={title} update={this.update} />
	}
	createWrappers( data ) {
		let wrappers = [];
		for( var value of data ) {
			wrappers.push( this.createWrapper( value.title, value.items ) );
		}
		return wrappers;
		
	}
	render() {
		return (
			<div>
				{this.createWrappers(this.state.options)}
			</div>	
		);
	}
}

export default EUM;