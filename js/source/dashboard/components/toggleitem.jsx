import React from 'react';
import {render} from 'react-dom';
import EUM from './main.jsx';
import EUMActions from '../data/EUMActions.jsx';
import ToggleItemInput from './toggleiteminput.jsx';

class ToggleItem extends React.Component {
	
	constructor(props) {
		super(props);
		this.state = {
			checked     : this.props.checked,
			disabled    : this.props.disabled,
			label       : mpsum.enabled,
			loading     : false
		};
	}
	componentWillReceiveProps(newprops) {
		this.setState({
			loading:newprops.loading,
			checked:newprops.checked,
			disabled:newprops.disabled
		});
	}
	render() {
		return (
			<div>
				<ToggleItemInput
					id={this.props.id}
					name={this.props.name}
					onChange={this.itemChange}
					checked={this.state.checked}
					disabled={this.state.disabled}
					context={this.props.context}
					title={this.props.title}
				/>		
			</div>	
		);
	}
}
ToggleItem.propTypes = {
	id: React.PropTypes.string,
	checked: React.PropTypes.bool.isRequired,
	title: React.PropTypes.string.isRequired,
	disabled: React.PropTypes.bool.isRequired,
	name: React.PropTypes.string.isRequired,
	context: React.PropTypes.string.isRequired,
	loading: React.PropTypes.bool.isRequired
};
ToggleItem.defaultProps = {
	id: '',
	checked: false,
	title: '',
	disabled: false,
	name: '',
	context: '',
	loading: false
};
export default ToggleItem;